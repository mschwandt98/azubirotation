<?php
use Core\Helper\DataHelper;
use Core\Helper\DateHelper;
use Models\Plan;

if (empty($azubi_id)) return;

include_once(dirname(__DIR__) . "/config.php");
include_once(HELPER . "DataHelper.php");
include_once(HELPER . "DateHelper.php");
include_once(MODELS . "Plan.php");

global $pdo;
$helper = new DataHelper();

$azubi          = $helper->GetAzubis(intval($azubi_id));
$standardplan   = array_shift($helper->GetStandardPlaene($azubi->ID_Ausbildungsberuf));

if (empty($standardplan)) return;

$ansprechpartner = $helper->GetAnsprechpartner();

$praeferierteAbteilungen    = [];
$normaleAbteilungen         = [];
$optionaleAbteilungen       = [];
foreach ($standardplan->Phasen as $phase) {

    if ($phase->Praeferieren && !$phase->Optional) {
        $praeferierteAbteilungen[] = $phase;
    } elseif (!$phase->Praeferieren && !$phase->Optional) {
        $normaleAbteilungen[] = $phase;
    } elseif ($phase->Optional) {
        $optionaleAbteilungen[] = $phase;
    }
}

$abteilungen = [
    "Praeferierte"  => $praeferierteAbteilungen,
    "Normale"       => $normaleAbteilungen,
    "Optionale"     => $optionaleAbteilungen
];

$phasen = [];
$startDate = $azubi->Ausbildungsstart;
foreach ($abteilungen as $unterteilung) {
    foreach ($unterteilung as $phase) {

        $ap;
        foreach ($ansprechpartner as $person) {

            if ($phase->ID_Abteilung === $person->ID_Abteilung) {
                $ap = $person->ID;
                break;
            }
        }

        if (empty($ap)) continue;

        if (!DateHelper::IsMonday($startDate)) {
            $startDate = DateHelper::LastMonday($startDate);
        }

        $endDate = DateHelper::NextSunday($startDate);

        for ($i = 0; $i < $phase->Wochen; $i++) {

            if ($startDate > $azubi->Ausbildungsende && $endDate > $azubi->Ausbildungsende) {
                break; break; break;
            }

            $phasen[] = new Plan(
                $azubi->ID,
                $ap,
                $phase->ID_Abteilung,
                $startDate,
                $endDate
            );

            $startDate = DateHelper::NextMonday($startDate);
            $endDate = DateHelper::NextSunday($startDate);
        }
    }
}

$sql = "";

foreach ($phasen as $phase) {

    $sql .= "INSERT INTO " . T_PLAENE . "(ID_Auszubildender, ID_Ansprechpartner, ID_Abteilung, Startdatum, Enddatum)
        VALUES (
            $phase->ID_Azubi, " .
            ($phase->ID_Ansprechpartner ?? ":null") . ", " .
            $phase->ID_Abteilung . ", '" .
            $phase->Startdatum . "', '" .
            $phase->Enddatum ."'
        );";
}

$statement = $pdo->prepare($sql);
if (!$statement->execute([ ":null" => NULL ])) {
    $error = $pdo->errorInfo();
}

$test = 0;
