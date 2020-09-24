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

$tableFirstDate;
$tableLastDate;

foreach ($Azubis as $azubi) {

    if (empty($tableFirstDate) || $azubi->Ausbildungsstart < $tableFirstDate) {
        $tableFirstDate = $azubi->Ausbildungsstart;
    }

    if (empty($tableLastDate) || $azubi->Ausbildungsende > $tableLastDate) {
        $tableLastDate = $azubi->Ausbildungsende;
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

            <tr>
                <td class="azubi-info"><?= $azubi->Nachname; ?></td>
                <td class="azubi-info"><?= $azubi->Vorname; ?></td>
                <td class="azubi-info azubi-ausbildungszeit">
                    <?= date("d.m.Y", strtotime($azubi->Ausbildungsstart)) . " - " . date("d.m.Y", strtotime($azubi->Ausbildungsende)); ?>
                </td>

                <?php $currentDate = $tableFirstDate; ?>
                <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                    <td class="plan-phase" data-date="<?= $currentDate; ?>" data-azubi-id="<?= $azubi->ID; ?>"></td>

                    <?php $currentDate = date("Y-m-d", strtotime($currentDate . " next monday")); ?>
                <?php endfor; ?>

            </tr>

        <?php endforeach; ?>

    </table>
</div>
