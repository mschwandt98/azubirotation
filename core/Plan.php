<?php
/**
 * Plan.php
 *
 * Baut den Plan für das Frontend in einer Tabelle zusammen.
 *
 * Grober Aufbau des Plans:
 *
 * Header:      Infos über Azubis  | Zeitraum 1 | Zeitraum 2 | ... | Zeitraum n
 * Body:        Infos über Azubi A | Plan 1     | Plan 2     | ... | Plan n
 *              Infos über Azubi B | Plan 1     | Plan 2     | ... | Plan n
 *              ...
 *              Infos über Azubi C | Plan 1     | Plan 2     | ... | Plan n
 */

use core\helper\DataHelper;
use core\helper\DateHelper;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

include_once(dirname(__DIR__) . '/config.php');

$helper = new DataHelper();

$Abteilungen = [];
foreach ($helper->GetAbteilungen() as $abteilung) {
    $Abteilungen[$abteilung->ID] = $abteilung;
}

$Ausbildungsberufe = [];
foreach ($helper->GetAusbildungsberufe() as $ausbildungsberuf) {
    $Ausbildungsberufe[$ausbildungsberuf->ID] = $ausbildungsberuf;
}

$Ansprechpartner = [];
foreach ($helper->GetAnsprechpartner() as $ansprechpartner) {
    $Ansprechpartner[$ansprechpartner->ID] = $ansprechpartner;
}

$Plaene = $helper->GetPlaene();

$azubisByAusbildungsberufe = [];
foreach ($helper->GetAzubis() as $azubi) {

    if (empty($tableFirstDate) || $azubi->Ausbildungsstart < $tableFirstDate) {
        $tableFirstDate = $azubi->Ausbildungsstart;
    }

    if (empty($tableLastDate) || $azubi->Ausbildungsende > $tableLastDate) {
        $tableLastDate = $azubi->Ausbildungsende;
    }

    $azubi->plan = [];
    foreach ($Plaene as $plan) {

        if ($plan->ID_Azubi === $azubi->ID) {

            $plan->Termin = $helper->GetTermin($plan->ID);
            $azubi->plan[] = $plan;
        }
    }

    $azubisByAusbildungsberufe[$azubi->ID_Ausbildungsberuf][] = $azubi;
}

if (empty($tableFirstDate) || empty($tableLastDate)) return;

// Volle Phasen (Startdatum und Enddatum) ermitteln
$fullPlanPhases = [];
foreach ($azubisByAusbildungsberufe as $k1 => $azubis) {
    foreach ($azubis as $k2 => $azubi) {

        $fullPlanPhases[$azubi->ID] = [];
        $lastAbteilung = 0;
        $lastAnsprechpartner = 0;
        $lastUniqueId = '';
        foreach ($azubi->plan as $k3 => $plan) {

            if ($lastAbteilung === $plan->ID_Abteilung && $lastAnsprechpartner === $plan->ID_Ansprechpartner) {
                $fullPlanPhases[$azubi->ID][$lastUniqueId]['Enddatum'] = DateHelper::FormatDate($plan->Enddatum);
            } else {
                $lastUniqueId = uniqid();
                $fullPlanPhases[$azubi->ID][$lastUniqueId] = [
                    'Startdatum'    => DateHelper::FormatDate($plan->Startdatum),
                    'Enddatum'      => DateHelper::FormatDate($plan->Enddatum)
                ];
            }

            $azubisByAusbildungsberufe[$k1][$k2]->plan[$k3]->uniqid = $lastUniqueId;
            $lastAbteilung = $plan->ID_Abteilung;
            $lastAnsprechpartner = $plan->ID_Ansprechpartner;
        }
    }
}

if (!DateHelper::IsMonday($tableFirstDate)) {
    $tableFirstDate = DateHelper::LastMonday($tableFirstDate);
}

$tableLastDate = DateHelper::NextSunday($tableLastDate);
$weeksInTable = ceil((strtotime($tableLastDate) - strtotime($tableFirstDate)) / (60 * 60 * 24 * 7));

$abteilungenInWeek = [];
$weeksPerMonth = [];
$currentDate = $tableFirstDate;
for ($i = 0; $i < $weeksInTable; $i++) {

    $month = DateHelper::FormatDate($currentDate, 'M Y');
    $abteilungenInWeek[DateHelper::FormatDate($currentDate, 'W Y')] = [];

    if (array_key_exists($month, $weeksPerMonth)) {
        $weeksPerMonth[$month]++;
    } else {
        $weeksPerMonth[$month] = 1;
    }

    $currentDate = DateHelper::NextMonday($currentDate);
}
unset($currentDate);
?>

