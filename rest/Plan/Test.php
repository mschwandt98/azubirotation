<?php
use core\helper\DataHelper;
use core\helper\DateHelper;
use core\PlanErrorCodes;

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (!is_logged_in() || !is_token_valid()) {
    http_response_code(401);
    exit;
}

$helper = new DataHelper();
$Azubis = $helper->GetAzubis();
$Standardplaene = $helper->GetStandardplaene();

$plaeneAbteilungen = [];
$plaeneAzubis = [];
foreach ($Azubis as $azubi) {
    $plaeneAzubis[$azubi->ID] = [];
}

// Pläne nach Azubis und Abteilungen sortieren
foreach ($helper->GetPlaene() as $plan) {
    $plaeneAzubis[$plan->ID_Azubi][] = $plan;
    $plaeneAbteilungen[$plan->ID_Abteilung][] = $plan;
}

// Azubi ist in länger in Abteilungen als im Plan vorgesehen &
// Planung außerhalb des Ausbildungszeitraums &
// Abteilung am Anfang der Ausbildung entspricht Standardplan
$errors = [];
foreach ($Azubis as $azubi) {

    $abteilungenCounter = [];
    foreach ($plaeneAzubis[$azubi->ID] as $plan) {

        if ($plan->Enddatum < $azubi->Ausbildungsstart || $plan->Startdatum > $azubi->Ausbildungsende) {
            $zeitraum = DateHelper::BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);
            $errors[PlanErrorCodes::Ausbildungszeitraum][$azubi->ID][] = $zeitraum;
        }

        $abteilungenCounter[$plan->ID_Abteilung] = ($abteilungenCounter[$plan->ID_Abteilung] ?? 0) + 1;
    }

    unset($standardplan);
    foreach ($Standardplaene as $plan) {
        if ($azubi->ID_Ausbildungsberuf === $plan->ID_Ausbildungsberuf) {
            $standardplan = $plan;
            break;
        }
    }

    if (empty($standardplan)) continue;

    $praeferierteAbteilungen = [];
    foreach ($standardplan->Phasen as $phase) {
        if ($phase->Praeferieren) {
            $praeferierteAbteilungen[] = $phase->ID_Abteilung;
        }
    }

    if (!empty($praeferierteAbteilungen)) {
        foreach ($plaeneAzubis[$azubi->ID] as $plan) {
            if ($plan->Startdatum <= $azubi->Ausbildungsstart && $plan->Enddatum >= $azubi->Ausbildungsstart) {
                if (!in_array($plan->ID_Abteilung, $praeferierteAbteilungen)) {
                    $errors[PlanErrorCodes::PraeferierteAbteilungen][$azubi->ID][] = $plan->ID_Abteilung;
                }
            }
        }
    }

    // Falls eine Abteilung mehrmals im Standardplan ist, müssen die Wochen für diese Abteilung addiert werden
    $wochenEinerAbteilungGesamt = [];
    foreach ($standardplan->Phasen as $phase) {
        if (array_key_exists($phase->ID_Abteilung, $abteilungenCounter)) {
            if (array_key_exists($phase->ID_Abteilung, $wochenEinerAbteilungGesamt)) {
                $wochenEinerAbteilungGesamt[$phase->ID_Abteilung] += $phase->Wochen;
            } else {
                $wochenEinerAbteilungGesamt[$phase->ID_Abteilung] = $phase->Wochen;
            }
        }
    }

    foreach ($wochenEinerAbteilungGesamt as $id_abteilung => $wochen) {
        if (array_key_exists($id_abteilung, $abteilungenCounter)) {
            if ($abteilungenCounter[$id_abteilung] > $wochen) {
                $errors[PlanErrorCodes::WochenInAbteilungen][$azubi->ID][] = $id_abteilung;
            }
        }
    }
}

