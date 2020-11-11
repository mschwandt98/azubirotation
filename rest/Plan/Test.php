<?php
/**
 * Test.php
 *
 * Testet die Planung auf Richtlinienverstöße. Bei diesen Richtlinienverstößen
 * wird geschaut, ob:
 *      - ein Azubi länger als im Standardplan vorgesehen in einer Abteilung ist
 *      - eine Abteilung mehr Azubis als erlaubt zugeordnet sind
 *      - ein Azubi außerhalb seines Ausbildungszeitraums verplant ist
 *      - ein Azubi am Anfang seiner Ausbildung in einer nicht präferierten
 *        Abteilung verplant ist
 */

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
$errors = SaveErrors($errors);
DeleteOldErrors($errors['jsonStrings']);

$errors = $errors['errors'];
if (empty($errors)) {
    exit(true);
}

ob_start('minifier');
include_once(BASE . '/templates/PlanErrors.php');
ob_end_flush();
exit;

/**
 * Löscht nicht mehr existierende Fehler aus der Datenbank.
 *
 * @param array $errors Die JSON-String der Fehler unterteilt nach den
 *                      Error-Codes.
 */
function DeleteOldErrors($errors) {

    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM ' . T_ERRORS);
    $statement->execute();
    $dbErrors = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dbErrors as $dbError) {

        $exists = false;
        foreach ($errors as $errorcode => $jsonStrings) {

            if ($errorcode === intval($dbError['ErrorCode'])) {
                foreach ($jsonStrings as $json) {

                    if ($json === $dbError['JSON']) {
                        $exists = true;
                        break 2;
                    }
                }
            }
        }

        if (!$exists) {

            ($pdo->prepare(
                'DELETE FROM ' . T_ERRORS . ' WHERE ID = :id;'
            ))->execute([ ':id' => intval($dbError['ID']) ]);
        }
    }
}

/**
 * Die gefundenen Fehler werden in der Datenbank gespeichert. Sofern dieser
 * Fehler bereits in der Datenbank existiert und als "akzeptiert"
 * gekennzeichnet ist, wird dieser Fehler aus der Liste entfernt.
 *
 * @param array $errors Eine Liste aller gefunden Fehler.
 *
 * @return array Eine Liste alle Fehler, die nicht als "akzeptiert"
 *               gekennzeichnet sind und aller JSON-Strings, unterteilt nach den
 *               Error-Codes.
 *               [
 *                   'jsonStrings' => [],
 *                   'errors'      => []
 *               ]
 */
