<?php
/**
 * CreatePlan.php
 *
 * Erstellt eine Planung für einen Azubi.
 *
 * Für das Skript muss die Variable $azubi_id gesetzt sein.
 */

use Core\Helper\DataHelper;
use Core\Helper\DateHelper;
use Models\Plan;

if (empty($azubi_id)) return;
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

include_once(dirname(__DIR__) . "/config.php");

if (!is_logged_in()) return;

include_once(HELPER . "/DataHelper.php");
include_once(HELPER . "/DateHelper.php");
include_once(MODELS . "/Plan.php");

global $pdo;
$helper = new DataHelper();

$azubi = $helper->GetAzubis($azubi_id);
$ausbildungsberuf = $helper->GetAusbildungsberufe($Azubi->ID_Ausbildungsberuf);
$standardplan = array_shift($helper->GetStandardPlaene($azubi->ID_Ausbildungsberuf));

if (empty($standardplan)) return;

// Ansprechpartner, Plaene und Abteilungen werden als globals in dieser Datei genutzt
$Ansprechpartner = $helper->GetAnsprechpartner();
$Plaene = $helper->GetPlaene();
$Abteilungen = [];
foreach ($helper->GetAbteilungen() as $abteilung) {
    $Abteilungen[$abteilung->ID] = $abteilung;
}

$praeferierteAbteilungen    = [];
$normaleAbteilungen         = [];
$optionaleAbteilungen       = [];
foreach ($standardplan->Phasen as $phase) {

    if ($phase->Praeferieren && !$phase->Optional) {
        $praeferierteAbteilungen[] = $phase;
    } elseif ($phase->Praeferieren && $phase->Optional) {
        $praeferierteOptionaleAbteilungen[] = $phase;
    } elseif (!$phase->Praeferieren && !$phase->Optional) {
        $normaleAbteilungen[] = $phase;
    } elseif (!$phase->Praeferieren && $phase->Optional) {
        $optionaleAbteilungen[] = $phase;
    }
}

$abteilungen = [
    "Praeferierte"          => $praeferierteAbteilungen,
    "PraeferierteOptionale" => $praeferierteOptionaleAbteilungen,
    "Normale"               => $normaleAbteilungen,
    "Optionale"             => $optionaleAbteilungen
];

$plaene = [];

// TODO: Liste von Abteilungen führen, die noch nicht verplant wurden

if (!empty($abteilungen["Praeferierte"]) || !empty($abteilungen["PraeferierteOptionale"])) {

    $eingetragen = false; // anhand von $eingetragen später prüfen, ob eine präferierte Abteilung eingetragen wurde.
    foreach (array_merge($abteilungen["Praeferierte"], $abteilungen["PraeferierteOptionale"]) as $abteilung) {

        $ausbildungsStart = (DateHelper::IsMonday($azubi->Ausbildungsstart))
        ? $azubi->Ausbildungsstart
        : DateHelper::LastMonday($azubi->Ausbildungsstart);

        $startDate = $ausbildungsStart;
        $endDate = DateHelper::NextSunday($startDate);

        $zeitraeume = [];
        for ($i = 0; $i <= $abteilung->Wochen; $i++) {
            $zeitraeume[] = [ "Startdatum" => $startDate, "Enddatum" => $endDate];
            $startDate = DateHelper::NextMonday($startDate);
            $endDate = DateHelper::NextSunday($endDate);
        }

        $dateInXWeeks = DateHelper::GetDateInXWeeks(
            DateHelper::NextSunday($ausbildungsStart),
            $abteilung->Wochen
        );

        if ($dateInXWeeks > $azubi->Ausbildungsende) {
            $dateInXWeeks = $azubi->Ausbildungsende;
        }

        $alleWochenFrei = true;
        foreach ($zeitraeume as $zeitraum) {

            if ($zeitraum["Enddatum"] > $dateInXWeeks) {
                $alleWochenFrei = false;
                break;
            }

            if (!IsZeitraumInAbteilungFrei(
                DateHelper::BuildTimePeriodString($zeitraum["Startdatum"], $zeitraum["Enddatum"]),
                $abteilung->ID_Abteilung
            )) {
                $alleWochenFrei = false;
                break;
            }
        }

        if ($alleWochenFrei) {

            $eingetragen = true;
            $ansprechpartner = GetAnsprechpartnerFuerAbteilung($abteilung->ID_Abteilung);

            foreach ($zeitraeume as $zeitraum) {

                $plaene[] = new Plan(
                    $azubi->ID,
                    empty($ansprechpartner) ? null : $ansprechpartner->ID,
                    $abteilung->ID_Abteilung,
                    $zeitraum["Startdatum"],
                    $zeitraum["Enddatum"],
                    ""
                );
            }

            break;
        }
    }

    // Da keine Abteilung frei ist -> maximale Anzahl einer zufälligen präferierten Abteilung ignorieren
    if (!$eingetragen) {

        $randomAbteilung = $abteilungen["Praeferierte"][array_rand($abteilungen["Praeferierte"])];
        $ansprechpartner = GetAnsprechpartnerFuerAbteilung($abteilung->ID_Abteilung);

        $startDate = (DateHelper::IsMonday($azubi->Ausbildungsstart))
            ? $azubi->Ausbildungsstart
            : DateHelper::LastMonday($azubi->Ausbildungsstart);
        $endDate = DateHelper::NextSunday($startDate);

        for ($i = 0; $i <= $randomAbteilung->Wochen; $i++) {

            $startDate = DateHelper::NextMonday($startDate);
            $endDate = DateHelper::NextSunday($endDate);

            $plaene[] = new Plan(
                $azubi->ID,
                (empty($ansprechpartner)) ? null : $ansprechpartner->ID,
                $randomAbteilung->ID_Abteilung,
                $startDate,
                $endDate,
                ""
            );
        }
    }
}

