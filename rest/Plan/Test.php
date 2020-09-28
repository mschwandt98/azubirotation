<?php
use Core\Helper\DataHelper;
use Core\Helper\DateHelper;
use Core\PlanErrorCodes;

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

foreach ($Azubis as $azubi) {

    foreach ($Plaene as $plan) {

        if ($plan->ID_Azubi === $azubi->ID) {

            if ($plan->Startdatum < $azubi->Ausbildungsstart || $plan->Enddatum > $azubi->Ausbildungsende) {
                $errors[PlanErrorCodes::Ausbildungszeitraum][] = [
                    "Azubi" => $azubi,
                    "Plan"  => $plan
                ];
            }
        }
    }
}

$abteilungsHelper = [];
foreach ($Abteilungen as $abteilung) {

    foreach ($Plaene as $plan) {

        if ($plan->ID_Abteilung === $abteilung->ID) {

            $planTimePeriodString = DateHelper::BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);

            if (empty($abteilungsHelper)) {
                $abteilungsHelper[$planTimePeriodString] = 1;
                continue;
            }

            $timePeriodToCheck;

            foreach ($abteilungsHelper as $timePeriod => $maxAzubis) {

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
