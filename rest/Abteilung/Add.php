<?php
if (array_key_exists("bezeichnung", $_POST) && array_key_exists("maxAzubis", $_POST) && array_key_exists("farbe", $_POST)) {

    $bezeichnung = $_POST["bezeichnung"];
    $maxAzubis = intval($_POST["maxAzubis"]);
    $farbe = $_POST["farbe"];

    if (!empty($bezeichnung) && !empty($maxAzubis) && !empty($farbe)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "INSERT INTO " . T_ABTEILUNGEN . "(Bezeichnung, MaxAzubis, Farbe)
            VALUES (:bezeichnung, :maxAzubis, :farbe);"
        );

        if ($statement->execute([
            ":bezeichnung"   => $bezeichnung,
            ":maxAzubis"     => $maxAzubis,
            ":farbe"         => $farbe])) {

            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
