<?php
session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("name", $_POST) && array_key_exists("email", $_POST) && array_key_exists("id_abteilung", $_POST)) {

        $name = $_POST["name"];
        $email = $_POST["email"];
        $id_abteilung = intval($_POST["id_abteilung"]);

        if (!empty($name) && !empty($email) && !empty($id_abteilung)) {

            global $pdo;

            $statement = $pdo->prepare(
                "INSERT INTO " . T_ANSPRECHPARTNER . "(Name, Email, ID_Abteilung)
                VALUES (:name, :email, :id_abteilung);"
            );

            if ($statement->execute([
                ":name"         => $name,
                ":email"        => $email,
                ":id_abteilung" => $id_abteilung ])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
