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
    $phaseStart = clone($startDatum);

    if ($lowestStartDate > $startDatum) {
        $lowestStartDate = $startDatum;
    }

    if ($highestEndDate < $endDatum) {
        $highestEndDate = $endDatum;
    }

    $standardplan = $standardplaene[$beruf->Bezeichnung];
    $completedPhases = [];

    foreach ($standardplan->Phasen as $phase) {

        if ($phaseStart == $endDatum) {
            break;
        }

        foreach ($abteilungen as $abteilung) {
            if ($abteilung->ID == $phase->ID_Abteilung) {
                $currentAbteilung = $abteilung;
            }
        }

        if (empty($currentAbteilung)) {
            return;
        }

        $phaseEnd = clone(GetEndDateOfPhase(clone($phaseStart), $phase->Wochen));

        if ($phaseEnd > $endDatum) {
            $phaseEnd = clone($endDatum);
        }

        $planung[$azubi->ID][] = [
            "StartDate" => clone($phaseStart),
            "EndDate" => clone($phaseEnd),
            "Wochen" => $phase->Wochen,
            "Abteilung" => $phase->Abteilung,
            "Farbe" => $currentAbteilung->Farbe
        ];

        $phaseStart = clone($phaseEnd);
        SetNextDay($phaseStart);
    }
}

ob_start();

$weeksBetweenLowestAndHighestDate = $lowestStartDate->diff($highestEndDate)->days / 7;
?>

<table>

<?php foreach ($planung as $azubi) : ?>

    <tr>

        <?php foreach ($azubi as $phase) : ?>
            <?php for ($i = 0; $i < $phase["Wochen"]; $i++) : ?>

                <td style="background-color: <?= $phase["Farbe"]; ?>; width: 16px; height: 16px;"></td>

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