// Maximale Anzahl an Azubis in Abteilung
foreach ($helper->GetAbteilungen() as $abteilung) {

    if (empty($plaeneAbteilungen[$abteilung->ID])) continue;

    $abteilungsHelper = [];
    foreach ($plaeneAbteilungen[$abteilung->ID] as $plan) {
        $planTimePeriodString = DateHelper::BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);

        if (empty($abteilungsHelper)) {
            $abteilungsHelper[$planTimePeriodString] = 1;
            continue;
        }

        foreach ($abteilungsHelper as $timePeriod => $maxAzubis) {

            $timePeriodToCheck = null;
            $dates = DateHelper::GetDatesFromString($timePeriod);

            if ($plan->Startdatum === $dates['StartDatum'] && $plan->Enddatum === $dates['EndDatum']) {

                $abteilungsHelper[$timePeriod]++;
                $timePeriodToCheck = $timePeriod;

            } elseif (DateHelper::InRange($plan->Startdatum, $dates['StartDatum'], $dates['EndDatum']) &&
                !DateHelper::InRange($plan->Enddatum, $dates['StartDatum'], $dates['EndDatum'])) {

                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $dates['StartDatum'],
                    DateHelper::DayBefore($plan->Startdatum)
                )] = $maxAzubis;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $plan->Startdatum,
                    $dates['EndDatum'])
                ] = $maxAzubis + 1;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    DateHelper::DayAfter($dates['EndDatum']),
                    $plan->EndDatum)
                ] = 1;

                $timePeriodToCheck = DateHelper::BuildTimePeriodString($plan->Startdatum, $dates['EndDatum']);
                unset($abteilungsHelper[$timePeriod]);

            } elseif (DateHelper::InRange($plan->Startdatum, $dates['StartDatum'], $dates['EndDatum']) &&
                DateHelper::InRange($plan->Enddatum, $dates['StartDatum'], $dates['EndDatum'])) {

                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $dates['StartDatum'],
                    DateHelper::DayBefore($plan->Startdatum))
                ] = $maxAzubis;
                $abteilungsHelper[$planTimePeriodString] = $maxAzubis + 1;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    DateHelper::DayAfter($plan->Enddatum),
                    $dates['EndDatum']
                )] = $maxAzubis;

                $timePeriodToCheck = $planTimePeriodString;
                unset($abteilungsHelper[$timePeriod]);

            } elseif (!DateHelper::InRange($plan->Startdatum, $dates['StartDatum'], $dates['EndDatum']) &&
                DateHelper::InRange($plan->Enddatum, $dates['StartDatum'], $dates['EndDatum'])) {

                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $plan->Startdatum,
                    DateHelper::DayBefore($dates['StartDatum'])
                )] = 1;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $dates['StartDatum'],
                    $plan->Enddatum
                )] = $maxAzubis + 1;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    DateHelper::DayAfter($plan->Enddatum),
                    $dates['EndDatum']
                )] = $maxAzubis;

                $timePeriodToCheck = DateHelper::BuildTimePeriodString($dates['StartDatum'], $plan->Enddatum);
                unset($abteilungsHelper[$timePeriod]);

            } elseif ($plan->Startdatum === $dates['StartDatum']) {

                $abteilungsHelper[$timePeriod]++;
                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    DateHelper::DayAfter($date['EndDatum']),
                    $plan->Enddatum
                )] = 1;

                $timePeriodToCheck = $timePeriod;

            } elseif ($plan->Enddatum === $dates['EndDatum']) {

                $abteilungsHelper[DateHelper::BuildTimePeriodString(
                    $plan->Startdatum,
                    DateHelper::DayBefore($date['Startdatum'])
                )] = 1;
                $abteilungsHelper[$timePeriod]++;

                $timePeriodToCheck = $timePeriod;

            } elseif (!array_key_exists($planTimePeriodString, $abteilungsHelper)) {
                $abteilungsHelper[$planTimePeriodString] = 1;
            }

            if (!empty($timePeriodToCheck)) {

                if ($abteilungsHelper[$timePeriodToCheck] > $abteilung->MaxAzubis) {
                    $errors[PlanErrorCodes::AbteilungenMaxAzubis][$abteilung->ID][$planTimePeriodString] = $abteilungsHelper[$timePeriodToCheck];
                }
            }
        }

        ksort($abteilungsHelper);
    }
}

