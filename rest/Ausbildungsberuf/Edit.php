<?php
if (array_key_exists("id", $_POST) && array_key_exists("bezeichnung", $_POST)) {

    $id = intval($_POST["id"]);
    $bezeichnung = $_POST["bezeichnung"];

    if (!empty($id) && !empty($bezeichnung)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "UPDATE " . T_AUSBILDUNGSBERUFE . "
            SET Bezeichnung = :bezeichnung
            WHERE ID = :id"
        );

        if ($statement->execute([ ":id" => $id, ":bezeichnung" => $bezeichnung ])) {
            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
