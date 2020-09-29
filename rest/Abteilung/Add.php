<?php
use Models\Abteilung;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(MODELS . "Abteilung.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("bezeichnung", $_POST) && array_key_exists("maxAzubis", $_POST) && array_key_exists("farbe", $_POST)) {

        $bezeichnung    = sanitize_string($_POST["bezeichnung"]);
        $maxAzubis      = sanitize_string($_POST["maxAzubis"]);
        $farbe          = sanitize_string($_POST["farbe"]);

        if (!empty($bezeichnung) && !empty($maxAzubis) && !empty($farbe)) {

            global $pdo;
            $abteilung = new Abteilung($bezeichnung, $maxAzubis, $farbe);

            $statement = $pdo->prepare(
                "INSERT INTO " . T_ABTEILUNGEN . "(Bezeichnung, MaxAzubis, Farbe)
                VALUES (:bezeichnung, :maxAzubis, :farbe);"
            );

            if ($statement->execute([
                ":bezeichnung"   => $abteilung->Bezeichnung,
                ":maxAzubis"     => $abteilung->MaxAzubis,
                ":farbe"         => $abteilung->Farbe ])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