function SaveErrors($errors) {

    global $pdo;

    $jsonStrings = [];
    foreach ($errors as $errorcode => $errorList) {

        $jsonStrings[$errorcode] = [];

        $statement = $pdo->prepare('SELECT * FROM ' . T_ERRORS . ' WHERE ErrorCode = :errorcode');
        $statement->execute([ ':errorcode' => $errorcode ]);
        $dbErrors = $statement->fetchAll(PDO::FETCH_ASSOC);

        switch ($errorcode) {
            case PlanErrorCodes::Ausbildungszeitraum:

                foreach ($errorList as $id_azubi => $zeitraeume) {

                    foreach ($zeitraeume as $error_id => $zeitraum) {
                        $jsonString = json_encode([
                            'id_azubi' => $id_azubi,
                            'zeitraum' => $zeitraum
                        ]);
                        $jsonStrings[$errorcode][] = $jsonString;

                        $errorExists = false;
                        $id = ErrorIsAccepted($dbErrors, $jsonString);
                        if ($id !== false) {

                            $errorExists = true;

                            if ($id !== true) {
                                $errors[$errorcode][$id_azubi]['id-' . $id] = $zeitraum;
                            }
                        }

                        if (!$errorExists) {

                            $dbErrorId = strval(InsertError($errorcode, $jsonString));
                            $errors[$errorcode][$id_azubi][$dbErrorId] = $zeitraum;
                        }

                        unset($errors[$errorcode][$id_azubi][$error_id]);
                    }

                    if (empty($errors[$errorcode][$id_azubi])) {
                        unset($errors[$errorcode][$id_azubi]);
                    }
                }

                break;
            case PlanErrorCodes::AbteilungenMaxAzubis:

                foreach ($errorList as $id_abteilung => $zeitraeume) {

                    foreach ($zeitraeume as $zeitraum => $anzahlAzubis) {

                        $jsonString = json_encode([
                            'id_abteilung'  => $id_abteilung,
                            'anzahlAzubis'  => $anzahlAzubis,
                            'zeitraum'      => $zeitraum
                        ]);
                        $jsonStrings[$errorcode][] = $jsonString;

                        $errorExists = false;
                        $id = ErrorIsAccepted($dbErrors, $jsonString);
                        if ($id !== false) {

                            $errorExists = true;

                            if ($id !== true) {
                                $errors[$errorcode][$id_abteilung]['id-' . $id] = [
                                    'zeitraum'      => $zeitraum,
                                    'anzahlAzubis'  => $anzahlAzubis
                                ];
                            }
                        }

                        if (!$errorExists) {

                            $dbErrorId = strval(InsertError($errorcode, $jsonString));
                            $errors[$errorcode][$id_abteilung]['id-' . $dbErrorId] = [
                                'zeitraum'      => $zeitraum,
                                'anzahlAzubis'  => $anzahlAzubis
                            ];
                        }

                        unset($errors[$errorcode][$id_abteilung][$zeitraum]);

                        if (empty($errors[$errorcode][$id_abteilung])) {
                            unset($errors[$errorcode][$id_abteilung]);
                        }
                    }
                }

                break;
            case PlanErrorCodes::PraeferierteAbteilungen:
            case PlanErrorCodes::WochenInAbteilungen:

                foreach ($errorList as $id_azubi => $abteilungen) {

                    foreach ($abteilungen as $key => $id_abteilung) {

                        $jsonString = json_encode([
                            'id_abteilung'  => $id_abteilung,
                            'id_azubi'      => $id_azubi
                        ]);
                        $jsonStrings[$errorcode][] = $jsonString;

                        $errorExists = false;
                        $id = ErrorIsAccepted($dbErrors, $jsonString);
                        if ($id !== false) {

                            $errorExists = true;

                            if ($id !== true) {
                                $errors[$errorcode][$id_azubi]['id-' . $id] = $id_abteilung;
                            }
                        }

                        if (!$errorExists) {

                            $dbErrorId = strval(InsertError($errorcode, $jsonString));
                            $errors[$errorcode][$id_azubi]['id-' . $dbErrorId] = $id_abteilung;
                        }

                        unset($errors[$errorcode][$id_azubi][$key]);

                        if (empty($errors[$errorcode][$id_azubi])) {
                            unset($errors[$errorcode][$id_azubi]);
                        }
                    }
                }

                break;
            default:
                continue 2;
        }

        if (empty($errors[$errorcode])) {
            unset($errors[$errorcode]);
        }
    }

    return [
        'jsonStrings'   => $jsonStrings,
        'errors'        => $errors
    ];
}

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

/**
 * Prüft, ob ein Fehler als "akzeptiert" gekennzeichnet ist.
 *
 * @param array     $dbErrors   Die in der Datenbank gespeicherten Fehler.
 * @param string    $jsonString Die Daten des Fehlers.
 *
 * @return bool|string True, wenn der Fehler als "akzeptiert" gekennzeichet ist.
 *                     False, wenn der Fehler in der Datenbank nicht existiert.
 *                     String - Die Datenbank-ID des Fehler als String.
 */
function ErrorIsAccepted($dbErrors, $jsonString) {

    foreach ($dbErrors as $dbError) {

        if ($dbError['JSON'] === $jsonString) {

            if ($dbError['Accepted'] == true) {
                return true;
            }

            return strval($dbError['ID']);
        }
    }

    return false;
}

/**
 * Speichert einen Fehler in der Datenbank.
 *
 * @param int       $errorcode  Der Fehlercode (Klasse PlanErrorCodes).
 * @param string    $jsonString Die Daten des Fehlers.
 *
 * @return int Die Datenbank-ID des gespeicherten Fehlers.
 */
function InsertError($errorcode, $jsonString) {

    global $pdo;

    $statement = $pdo->prepare(
        'INSERT INTO errors
        (ErrorCode, `JSON`, Accepted)
        VALUES (:errorCode, :jsonString, :accepted)'
    );

    $statement->execute([
        ':errorCode'    => $errorcode,
        ':jsonString'   => $jsonString,
        ':accepted'     => false
    ]);

    return $pdo->lastInsertId();
}
