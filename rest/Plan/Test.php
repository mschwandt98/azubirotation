<?php
use Core\Helper;
use Core\Helper\DateHelper;
use Core\PlanErrorCodes;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER . "/DateHelper.php");
include_once(BASE . "/core/Helper.php");
include_once(BASE . "/core/PlanErrorCodes.php");

$helper = new Helper();

$Abteilungen    = $helper->GetAbteilungen();
$Azubis         = $helper->GetAzubis();
$Standardplaene = $helper->GetStandardplaene();
$Plaene         = $helper ->GetPlaene();

$errors = [];

foreach ($Azubis as $azubi) {

    $azubi->plan = [];

    foreach ($Plaene as $plan) {

        if ($plan->ID_Azubi === $azubi->ID) {
            $azubi->plan[] = $plan;
        }
    }
}

foreach ($Azubis as $azubi) {

    foreach ($azubi->plan as $plan) {

        if ($plan->Startdatum < $azubi->Ausbildungsstart || $plan->Enddatum > $azubi->Ausbildungsende) {
            $errors[PlanErrorCodes::Ausbildungszeitraum][] = [
                "Azubi" => $azubi,
                "Plan"  => $plan
            ];
        }
    }
}

$abteilungsHelper = [];
foreach ($Abteilungen as $abteilung) {

    foreach ($Plaene as $plan) {

        if ($plan->Startdatum === "2020-12-28") {
            $test = 0;
        }

        if ($plan->ID_Abteilung === $abteilung->ID) {

            if (empty($abteilungsHelper)) {
                $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, $plan->Enddatum)] = 1;
                continue;
            }

            foreach ($abteilungsHelper as $timePeriod => $maxAzubis) {

                $dates = GetDatesFromTimePeriod($timePeriod);
                $timePeriodToCheck = null;

                if ($plan->Startdatum === $dates["StartDatum"] && $plan->Enddatum === $dates["EndDatum"]) {

                    $abteilungsHelper[$timePeriod]++;
                    $timePeriodToCheck = $timePeriod;

                } elseif (IsDateInRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    !IsDateInRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[BuildTimePeriodString($dates["StartDatum"], DateDayBefore($plan->Startdatum))] = $maxAzubis;
                    $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, $dates["EndDatum"])] = $maxAzubis + 1;
                    $abteilungsHelper[BuildTimePeriodString(DateDayAfter($dates["EndDatum"]), $plan->EndDatum)] = 1;
                    $timePeriodToCheck = BuildTimePeriodString($plan->Startdatum, $dates["EndDatum"]);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif (IsDateInRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    IsDateInRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[BuildTimePeriodString($dates["StartDatum"], DateDayBefore($plan->Startdatum))] = $maxAzubis;
                    $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, $plan->Enddatum)] = $maxAzubis + 1;
                    $abteilungsHelper[BuildTimePeriodString(DateDayAfter($plan->Enddatum), $dates["EndDatum"])] = $maxAzubis;
                    $timePeriodToCheck = BuildTimePeriodString($plan->Startdatum, $plan->Enddatum);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif (!IsDateInRange($plan->Startdatum, $dates["StartDatum"], $dates["EndDatum"]) &&
                    IsDateInRange($plan->Enddatum, $dates["StartDatum"], $dates["EndDatum"])) {

                    $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, DateDayBefore($dates["StartDatum"]))] = 1;
                    $abteilungsHelper[BuildTimePeriodString($dates["StartDatum"], $plan->Enddatum)] = $maxAzubis + 1;
                    $abteilungsHelper[BuildTimePeriodString(DateDayAfter($plan->Enddatum), $dates["EndDatum"])] = $maxAzubis;
                    $timePeriodToCheck = BuildTimePeriodString($dates["StartDatum"], $plan->Enddatum);
                    unset($abteilungsHelper[$timePeriod]);

                } elseif ($plan->Startdatum === $dates["StartDatum"]) {

                    $abteilungsHelper[$timePeriod]++;
                    $abteilungsHelper[BuildTimePeriodString(DateDayAfter($date["EndDatum"]), $plan->Enddatum)] = 1;
                    $timePeriodToCheck = $timePeriod;

                } elseif ($plan->Enddatum === $dates["EndDatum"]) {

                    $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, DateDayBefore($date["Startdatum"]))] = 1;
                    $abteilungsHelper[$timePeriod]++;
                    $timePeriodToCheck = $timePeriod;

                } elseif (!array_key_exists(BuildTimePeriodString($plan->Startdatum, $plan->Enddatum), $abteilungsHelper)) {
                    $abteilungsHelper[BuildTimePeriodString($plan->Startdatum, $plan->Enddatum)] = 1;
                }

                if (!empty($timePeriodToCheck)) {

                    if ($abteilungsHelper[$timePeriodToCheck] > $abteilung->MaxAzubis) {

                        if (!array_key_exists(PlanErrorCodes::AbteilungenMaxAzubis, $errors)) {
                            $errors[PlanErrorCodes::AbteilungenMaxAzubis] = [];
                        }

                        if (!array_key_exists($abteilung->ID, $errors[PlanErrorCodes::AbteilungenMaxAzubis])) {
                            $errors[PlanErrorCodes::AbteilungenMaxAzubis][$abteilung->ID] = [];
                        }

                        $errors[PlanErrorCodes::AbteilungenMaxAzubis][$abteilung->ID][DateHelper::FormatDate($plan->Startdatum) . " - " . DateHelper::FormatDate($plan->Enddatum)] = $abteilungsHelper[$timePeriodToCheck];
                    }
                }
            }
        }

        ksort($abteilungsHelper);
    }
}

exit ((empty($errors)) ? true : include_once(BASE . "/core/PlanErrorList.php"));

function BuildTimePeriodString($startDate, $endDate) {
    return $startDate . " " . $endDate;
}

function GetDatesFromTimePeriod($timePeriod) {

    $dates = explode(" ", $timePeriod);
    return [
        "StartDatum" => $dates[0],
        "EndDatum" => $dates[1]
    ];
}

function IsDateInRange($date, $startDate, $endDate) {
    if ($date > $startDate && $date < $endDate ) return true;
    return false;
}

function DateDayAfter($date) {
    return date("Y-m-d", strtotime("$date +1 day"));
}

function DateDayBefore($date) {
    return date("Y-m-d", strtotime("$date -1 day"));
}
