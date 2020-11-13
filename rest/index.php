<?php
/**
 * index.php
 *
 * REST-API-Endpunkt, der die Daten der Planung im JSON-Format zurückgibt.
 */

use core\helper\DataHelper;
use core\helper\DateHelper;

include_once('../config.php');

$helper             = new DataHelper();
$Abteilungen        = $helper->GetAbteilungen();
$Ansprechpartner    = $helper->GetAnsprechpartner();
$Azubis             = $helper->GetAzubis();
$Plaene             = $helper->GetPlaene();

$abteilungen = [];
foreach ($Abteilungen as $abteilung) {
    $abteilungen[$abteilung->ID] = $abteilung;
}

$ansprechpartner = [];
foreach ($Ansprechpartner as $ap) {
    $ansprechpartner[$ap->ID] = $ap;
}

$jsonData = [];
foreach ($Azubis as $azubi) {

    $firstName = $azubi->Vorname;
    $lastName = $azubi->Nachname;

    $azubiPlaene = [];
    foreach ($Plaene as $plan) {

        if ($plan->ID_Azubi === $azubi->ID) {

            $azubiPlaene[] = [
                'Startdatum'        => $plan->Startdatum,
                'Enddatum'          => $plan->Enddatum,
                'Abteilung'         => $plan->ID_Abteilung,
                'Ansprechpartner'   => $plan->ID_Ansprechpartner
            ];
        }
    }

    $lastAbteilung          = 0;
    $lastAnsprechpartner    = 0;
    $lastEnddate            = null;
    $summedUpPlaene         = [];
    foreach ($azubiPlaene as $plan) {

        if (!empty($summedUpPlaene)) {

            if (DateHelper::DayAfter($lastEnddate) === $plan['Startdatum']) {

                if ($lastAbteilung === $plan['Abteilung'] && $lastAnsprechpartner === $plan['Ansprechpartner']) {

                    end($summedUpPlaene);
                    $key = key($summedUpPlaene);
                    $summedUpPlaene[$key]['Enddatum']   = $plan['Enddatum'];
                    $lastAbteilung                      = $plan['Abteilung'];
                    $lastAnsprechpartner                = $plan['Ansprechpartner'];
                    $lastEnddate                        = $plan['Enddatum'];
                    continue;
                }
            }
        }

        $lastAbteilung          = $plan['Abteilung'];
        $lastAnsprechpartner    = $plan['Ansprechpartner'];
        $lastEnddate            = $plan['Enddatum'];
        $summedUpPlaene[]       = $plan;
    }

    foreach ($summedUpPlaene as $key => $plan) {
        $summedUpPlaene[$key]['Abteilung'] = $abteilungen[$plan['Abteilung']]->Bezeichnung;

        if (!empty($ansprechpartner[$plan['Ansprechpartner']])) {
            $summedUpPlaene[$key]['Ansprechpartner'] = $ansprechpartner[$plan['Ansprechpartner']]->Name;
        }
    }

    $jsonData[] = [
        'Vorname'   => $azubi->Vorname,
        'Nachname'  => $azubi->Nachname,
        'Plaene'    => $summedUpPlaene
    ];
}

echo json_encode($jsonData);
