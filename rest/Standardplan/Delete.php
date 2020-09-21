<?php
if (array_key_exists("id_ausbildungsberuf", $_POST)) {

    $id = intval($_POST["id_ausbildungsberuf"]);

    if ($id !== 0 && !empty($id)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "DELETE FROM " . T_STANDARDPLAENE . " WHERE ID_Ausbildungsberuf = :id;"
        );

        if ($statement->execute([":id" => $id])) {
            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