<table>
    <thead>
        <tr>
            <th colspan="4" class="top-left-sticky"></th>

            <?php foreach ($weeksPerMonth as $month => $numberOfWeeks) : ?>
                <th colspan="<?= $numberOfWeeks; ?>"><?= $month; ?></th>
            <?php endforeach; ?>

        </tr>
        <tr>
            <th class="top-left-sticky">Nachname</th>
            <th class="top-left-sticky">Vorname</th>
            <th class="top-left-sticky">Kürzel</th>
            <th class="top-left-sticky">Zeitraum</th>

            <?php $currentDate = $tableFirstDate; ?>
            <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>

                <th class="month <?= (DateHelper::InRange(date('Y-m-d'), $currentDate, DateHelper::NextSunday($currentDate))) ? 'current-week' : '' ?>"
                    title="<?= DateHelper::FormatDate($currentDate); ?> - <?= DateHelper::NextSunday($currentDate, 'd.m.Y'); ?>">
                    <?= DateHelper::FormatDate($currentDate, 'W'); ?>
                </th>

                <?php $currentDate = DateHelper::NextMonday($currentDate); ?>
            <?php endfor; ?>
            <?php unset($currentDate); ?>

        </tr>
    </thead>
    <tbody>

        <?php foreach ($azubisByAusbildungsberufe as $id_ausbildungsberuf => $azubis) : ?>

            <tr>
                <th class="ausbildungsberuf" colspan="4">
                    <i class="icon-triangle-down"></i>
                    <b><?= $Ausbildungsberufe[$id_ausbildungsberuf]->Bezeichnung; ?></b>
                </th>
                <td colspan="<?= $weeksInTable; ?>"></td>
            </tr>

            <?php foreach ($azubis as $azubi) : ?>
                <?php if ($azubi->Ausbildungsende >= DateHelper::Today()) : ?>

                    <tr class="azubi"
                        data-id="<?= $azubi->ID; ?>">
                        <th><?= $azubi->Nachname; ?></th>
                        <th><?= $azubi->Vorname; ?></th>
                        <th><?= $azubi->Kuerzel; ?></th>
                        <th>
                            <?= DateHelper::FormatDate($azubi->Ausbildungsstart) . ' - ' . DateHelper::FormatDate($azubi->Ausbildungsende); ?>
                        </th>

                        <?php $currentDate = $tableFirstDate; ?>
                        <?php for ($i = 0; $i < $weeksInTable; $i++) : ?>
                            <?php if ($plan = AzubiHasPlan($azubi, $currentDate)) : ?>
                                <?php $abteilung = $Abteilungen[$plan->ID_Abteilung]; ?>
                                <?php $abteilungenInWeek[DateHelper::FormatDate($currentDate, 'W Y')][$abteilung->ID] = $abteilung; ?>

                                <td class="plan-phase
                                    <?= IsAusbildungsstart($azubi->Ausbildungsstart, $currentDate) ? 'icon-mark-begin': ''; ?>
                                    <?= IsAusbildungsende($azubi->Ausbildungsende, $currentDate) ? 'icon-mark-end': ''; ?>"
                                    style="background-color: <?= $abteilung->Farbe; ?>; border-left-color: <?= $abteilung->Farbe; ?>; border-right-color: <?= $abteilung->Farbe; ?>;"
                                    data-date="<?= $currentDate; ?>"
                                    data-id-abteilung="<?= $plan->ID_Abteilung;?>"
                                    data-id-ansprechpartner="<?= $plan->ID_Ansprechpartner ?? ''; ?>"
                                    title="<?= $abteilung->Bezeichnung; ?> | <?= $fullPlanPhases[$azubi->ID][$plan->uniqid]['Startdatum']; ?> - <?= $fullPlanPhases[$azubi->ID][$plan->uniqid]['Enddatum']; ?>"
                                >

                                    <?php if (IsFirstPhaseInAbteilung($azubi, $plan, $currentDate) && !empty($plan->ID_Ansprechpartner)) : ?>

                                        <span class="ansprechpartner-name">
                                            <?= $Ansprechpartner[$plan->ID_Ansprechpartner]->Name; ?>
                                        </span>

                                    <?php endif; ?>

                                    <?php if (!empty($plan->Termin)) : ?>

                                        <div class="plan-mark <?= ($plan->Termin->Separat) ? 'icon-plan-mark-separat' : 'icon-plan-mark' ; ?>"
                                            title="<?= $plan->Termin->Bezeichnung; ?>">
                                        </div>

                                    <?php endif; ?>

                                </td>

                            <?php else: ?>

                                <td class="plan-phase
                                    <?= IsAusbildungsstart($azubi->Ausbildungsstart, $currentDate) ? 'icon-mark-begin': ''; ?>
                                    <?= IsAusbildungsende($azubi->Ausbildungsende, $currentDate) ? 'icon-mark-end': ''; ?>"
                                    data-date="<?= $currentDate; ?>"></td>

                            <?php endif; ?>

                            <?php $currentDate = DateHelper::NextMonday($currentDate); ?>
                        <?php endfor; ?>

                    </tr>

                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </tbody>
</table>

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

    $loopMax = count($azubi->plan);
    for ($i = 0; $i < $loopMax; $i++) {

        $currentPlan = $azubi->plan[$i];

        if ($currentPlan->ID === $plan->ID) {

            if (array_key_exists($i - 1, $azubi->plan)) {

                if ($azubi->plan[$i - 1]->ID_Abteilung !== $plan->ID_Abteilung) return true;
                if ($azubi->plan[$i - 1]->ID_Ansprechpartner !== $plan->ID_Ansprechpartner) return true;

                $diff = (strtotime($currentPlan->Startdatum) - strtotime($azubi->plan[$i - 1]->Enddatum));
                if (floor($diff / (60 * 60 * 24)) <= 1) return false;
            }

            return true;
        }
    }
}
