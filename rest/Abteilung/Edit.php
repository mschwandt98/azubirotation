<?php
session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in()) {

    if (array_key_exists("id", $_POST) && array_key_exists("bezeichnung", $_POST) && array_key_exists("maxAzubis", $_POST) && array_key_exists("farbe", $_POST)) {

        $id = intval($_POST["id"]);
        $bezeichnung = $_POST["bezeichnung"];
        $maxAzubis = intval($_POST["maxAzubis"]);
        $farbe = $_POST["farbe"];

        if (!empty($id) && !empty($bezeichnung) && !empty($maxAzubis) && !empty($farbe)) {

            global $pdo;

            $statement = $pdo->prepare(
                "UPDATE " . T_ABTEILUNGEN . "
                SET Bezeichnung = :bezeichnung, MaxAzubis = :maxAzubis, Farbe = :farbe
                WHERE ID = :id"
            );

            if ($statement->execute([
                ":id"           => $id,
                ":bezeichnung"  => $bezeichnung,
                ":maxAzubis"    => $maxAzubis,
                ":farbe"        => $farbe])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
