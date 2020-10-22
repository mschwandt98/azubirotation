<?php
/**
 * Add.php
 *
 * Der API-Endpunkt zum Hinzufügen eines Standardplans.
 */

use models\Phase;
use core\helper\DataHelper;

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('id_ausbildungsberuf', $_POST) && array_key_exists('phasen', $_POST)) {

        $id_ausbildungsberuf = intval(sanitize_string($_POST['id_ausbildungsberuf']));
        $phasen = $_POST['phasen'];

        if (!empty($id_ausbildungsberuf) && !empty($phasen)) {

            global $pdo;

            foreach ($phasen as $phase) {

                $phase = new Phase(
                    sanitize_string($phase['id_abteilung']),
                    (new DataHelper())->GetAbteilungen($phase['id_abteilung'])->Bezeichnung,
                    sanitize_string($phase['wochen']),
                    sanitize_string($phase['praeferieren']),
                    sanitize_string($phase['optional'])
                );

                $statement = $pdo->prepare(
                    'INSERT INTO ' . T_STANDARDPLAENE . '(ID_Ausbildungsberuf, ID_Abteilung, AnzahlWochen, Praeferieren, Optional)
                    VALUES (:id_ausbildungsberuf, :id_abteilung, :wochen, :praeferieren, :optional);'
                );

                if (!$statement->execute([
                    ':id_ausbildungsberuf'  => $id_ausbildungsberuf,
                    ':id_abteilung'         => $phase->ID_Abteilung,
                    ':wochen'               => $phase->Wochen,
                    ':praeferieren'         => $phase->Praeferieren,
                    ':optional'             => $phase->Optional ])) {

                    http_response_code(400);
                    exit;
                }
            }

            http_response_code(200);
            exit;
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
