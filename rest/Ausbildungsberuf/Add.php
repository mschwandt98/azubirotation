<?php
if (array_key_exists("bezeichnung", $_POST)) {

    /**
     * @var string Bezeichnung
     */
    $bezeichnung = $_POST["bezeichnung"];

    if (!empty($bezeichnung)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "INSERT INTO " . T_AUSBILDUNGSBERUFE . "(Bezeichnung)
            VALUES (:bezeichnung);"
        );

        if ($statement->execute([ ":bezeichnung"   => $bezeichnung ])) {
            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
