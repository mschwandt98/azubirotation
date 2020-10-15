<?php
/**
 * Edit.php
 *
 * Der API-Endpunkt zum Bearbeiten einer Abteilung.
 */

use models\Abteilung;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("id", $_POST) && array_key_exists("bezeichnung", $_POST) && array_key_exists("maxAzubis", $_POST) && array_key_exists("farbe", $_POST)) {

        $id             = sanitize_string($_POST["id"]);
        $bezeichnung    = sanitize_string($_POST["bezeichnung"]);
        $maxAzubis      = sanitize_string($_POST["maxAzubis"]);
        $farbe          = sanitize_string($_POST["farbe"]);

        if (!empty($id) && !empty($bezeichnung) && !empty($maxAzubis) && !empty($farbe)) {

            global $pdo;
            $abteilung = new Abteilung($bezeichnung, $maxAzubis, $farbe, $id);

            $statement = $pdo->prepare(
                "UPDATE " . T_ABTEILUNGEN . "
                SET Bezeichnung = :bezeichnung, MaxAzubis = :maxAzubis, Farbe = :farbe
                WHERE ID = :id"
            );

            if ($statement->execute([
                ":id"           => $abteilung->ID,
                ":bezeichnung"  => $abteilung->Bezeichnung,
                ":maxAzubis"    => $abteilung->MaxAzubis,
                ":farbe"        => $abteilung->Farbe ])) {

                http_response_code(200);
                exit;
            }
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
