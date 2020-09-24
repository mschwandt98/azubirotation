<?php
use Core\Helper;

include_once(dirname(__DIR__) . "/config.php");
include_once(BASE . "/core/Helper.php");

$helper = new Helper();

$Abteilungen = $helper->GetAbteilungen();
$Ansprechpartner = $helper->GetAnsprechpartner();
$Ausbildungsberufe = $helper->GetAusbildungsberufe();
$Azubis = $helper->GetAzubis();
$Standardplaene = $helper->GetStandardPlaene();
$Plaene = $helper->GetPlaene();

$tableFirstDate;
$tableLastDate;

foreach ($Azubis as $azubi) {

    $azubi->plan = [];

    if (empty($tableFirstDate) || $azubi->Ausbildungsstart < $tableFirstDate) {
        $tableFirstDate = $azubi->Ausbildungsstart;
    }

    if (empty($tableLastDate) || $azubi->Ausbildungsende > $tableLastDate) {
        $tableLastDate = $azubi->Ausbildungsende;
    }

    foreach ($Plaene as $plan) {

        if ($plan->ID_Azubi === $azubi->ID) {
            $azubi->plan[] = $plan;
        }
    }
}

if (empty($tableFirstDate) || empty($tableLastDate)) {
    return;
}

if (strtolower(date("l", strtotime($tableFirstDate))) !== "monday") {
    $tableFirstDate = date("Y-m-d", strtotime($tableFirstDate . "last monday"));
}

$weeksInTable = ceil(
    (strtotime($tableLastDate) - strtotime($tableFirstDate)) / (60 * 60 * 24 * 7)
);
?>

<div id="Plan">
    <table>
        <tr>
            <th>Nachname</th>
            <th>Vorname</th>
            <th>Zeitraum</th>

            <?php $currentDate = $tableFirstDate; ?>
            <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                <th class="month"><?= date("M Y", strtotime($currentDate)); ?></th>

                <?php $currentDate = date("Y-m-d", strtotime($currentDate . " next monday")); ?>
            <?php endfor; ?>
            <?php unset($currentDate); ?>

        </tr>

        <?php foreach ($Azubis as $azubi) : ?>

            <tr class="azubi" data-id="<?= $azubi->ID; ?>">
                <td class="azubi-info"><?= $azubi->Nachname; ?></td>
                <td class="azubi-info"><?= $azubi->Vorname; ?></td>
                <td class="azubi-info azubi-ausbildungszeit">
                    <?= date("d.m.Y", strtotime($azubi->Ausbildungsstart)) . " - " . date("d.m.Y", strtotime($azubi->Ausbildungsende)); ?>
                </td>

                <?php $currentDate = $tableFirstDate; ?>
                <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                    <?php if ($plan = AzubiHasPlan($azubi, $currentDate)) : ?>

                        <td class="plan-phase"
                            style="background-color: <?= GetAbteilungsFarbe($plan->ID_Abteilung); ?>;"
                            data-date="<?= $currentDate; ?>"
                            data-id-abteilung="<?= $plan->ID_Abteilung;?>"
                            data-id-ansprechpartner="<?= $plan->ID_Ansprechpartner; ?>"
                        ></td>

                    <?php else: ?>

                        <td class="plan-phase" data-date="<?= $currentDate; ?>"></td>

                    <?php endif; ?>

                    <?php $currentDate = date("Y-m-d", strtotime($currentDate . " next monday")); ?>
                <?php endfor; ?>

            </tr>

        <?php endforeach; ?>

    </table>

    <input type="button" id="SavePlan" value="Planung speichern" />
</div>


<div id="Legende">

    <?php foreach ($Abteilungen as $abteilung) : ?>

        <div class="abteilung">
            <div class="farbe" style="background-color: <?= $abteilung->Farbe; ?>;"></div>
            <div><?= $abteilung->Bezeichnung; ?></div>
        </div>

    <?php endforeach; ?>

</div>

<?php
function AzubiHasPlan($azubi, $startDate) {

    foreach ($azubi->plan as $phase) {
        if ($phase->Startdatum === $startDate) {
            return $phase;
        }
    }

    return false;
}

function GetAbteilungsFarbe($id_abteilung) {

    global $Abteilungen;

    foreach ($Abteilungen as $abteilung) {
        if ($id_abteilung === $abteilung->ID) {
            return $abteilung->Farbe;
        }
    }
}
