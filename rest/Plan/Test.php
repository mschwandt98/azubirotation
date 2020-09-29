<?php
use Core\Helper\DataHelper;
use Core\Helper\DateHelper;
use Core\PlanErrorCodes;
use Models\Plan;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER . "/DataHelper.php");
include_once(HELPER . "/DateHelper.php");
include_once(BASE . "/core/PlanErrorCodes.php");

$helper = new DataHelper();

$Abteilungen    = $helper->GetAbteilungen();
$Azubis         = $helper->GetAzubis();
$Standardplaene = $helper->GetStandardplaene();
$Plaene         = $helper ->GetPlaene();

$errors = [];

// Azubi ist in länger in Abteilungen als im Plan vorgesehen &
// Planung außerhalb des Ausbildungszeitraums &
// Abteilung am Anfang der Ausbildung entspricht Standardplan
foreach ($Azubis as $azubi) {

    $abteilungenCounter = [];
    foreach ($Plaene as $plan) {
        if ($plan->ID_Azubi === $azubi->ID) {
            if ($plan->Enddatum < $azubi->Ausbildungsstart || $plan->Startdatum > $azubi->Ausbildungsende) {
                $key = DateHelper::FormatDate($plan->Startdatum) . " - " . DateHelper::FormatDate($plan->Enddatum);
                $errors[PlanErrorCodes::Ausbildungszeitraum][$azubi->ID][$key] = $plan;
            }

            $abteilungenCounter[$plan->ID_Abteilung] = ($abteilungenCounter[$plan->ID_Abteilung] ?? 0) + 1;
        }
    }

    $standardplan;
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
        foreach ($Plaene as $plan) {
            if ($plan->ID_Azubi === $azubi->ID) {
                if ($plan->Startdatum <= $azubi->Ausbildungsstart && $plan->Enddatum >= $azubi->Ausbildungsstart) {
                    if (!in_array($plan->ID_Abteilung, $praeferierteAbteilungen)) {
                        $errors[PlanErrorCodes::PraeferierteAbteilungen][$azubi->ID] = $plan->ID_Abteilung;
                    }
                }
            }
        }
    }

    foreach ($standardplan->Phasen as $phase) {
        if (array_key_exists($phase->ID_Abteilung, $abteilungenCounter)) {
            if ($abteilungenCounter[$phase->ID_Abteilung] > $phase->Wochen) {
                $errors[PlanErrorCodes::WochenInAbteilungen][$azubi->ID] = $phase->ID_Abteilung;
            }
        }
    }
}

// Maximale Anzahl an Azubis in Abteilung
foreach ($Abteilungen as $abteilung) {

    $abteilungsHelper = [];
    foreach ($Plaene as $plan) {

        if ($plan->ID_Abteilung === $abteilung->ID) {

            $planTimePeriodString = DateHelper::BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);

            if (empty($abteilungsHelper)) {
                $abteilungsHelper[$planTimePeriodString] = 1;
                continue;
            }

            foreach ($abteilungsHelper as $timePeriod => $maxAzubis) {

                $timePeriodToCheck = null;
                $dates = DateHelper::GetDatesFromString($timePeriod);

                if ($plan->Startdatum === $dates["StartDatum"] && $plan->Enddatum === $dates["EndDatum"]) {

                    $abteilungsHelper[$timePeriod]++;
                    $timePeriodToCheck = $timePeriod;

                } elseif (DateHelper::InRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    !DateHelper::InRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $dates["StartDatum"],
                        DateHelper::DayBefore($plan->Startdatum)
                    )] = $maxAzubis;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $plan->Startdatum,
                        $dates["EndDatum"])
                    ] = $maxAzubis + 1;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        DateHelper::DayAfter($dates["EndDatum"]),
                        $plan->EndDatum)
                    ] = 1;

                    $timePeriodToCheck = DateHelper::BuildTimePeriodString($plan->Startdatum, $dates["EndDatum"]);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif (DateHelper::InRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    DateHelper::InRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $dates["StartDatum"],
                        DateHelper::DayBefore($plan->Startdatum))
                    ] = $maxAzubis;
                    $abteilungsHelper[$planTimePeriodString] = $maxAzubis + 1;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        DateHelper::DayAfter($plan->Enddatum),
                        $dates["EndDatum"]
                    )] = $maxAzubis;

                    $timePeriodToCheck = DateHelper::BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif (!DateHelper::InRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    DateHelper::InRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $plan->Startdatum,
                        DateHelper::DayBefore($dates["StartDatum"])
                    )] = 1;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $dates["StartDatum"],
                        $plan->Enddatum
                    )] = $maxAzubis + 1;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        DateHelper::DayAfter($plan->Enddatum),
                        $dates["EndDatum"]
                    )] = $maxAzubis;

                    $timePeriodToCheck = DateHelper::BuildTimePeriodString($dates["StartDatum"], $plan->Enddatum);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif ($plan->Startdatum === $dates["StartDatum"]) {

                    $abteilungsHelper[$timePeriod]++;
                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        DateHelper::DayAfter($date["EndDatum"]),
                        $plan->Enddatum
                    )] = 1;

                    $timePeriodToCheck = $timePeriod;

                } elseif ($plan->Enddatum === $dates["EndDatum"]) {

                    $abteilungsHelper[DateHelper::BuildTimePeriodString(
                        $plan->Startdatum,
                        DateHelper::DayBefore($date["Startdatum"])
                    )] = 1;
                    $abteilungsHelper[$timePeriod]++;

                    $timePeriodToCheck = $timePeriod;

                } elseif (!array_key_exists($planTimePeriodString, $abteilungsHelper)) {
                    $abteilungsHelper[$planTimePeriodString] = 1;
                }

                if (!empty($timePeriodToCheck)) {

                    if ($abteilungsHelper[$timePeriodToCheck] > $abteilung->MaxAzubis) {
                        $key = DateHelper::FormatDate($plan->Startdatum) . " - " . DateHelper::FormatDate($plan->Enddatum);
                        $errors[PlanErrorCodes::AbteilungenMaxAzubis][$abteilung->ID][$key] = $abteilungsHelper[$timePeriodToCheck];
                    }
                }
            }
        }

        ksort($abteilungsHelper);
    }
}

exit ((empty($errors)) ? true : include_once(BASE . "/templates/PlanErrors.php"));