// Pläne eintragen
$sql = "";

foreach ($plaene as $plan) {

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

/**
 * Ermittelt einen zufälligen Ansprechpartner, der für die angeforderte
 * Abteilung eingetragen ist.
 *
 * @param int $id_abteilung Die ID der Abteilung, für die der Ansprechpartner
 *                          eingetragen sein soll.
 *
 * @return Ansprechpartner Ein zufällig ausgewählter Ansprechpartner, der für
 *                         die angeforderte Abteilung eingetragen ist.
 */
function GetAnsprechpartnerFuerAbteilung($id_abteilung) {

    global $Ansprechpartner;

    $abteilungsAnsprechpartner = [];
    foreach ($Ansprechpartner as $ansprechpartner) {

        if ($ansprechpartner->ID_Abteilung === $id_abteilung) {
            $abteilungsAnsprechpartner[] = $ansprechpartner;
        }
    }

    return (empty($abteilungsAnsprechpartner))
        ? false
        : $abteilungsAnsprechpartner[array_rand($abteilungsAnsprechpartner)];
}

/**
 * Ermittelt die belegten Zeiträume in den einzelnen Abteilungen.
 * Ein Zeitraum gilt als belegt, sobald ein Azubi für diesen Zeitraum geplant
 * ist. Es heißt nicht, dass die maximale Anzahl an Azubis für die Abteilung
 * innerhalb dieses Zeitraums bereits erreicht ist.
 *
 * @return array Die belegten Zeiträume.
 *               Aufbau des Arrays:
 *               (ID Abteilung 1) => [
 *                   (Startdatum_1 Enddatum_2) => Anzahl an Azubis,
 *                   ...
 *                   (Startdatum_n Enddatum_n) => Anzahl an Azubis
 *               ],
 *               (ID Abteilung 2) => ...
 */
function GetBelegteZeitraeumeInAbteilungen() {

    global $Abteilungen;
    global $Plaene;

    $abteilungsPlaene = [];
    $belegteZeitraeume = []; // ... in Abteilungen
    foreach ($Abteilungen as $abteilung) {
        $abteilungsPlaene[$abteilung->ID] = [];
        $belegteZeitraeume[$abteilung->ID] = [];
    }

    foreach ($Plaene as $plan) {
        $abteilungsPlaene[$plan->ID_Abteilung][] = $plan;
    }

    // Belegte Zeiträume mit Anzahl an Azubis speichern
    foreach ($abteilungsPlaene as $id_abteilung => $plaene) {

        if (empty($plaene)) continue;

        foreach ($plaene as $plan) {

            $startDate = $plan->Startdatum;
            $endDate = $plan->Enddatum;
            $timePeriodString = DateHelper::BuildTimePeriodString($startDate, $endDate);

            if (empty($belegteZeitraeume[$id_abteilung])) {
                $belegteZeitraeume[$id_abteilung][$timePeriodString] = 1;
                continue;
            }

            $eingetragen = false;
            foreach ($belegteZeitraeume[$id_abteilung] as $zeitraum => $anzahlAzubis) {

                if ($timePeriodString === $zeitraum) {
                    $belegteZeitraeume[$id_abteilung][$zeitraum]++;
                    $eingetragen = true;
                }
            }

            if (!$eingetragen) {
                $belegteZeitraeume[$id_abteilung][$timePeriodString] = 1;
            }
        }
    }

    // Nach Datum sortieren
    foreach ($belegteZeitraeume as $id_abteilung => $value) {
        ksort($belegteZeitraeume[$id_abteilung]);
    }

    return $belegteZeitraeume;
}

/**
 * Untersucht, ob für den gegebenen Zeitraum die Abteilung die maximale Anzahl
 * an Azubis noch nicht erreicht hat, sprich ob sie noch weitere Azubis
 * ausbilden kann.
 *
 * @param string    $timePeriod     Der Zeitraum im Format "Y-m-d Y-m-d"
 *                                  (Startdatum[Leerzeichen]Enddatum).
 * @param int       $id_abteilung   Die ID der Abteilung, für die der Zeitraum
 *                                  überprüft werden soll.
 *
 * @return bool Der Status, ob die Abteilung noch weitere Azubis ausbilden kann.
 */
function IsZeitraumInAbteilungFrei($timePeriod, $id_abteilung) {

    global $Abteilungen;

    $abteilung = $Abteilungen[$id_abteilung];

    $zeitraeumeAbteilung = GetBelegteZeitraeumeInAbteilungen()[$id_abteilung];

        if (array_key_exists($timePeriod, $zeitraeumeAbteilung)) {

            if ($zeitraeumeAbteilung[$timePeriod] >= $abteilung->MaxAzubis) {
                return false;
            }
        }

    return true;
}
