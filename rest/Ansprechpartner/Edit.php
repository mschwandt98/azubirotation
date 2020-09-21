<?php
if (array_key_exists("id", $_POST) && array_key_exists("name", $_POST) && array_key_exists("email", $_POST) && array_key_exists("id_abteilung", $_POST)) {

    $id = intval($_POST["id"]);
    $name = $_POST["name"];
    $email = $_POST["email"];
    $id_abteilung = intval($_POST["id_abteilung"]);

    if (!empty($id) && !empty($name) && !empty($email) && !empty($id_abteilung)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "UPDATE " . T_ANSPRECHPARTNER . "
            SET Name = :name, Email = :email, ID_Abteilung = :id_abteilung
            WHERE ID = :id"
        );

        if ($statement->execute([
            ":id"           => $id,
            ":name"         => $name,
            ":email"        => $email,
            ":id_abteilung" => $id_abteilung])) {

            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
