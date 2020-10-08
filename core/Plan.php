<?php
use Core\Helper\DataHelper;
use Core\Helper\DateHelper;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

include_once(dirname(__DIR__) . "/config.php");
include_once(HELPER . "/DataHelper.php");
include_once(HELPER . "/DateHelper.php");

$helper = new DataHelper();

$Abteilungen        = $helper->GetAbteilungen();
$Ansprechpartner    = $helper->GetAnsprechpartner();
$Ausbildungsberufe  = $helper->GetAusbildungsberufe();
$Azubis             = $helper->GetAzubis();
$Standardplaene     = $helper->GetStandardPlaene();
$Plaene             = $helper->GetPlaene();

$tableFirstDate;
$tableLastDate;

$azubisByAusbildungsberufe = [];
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

    $azubisByAusbildungsberufe[$azubi->ID_Ausbildungsberuf][] = $azubi;
}

if (empty($tableFirstDate) || empty($tableLastDate)) return;

if (!DateHelper::IsMonday($tableFirstDate)) {
    $tableFirstDate = DateHelper::LastMonday($tableFirstDate);
}

$weeksInTable = ceil(
    (strtotime($tableLastDate) - strtotime($tableFirstDate)) / (60 * 60 * 24 * 7)
);
?>

<div class="horizontal-scroll">
    <table>
        <thead>
            <tr>
                <th class="sticky-col">Nachname</th>
                <th class="sticky-col">Vorname</th>
                <th class="sticky-col">Zeitraum</th>

                <?php $currentDate = $tableFirstDate; ?>
                <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                    <th class="month"
                        title="<?= DateHelper::FormatDate($currentDate); ?> - <?= DateHelper::NextSunday($currentDate, "d.m.Y"); ?>"
                    >
                        <?= DateHelper::FormatDate($currentDate, "M Y"); ?>
                    </th>

                    <?php $currentDate = DateHelper::NextMonday($currentDate); ?>
                <?php endfor; ?>
                <?php unset($currentDate); ?>

            </tr>
        </thead>
        <tbody>

            <?php foreach ($azubisByAusbildungsberufe as $id_ausbildungsberuf => $azubis) : ?>

                <tr>
                    <td colspan="3" class="row-info sticky-col"><b><?= ($helper->GetAusbildungsberufe($id_ausbildungsberuf))->Bezeichnung; ?></b></td>
                    <td colspan="<?= $weeksInTable; ?>"></td>
                </tr>

                <?php foreach ($azubis as $azubi) : ?>

                    <tr class="azubi" data-id="<?= $azubi->ID; ?>">
                        <td class="row-info sticky-col"><?= $azubi->Nachname; ?></td>
                        <td class="row-info sticky-col"><?= $azubi->Vorname; ?></td>
                        <td class="row-info sticky-col">
                            <?= DateHelper::FormatDate($azubi->Ausbildungsstart) . " - " . DateHelper::FormatDate($azubi->Ausbildungsende); ?>
                        </td>

                        <?php $currentDate = $tableFirstDate; ?>
                        <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                            <?php if ($plan = AzubiHasPlan($azubi, $currentDate)) : ?>
                                <?php $abteilung = $helper->GetAbteilungen($plan->ID_Abteilung); ?>

                                <td class="plan-phase
                                    <?= IsAusbildungsstart($azubi->Ausbildungsstart, $currentDate) ? "mark-start": ""; ?>
                                    <?= IsAusbildungsende($azubi->Ausbildungsende, $currentDate) ? "mark-ende": ""; ?>"
                                    style="background-color: <?= $abteilung->Farbe; ?>; border-color: <?= $abteilung->Farbe; ?>;"
                                    data-date="<?= $currentDate; ?>"
                                    data-id-abteilung="<?= $plan->ID_Abteilung;?>"
                                    data-id-ansprechpartner="<?= $plan->ID_Ansprechpartner ?? ""; ?>"
                                >

                                    <?php if (IsFirstPhaseInAbteilung($azubi, $plan, $currentDate) && !empty($plan->ID_Ansprechpartner)) : ?>

                                        <span class="ansprechpartner-name"><?= $helper->GetAnsprechpartner($plan->ID_Ansprechpartner)->Name; ?></span>

                                    <?php endif; ?>

                                    <?php if (!empty($plan->Markierung)) : ?>

                                        <div class="plan-mark" title="<?= $plan->Markierung; ?>"></div>

                                    <?php endif; ?>

                                </td>

                            <?php else: ?>

                                <td class="plan-phase
                                    <?= IsAusbildungsstart($azubi->Ausbildungsstart, $currentDate) ? "mark-start": ""; ?>
                                    <?= IsAusbildungsende($azubi->Ausbildungsende, $currentDate) ? "mark-ende": ""; ?>"
                                    data-date="<?= $currentDate; ?>"></td>

                            <?php endif; ?>

                            <?php $currentDate = DateHelper::NextMonday($currentDate); ?>
                        <?php endfor; ?>

                    </tr>

                <?php endforeach; ?>
            <?php endforeach; ?>

        </tbody>
    </table>
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

function IsAusbildungsstart($ausbildungsstart, $date) {
    if ($ausbildungsstart === $date) return true;
    if (DateHelper::NextMonday($date) > $ausbildungsstart && $date < $ausbildungsstart) return true;
    return false;
}

function IsAusbildungsende($ausbildungsende, $date) {
    if ($ausbildungsende === $date) return true;
    if (DateHelper::NextMonday($date) > $ausbildungsende && $date < $ausbildungsende) return true;
    return false;
}

function IsFirstPhaseInAbteilung($azubi, $plan) {

    for ($i = 0; $i < count($azubi->plan); $i++) {

        $currentPlan = $azubi->plan[$i];

        if ($currentPlan->ID === $plan->ID) {

            if (array_key_exists($i - 1, $azubi->plan)) {

                if ($azubi->plan[$i - 1]->ID_Abteilung !== $plan->ID_Abteilung) return true;

                $diff = (strtotime($currentPlan->Startdatum) - strtotime($azubi->plan[$i - 1]->Enddatum));
                if (floor($diff / (60 * 60 * 24)) <= 1) return false;
            }

            return true;
        }
    }
}
