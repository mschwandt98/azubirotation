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

    $startDatum = new DateTime($azubi->Ausbildungsstart);
    $endDatum = new DateTime($azubi->Ausbildungsende);
    $weeks = $startDatum->diff($endDatum)->days / 7;
    $weeksLeft = $weeks;
    $phaseStart = clone $startDatum;

    if ($lowestStartDate > $startDatum) {
        $lowestStartDate = $startDatum;
    }

    if ($highestEndDate < $endDatum) {
        $highestEndDate = $endDatum;
    }

    $planung[$azubi->ID] = new Item($azubi);
    $standardplan = $standardplaene[$beruf->Bezeichnung];
    $completedPhases = [];

    foreach ($standardplan->Phasen as $phase) {

        if ($phaseStart == $endDatum) {
            break;
        }

        foreach ($abteilungen as $abteilung) {
            if ($abteilung->ID == $phase->ID_Abteilung) {
                $currentAbteilung = $abteilung;
                break;
            }
        }

        if (empty($currentAbteilung)) {
            return;
        }

        $phaseEnd = clone GetEndDateOfPhase(clone $phaseStart, $phase->Wochen);

        if ($phaseEnd > $endDatum) {
            $phaseEnd = clone $endDatum;
        }

        $planung[$azubi->ID]->Phasen[] = [
            "StartDate" => clone $phaseStart,
            "EndDate" => clone $phaseEnd,
            "Wochen" => $phase->Wochen,
            "ID_Abteilung" => $phase->ID_Abteilung,
            "Farbe" => $currentAbteilung->Farbe
        ];

        $phaseStart = clone $phaseEnd;
        SetNextDay($phaseStart);
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

function DateInWeekOf($date, $dateWeek) {
    return;
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
