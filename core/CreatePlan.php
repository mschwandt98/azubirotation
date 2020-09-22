<?php
use Core\Helper;

include_once(dirname(__DIR__) . "/config.php");
include_once("Helper.php");
include_models();

$helper = new Helper();

$abteilungen        = $helper->GetAbteilungen();
$ansprechpartner    = $helper->GetAnsprechpartner();
$ausbildungsberufe  = $helper->GetAusbildungsberufe();
$azubis             = $helper->GetAzubis();
$standardplaene     = $helper->GetStandardPlaene();

$planung = [];
$lowestStartDate = new DateTime($azubis[0]->Ausbildungsstart);
$highestEndDate = new DateTime($azubis[0]->Ausbildungsende);

// Vorbereitung Counter wieviele Azubis wann in welchen Abteilungen sind
$abteilungenAzubiCounter = [];
foreach ($abteilungen as $abteilung) {
    $abteilungenAzubiCounter[$abteilung->ID] = [];
}

foreach ($azubis as $azubi) {

    foreach ($ausbildungsberufe as $ausbildungsberuf) {

        if ($ausbildungsberuf->ID === $azubi->ID_Ausbildungsberuf ) {
            $beruf = $ausbildungsberuf;
            break;
        }
    }

    if (empty($beruf)) {
        return;
    }

    if (!array_key_exists($beruf->Bezeichnung, $standardplaene)) {
        return;
    }

    $standardplan = $standardplaene[$beruf->Bezeichnung];

    // Sortierung nach Präferierung und nach Optionalität der Phasen
    $praeferien = [];
    $praeferienAndOptional = [];
    $phases = [];
    $optional = [];

    foreach ($standardplan->Phasen as $phase) {

        if ($phase->Praeferieren && !$phase->Optional) {
            $praeferien[] = $phase;
        } elseif ($phase->Praeferieren && $phase->Optional) {
            $praeferienAndOptional[] = $phase;
        } elseif (!$phase->Praeferieren && $phase->Optional) {
            $optional[] = $phase;
        } else {
            $phases[] = $phase;
        }
    }

    $orderedStandardplan = clone $standardplan;
    $orderedStandardplan->Phasen = array_merge($praeferien, $praeferienAndOptional, $phases, $optional);

    // Startdatum und Enddatum jeder Phase setzen
    $startDatum = new DateTime($azubi->Ausbildungsstart);
    $endDatum = new DateTime($azubi->Ausbildungsende);
    $weeks = $startDatum->diff($endDatum)->days / 7;
    $phaseStart = clone $startDatum;

    if ($lowestStartDate > $startDatum) {
        $lowestStartDate = $startDatum;
    }

    if ($highestEndDate < $endDatum) {
        $highestEndDate = $endDatum;
    }

    foreach ($orderedStandardplan->Phasen as $phase) {

        if ($phaseStart == $endDatum) {
            break;
        }

        $phaseEnd = clone GetEndDateOfPhase(clone $phaseStart, $phase->Wochen);

        if ($phaseEnd > $endDatum) {
            $phaseEnd = clone $endDatum;
        }

        $phase->StartDate = clone $phaseStart;
        $phase->EndDate = clone $phaseEnd;

        $phaseStart = clone $phaseEnd;
        SetNextDay($phaseStart);
    }

    unset($phaseStart, $phaseEnd);

    // Erstes Erstellen des Plans
    $planung[$azubi->ID] = new Item($azubi);

    foreach ($orderedStandardplan->Phasen as $key => $phase) {

        foreach ($abteilungen as $abteilung) {
            if ($abteilung->ID == $phase->ID_Abteilung) {
                $currentAbteilung = $abteilung;
                break;
            }
        }

        if (empty($currentAbteilung)) {
            return;
        }

        $planung[$azubi->ID]->Phasen[] = [
            "StartDate"     => $phase->StartDate,
            "EndDate"       => $phase->EndDate,
            "Wochen"        => $phase->Wochen,
            "Praeferieren"  => $phase->Praeferieren,
            "Optional"      => $phase->Optional,
            "ID_Abteilung"  => $phase->ID_Abteilung,
            "Farbe"         => $currentAbteilung->Farbe
        ];
    }
}

$tempStartDate = clone $lowestStartDate;
$tempEndDate = clone $highestEndDate;
$mondayLowestStartDate = clone $tempStartDate->modify("Monday this week");
$sundayHighestEndDate = clone $tempEndDate->modify("Sunday this week");
unset($tempStartDate, $tempEndDate);

$weeksBetweenLowestAndHighestDate = ceil($mondayLowestStartDate->diff($sundayHighestEndDate)->days / 7);
$months = [];
$currentDate = clone $mondayLowestStartDate;

for ($i = 0; $i < $weeksBetweenLowestAndHighestDate; $i++) {
    $months[] = strtoupper(substr($currentDate->format("F"), 0, 3)) . " " . substr($currentDate->format("Y"), -2);
    $currentDate->modify("Monday next week");
}

ob_start();
?>

<table>
<tr>
    <th>Nachname</th>
    <th>Vorname</th>
    <th>Zeitraum</th>

    <?php for ($i = 0; $i < $weeksBetweenLowestAndHighestDate; $i++) : ?>

        <th><?= $months[$i]; ?></th>

    <?php endfor; ?>

</tr>

<?php foreach ($planung as $plan) : ?>
    <?php $currentDate = clone $mondayLowestStartDate; ?>

    <tr>
        <td><?= $plan->Azubi->Nachname; ?></td>
        <td><?= $plan->Azubi->Vorname; ?></td>
        <td>
            <?= date("m.Y", strtotime($plan->Azubi->Ausbildungsstart)) .
                " - " .
                date("m.Y", strtotime($plan->Azubi->Ausbildungsende)); ?>
        </td>

        <?php foreach ($plan->Phasen as $phase) : ?>
            <?php $phaseCurrentDate = $phase["StartDate"]; ?>
            <?php for ($i = 0; $i < $weeksBetweenLowestAndHighestDate; $i++) : ?>
                <?php if ($currentDate > $phase["EndDate"]) : ?>
                    <?php continue; ?>
                <?php elseif ($phaseCurrentDate <= $currentDate) : ?>

                    <td style="background-color: <?= $phase["Farbe"]; ?>;"></td>

                <?php else: ?>

                    <td></td>

                <?php endif; ?>
                <?php $currentDate->modify("Monday next week"); ?>
            <?php endfor; ?>
        <?php endforeach; ?>

    </tr>

<?php endforeach; ?>

</table>

<?php
exit(ob_get_clean());

function GetEndDateOfPhase($startDate, $weeksOfPhase) {
    $interval = new DateInterval("P" . $weeksOfPhase . "W");
    return $startDate->add($interval);
}

function SetNextDay($date) {
    return $date->add(new DateInterval("P1D"));
}

class Item {
    public $Azubi;
    public $Phasen;

    function __construct($azubi) {
        $this->Azubi = $azubi;
        $this->Phasen = [];
    }
}
