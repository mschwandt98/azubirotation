<?php
use Core\AbteilungsManager;
use Core\AzubiManager;
use Core\Helper;

include_once(dirname(__DIR__) . "/config.php");
include_once(__DIR__ . "/AbteilungsManager.php");
include_once(__DIR__ . "/AzubiManager.php");
include_once("Helper.php");
include_models();

// Hole Daten ----------------------------------------------------------------------------------------------------------
$helper             = new Helper();
$abteilungen        = $helper->GetAbteilungen();
$ansprechpartner    = $helper->GetAnsprechpartner();
$ausbildungsberufe  = $helper->GetAusbildungsberufe();
$azubis             = $helper->GetAzubis();
$standardplaene     = $helper->GetStandardPlaene();

// Vorbereitung der Manager --------------------------------------------------------------------------------------------
$startDateOfPlan;
$endDateOfPlan;

$azubiManager = [];
foreach ($azubis as $azubi) {

    if (empty($startDateOfPlan) || $azubi->Ausbildungsstart < $startDateOfPlan) {
        $startDateOfPlan = $azubi->Ausbildungsstart;
    }

    if (empty($endDateOfPlan) || $azubi->Ausbildungsende > $endDateOfPlan) {
        $endDateOfPlan = $azubi->Ausbildungsende;
    }

    $standardplan;

    foreach ($standardplaene as $plan) {
        if ($azubi->ID_Ausbildungsberuf == $plan->ID_Ausbildungsberuf) {
            $standardplan = $plan;
            break;
        }
    }

    if (empty($standardplan)) return;

    $azubiManager[$azubi->ID] = new AzubiManager($azubi, $standardplan);
}

if (empty($startDateOfPlan) || empty($endDateOfPlan)) return;

$abteilungsManager = [];
foreach ($abteilungen as $abteilung) {
    $abteilungsManager[$abteilung->ID] = new AbteilungsManager($abteilung, "$startDateOfPlan $endDateOfPlan");
}

// Phase 1 - Präferenzierte Abteilungen --------------------------------------------------------------------------------
foreach ($azubiManager as $manager) {

    foreach ($manager->PraeferierteAbteilungen as $abteilung) {

        if (array_key_exists($abteilung->ID_Abteilung, $abteilungsManager)) {

            $anfrage = $manager->CreateAnfrage($abteilung->ID_Abteilung, $abteilung->Wochen);

            if ($abteilungsManager[$abteilung->ID_Abteilung]->HandleAnfrage($anfrage)) {
                $manager->AddToPlan($anfrage);
            }
        }
    }
}

// Phase 2 - Präferenzierte, aber optionale Abteilungen ----------------------------------------------------------------
foreach ($azubiManager as $manager) {

    if (empty($manager->Plan)) {

        foreach ($manager->PraeferierteOptionaleAbteilungen as $abteilung) {

            if (array_key_exists($abteilung->ID_Abteilung, $abteilungsManager)) {

                $anfrage = $manager->CreateAnfrage($abteilung->ID_Abteilung, $abteilung->Wochen);

                if ($abteilungsManager[$abteilung->ID_Abteilung]->HandleAnfrage($anfrage)) {
                    $manager->AddToPlan($anfrage);
                }
            }
        }
    }
}

// Phase 3 - Abteilungen -----------------------------------------------------------------------------------------------
foreach ($azubiManager as $manager) {

    foreach ($manager->UnmarkedAbteilungen as $abteilung) {

        if (array_key_exists($abteilung->ID_Abteilung, $abteilungsManager)) {

            $anfrage = $manager->CreateAnfrage($abteilung->ID_Abteilung, $abteilung->Wochen);

            if ($abteilungsManager[$abteilung->ID_Abteilung]->HandleAnfrage($anfrage)) {
                $manager->AddToPlan($anfrage);
            }
        }
    }
}

$test = 0;