if (empty($errors)) {
    exit(true);
}

$errors = SumUpTimePeriods($errors);

ob_start('minifier');
include_once(BASE . '/templates/PlanErrors.php');
ob_end_flush();
exit;

/**
 * Fasst die Zeiträume von Fehler zusammen, sodass nicht jeder Woche einzeln als
 * Fehler ausgegeben wird, sondern mehrere Wochen desselben Fehlers zu einem
 * Zeitraum zusammengefasst sind.
 *
 * @param array $errors Die zusammenzufassenden Fehler.
 *
 * @return array Die zusammengefassten Fehler.
 */
function SumUpTimePeriods($errors) {

    if (array_key_exists(PlanErrorCodes::AbteilungenMaxAzubis, $errors)) {

        $summedUpErrors = [];
        foreach ($errors[PlanErrorCodes::AbteilungenMaxAzubis] as $id_abteilung => $zeitraeume) {

            $lastMonday = '';
            $lastAnzahlAzubis = 0;
            $zeitraumHolder = [];
            foreach ($zeitraeume as $zeitraum => $anzahlAzubis) {

                $dates = DateHelper::GetDatesFromString($zeitraum);
                $weekStart = $dates['StartDatum'];

                if (!empty($lastMonday) &&
                    DateHelper::NextMonday($lastMonday) === $weekStart &&
                    $lastAnzahlAzubis === $anzahlAzubis) {

                    end($zeitraumHolder);
                    $expandableZeitraum = DateHelper::GetDatesFromString(key($zeitraumHolder));
                    array_pop($zeitraumHolder);
                    $zeitraumHolder[
                        DateHelper::BuildTimePeriodString($expandableZeitraum['StartDatum'], $dates['EndDatum'])
                    ] = $anzahlAzubis;
                } else {
                    $zeitraumHolder[$zeitraum] = $anzahlAzubis;
                }

                $lastMonday = $weekStart;
                $lastAnzahlAzubis = $anzahlAzubis;
            }

            ksort($zeitraumHolder);
            $summedUpErrors[PlanErrorCodes::AbteilungenMaxAzubis][$id_abteilung] = $zeitraumHolder;
        }
    }

    if (array_key_exists(PlanErrorCodes::Ausbildungszeitraum, $errors)) {

        $zeitraumHolder = [];
        foreach ($errors[PlanErrorCodes::Ausbildungszeitraum] as $azubi_id => $zeitraeume) {

            $lastStartdate = '';
            foreach ($zeitraeume as $zeitraum) {

                $dates = DateHelper::GetDatesFromString($zeitraum);

                if (!empty($lastStartdate) && DateHelper::NextMonday($lastStartdate) === $dates['StartDatum']) {

                    $expandableZeitraum = DateHelper::GetDatesFromString(array_pop($zeitraumHolder[$azubi_id]));
                    $zeitraumHolder[$azubi_id][] = DateHelper::BuildTimePeriodString(
                        $expandableZeitraum['StartDatum'],
                        $dates['EndDatum']
                    );

                } else {
                    $zeitraumHolder[$azubi_id][] = $zeitraum;
                }

                $lastStartdate = $dates['StartDatum'];
            }

            ksort($zeitraumHolder[$azubi_id]);
            $summedUpErrors[PlanErrorCodes::Ausbildungszeitraum][$azubi_id] = $zeitraumHolder[$azubi_id];
        }
    }

    if (array_key_exists(PlanErrorCodes::PraeferierteAbteilungen, $errors)) {
        $summedUpErrors[PlanErrorCodes::PraeferierteAbteilungen] = $errors[PlanErrorCodes::PraeferierteAbteilungen];
    }

    if (array_key_exists(PlanErrorCodes::WochenInAbteilungen, $errors)) {
        $summedUpErrors[PlanErrorCodes::WochenInAbteilungen] = $errors[PlanErrorCodes::WochenInAbteilungen];
    }

    return $summedUpErrors;
}
