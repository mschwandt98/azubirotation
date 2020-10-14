<?php
/**
 * CreatePlan.php
 *
 * Erstellt eine Planung für einen Azubi.
 *
 * Für das Skript muss die Variable $azubi_id gesetzt sein.
 */

use Core\Helper\DataHelper;
use Core\Helper\PlanungsHelper;

if (empty($azubi_id)) return;
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

include_once(dirname(__DIR__) . "/config.php");

if (!is_logged_in()) return;

global $pdo;
$dataHelper = new DataHelper();

$azubi          = $dataHelper->GetAzubis($azubi_id);
$standardplan   = array_shift($dataHelper->GetStandardPlaene($azubi->ID_Ausbildungsberuf));

if (empty($standardplan)) return;

$planungsHelper = new PlanungsHelper($azubi);

// Abteilungen unterteilen
// Abteilungen sind keine Abteilungen nach dem Model Abteilung, sondern die Phasen des Standardplans
$praeferierteAbteilungen            = [];
$praeferierteOptionaleAbteilungen   = [];
$normaleAbteilungen                 = [];
$optionaleAbteilungen               = [];
foreach ($standardplan->Phasen as $phase) {

    if ($phase->Praeferieren && !$phase->Optional) {
        $praeferierteAbteilungen[$phase->ID_Abteilung] = $phase;
    } elseif ($phase->Praeferieren && $phase->Optional) {
        $praeferierteOptionaleAbteilungen[$phase->ID_Abteilung] = $phase;
    } elseif (!$phase->Praeferieren && !$phase->Optional) {
        $normaleAbteilungen[$phase->ID_Abteilung] = $phase;
    } elseif (!$phase->Praeferieren && $phase->Optional) {
        $optionaleAbteilungen[$phase->ID_Abteilung] = $phase;
    }
}

shuffle($praeferierteAbteilungen);
shuffle($praeferierteOptionaleAbteilungen);

$abteilungen = [
    "Praeferierte"          => $praeferierteAbteilungen,
    "PraeferierteOptionale" => $praeferierteOptionaleAbteilungen,
    "Normale"               => $normaleAbteilungen,
    "Optionale"             => $optionaleAbteilungen
];

if (!empty($abteilungen["Praeferierte"]) || !empty($abteilungen["PraeferierteOptionale"])) {

    $mergedAbteilungen = array_merge($abteilungen["Praeferierte"], $abteilungen["PraeferierteOptionale"]);
    $planungsHelper->PlanStartOfAusbildung($mergedAbteilungen);
}

if (!empty($abteilungen["Normale"])) {
    $planungsHelper->PlanAbteilungen($abteilungen["Normale"]);
}

if (end($planungsHelper->Plaene)->Enddatum < $azubi->Ausbildungsende) {
    $planungsHelper->PlanAbteilungen($abteilungen["Optionale"]);
}

if (!empty($planungsHelper->AbteilungenLeft)) {
    $planungsHelper->PlanLeftAbteilungen();
}

// Pläne eintragen
$sql = "";

foreach ($planungsHelper->Plaene as $plan) {

    $sql .= "INSERT INTO " . T_PLAENE . "(ID_Auszubildender, ID_Ansprechpartner, ID_Abteilung, Startdatum, Enddatum)
        VALUES (
            $plan->ID_Azubi, " .
            ($plan->ID_Ansprechpartner ?? ":null") . ", " .
            $plan->ID_Abteilung . ", '" .
            $plan->Startdatum . "', '" .
            $plan->Enddatum ."'
        );";
}

$statement = $pdo->prepare($sql);
$statement->execute([ ":null" => NULL ]);
