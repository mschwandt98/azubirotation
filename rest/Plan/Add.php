<?php
/**
 * Add.php
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
                            if ($ansprechpartner = (new DataHelper())->GetAnsprechpartner($ansprechpartner_id)) {

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
                            $endDate,
                            (!empty($phase['termin'])) ? sanitize_string($phase['termin']) : ''
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

                            $replacements = [
                                ':id'                   => $result['ID'],
                                ':id_ansprechpartner'   => $plan->ID_Ansprechpartner,
                                ':id_abteilung'         => $plan->ID_Abteilung,
                                ':termin'               => $plan->Termin
                            ];

                            $statement = $pdo->prepare(
                                'UPDATE ' . T_PLAENE . '
                                SET ID_Ansprechpartner = :id_ansprechpartner,
                                    ID_Abteilung = :id_abteilung,
                                    Termin = :termin
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
                                ':endDate'              => $plan->Enddatum,
                                ':termin'               => $plan->Termin
                            ];

                            $statement = $pdo->prepare(
                                'INSERT ' . T_PLAENE . '
                                (
                                    ID_Auszubildender,
                                    ID_Ansprechpartner,
                                    ID_Abteilung,
                                    Startdatum,
                                    Enddatum,
                                    Termin
                                )
                                VALUES (
                                    :id_azubi,
                                    :id_ansprechpartner,
                                    :id_abteilung,
                                    :startDate,
                                    :endDate,
                                    :termin
                                );'
                            );

                            if (!$statement->execute($replacements)) {
                                http_response_code(400);
                                exit;
                            }
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
                        http_response_code(400);
                        exit;
                    }
                }
            }

            http_response_code(200);
            ob_start('minifier');
            include_once(BASE . '/core/Plan.php');
            ob_end_flush();
            exit;
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
