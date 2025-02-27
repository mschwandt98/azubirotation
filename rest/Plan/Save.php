<?php
/**
 * Save.php
 *
 * Der API-Endpunkt zum Aktualisieren der Daten aus der Planung.
 */

use core\helper\DataHelper;
use core\helper\DateHelper;
use models\Plan;

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('azubis', $_POST)) {

        $azubis = $_POST['azubis'];

        if (!empty($azubis)) {

            global $pdo;
            $dataHelper = new DataHelper();
            foreach ($azubis as $azubi) {

                $id_azubi = sanitize_string($azubi['id']);
                $deletedPhasen = []; // beinhaltet die Startdaten
                foreach ($azubi['phasen'] as $phase) {

                    $startDate = sanitize_string($phase['date']);

                    if (empty($phase['id_abteilung'])) {
                        $deletedPhasen[] = $startDate;
                    } else {

                        $id_abteilung = sanitize_string($phase['id_abteilung']);
                        $endDate = DateHelper::NextSunday($startDate);

                        if (empty($phase['id_ansprechpartner'])) {
                            $ansprechpartner_id = NULL;
                        } else {
                            $ansprechpartner_id = sanitize_string($phase['id_ansprechpartner']);

                            // Überprüfung, ob Ansprechpartner für die Abteilung erlaubt ist
                            if ($ansprechpartner = $dataHelper->GetAnsprechpartner($ansprechpartner_id)) {

                                if (!empty($ansprechpartner->ID_Abteilung) && $ansprechpartner->ID_Abteilung != $id_abteilung) {
                                    $ansprechpartner_id = NULL;
                                }
                            }
                        }

                        $plan = new Plan(
                            $id_azubi,
                            $ansprechpartner_id,
                            $id_abteilung,
                            $startDate,
                            $endDate
                        );

                        $replacements = [
                            ':id_azubi'     => $plan->ID_Azubi,
                            ':startDate'    => $plan->Startdatum,
                            ':endDate'      => $plan->Enddatum
                        ];

                        $statement = $pdo->prepare(
                            'SELECT * FROM ' . T_PLAENE . '
                                WHERE ID_Auszubildender = :id_azubi AND
                                Startdatum = :startDate AND
                                Enddatum = :endDate;'
                        );
                        $statement->execute($replacements);

                        if ($result = $statement->fetch(PDO::FETCH_ASSOC)) {

                            $id_plan = intval($result['ID']);
                            $replacements = [
                                ':id'                   => $id_plan,
                                ':id_ansprechpartner'   => $plan->ID_Ansprechpartner,
                                ':id_abteilung'         => $plan->ID_Abteilung
                            ];

                            $statement = $pdo->prepare(
                                'UPDATE ' . T_PLAENE . '
                                SET ID_Ansprechpartner = :id_ansprechpartner,
                                    ID_Abteilung = :id_abteilung
                                WHERE ID = :id;'
                            );

                            if (!$statement->execute($replacements)) {
                                http_response_code(400);
                                exit;
                            }
                        } else {

                            $replacements = [
                                ':id_azubi'             => $plan->ID_Azubi,
                                ':id_ansprechpartner'   => $plan->ID_Ansprechpartner,
                                ':id_abteilung'         => $plan->ID_Abteilung,
                                ':startDate'            => $plan->Startdatum,
                                ':endDate'              => $plan->Enddatum
                            ];

                            $statement = $pdo->prepare(
                                'INSERT ' . T_PLAENE . '
                                (
                                    ID_Auszubildender,
                                    ID_Ansprechpartner,
                                    ID_Abteilung,
                                    Startdatum,
                                    Enddatum
                                )
                                VALUES (
                                    :id_azubi,
                                    :id_ansprechpartner,
                                    :id_abteilung,
                                    :startDate,
                                    :endDate
                                );'
                            );

                            if (!$statement->execute($replacements)) {
                                http_response_code(400);
                                exit;
                            }

                            $id_plan = $pdo->lastInsertId();
                        }

                        $termin = sanitize_string($phase['termin']);
                        $termin_separat = sanitize_string($phase['termin_separat']);
                        if (!empty($termin) || !empty($termin_separat)) {

                            $separat = !empty($termin_separat);

                            $statement = $pdo->prepare(
                                'SELECT *
                                FROM ' . T_TERMINE . '
                                WHERE ID_Plan = :id_plan'
                            );

                            $statement->execute([ ':id_plan' => $id_plan ]);

                            if ($result = $statement->fetch(PDO::FETCH_ASSOC)) {

                                $statement = $pdo->prepare('
                                    UPDATE ' . T_TERMINE . '
                                    SET Bezeichnung = :bezeichnung,
                                        Separat = :separat
                                    WHERE ID = :id_termin
                                ');

                                $statement->execute([
                                    ':bezeichnung'  => ($separat) ? $termin_separat : $termin,
                                    ':separat'      => $separat,
                                    ':id_termin'    => intval($result['ID'])
                                ]);

                            } else {

                                $statement = $pdo->prepare(
                                    'INSERT INTO ' . T_TERMINE . '
                                    (Bezeichnung, Separat, ID_Plan)
                                    VALUES (:bezeichnung, :separat, :id_plan);'
                                );

                                if (!$statement->execute([
                                    ':bezeichnung'  => ($separat) ? $termin_separat : $termin,
                                    ':separat'      => $separat,
                                    ':id_plan'      => $id_plan
                                ])) {
                                    http_response_code(400);
                                    exit;
                                }
                            }
                        } elseif (empty($termin) && empty($termin_separat)) {

                            $statement = $pdo->prepare(
                                'DELETE FROM ' . T_TERMINE . '
                                WHERE ID_Plan = :id_plan'
                            );
                            $statement->execute([ ':id_plan' => $id_plan ]);
                        }
                    }
                }

                if (!empty($deletedPhasen)) {

                    $statement = $pdo->prepare(
                        'DELETE FROM ' . T_PLAENE . '
                        WHERE ID_Auszubildender = ' . $id_azubi . ' AND '.
                            "Startdatum IN ('" . implode("','", $deletedPhasen) . "');"
                    );

                    if (!$statement->execute()) {

                        $test = $statement->errorInfo();

                        http_response_code(400);
                        exit;
                    }
                }
            }

            // Timestamp des letzten Updates speichern
            $dataHelper->UpdateSetting('last-time-updated' , DateHelper::Today());

            include_once(BASE . 'backup.php');
            unlink(BASE . '_cache/Plan.php');

            http_response_code(200);
            ob_start('minifier');
            include_once(BASE . 'core/Plan.php');
            ob_end_flush();
            exit;
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
