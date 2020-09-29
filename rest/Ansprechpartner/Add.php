<?php
use Models\Ansprechpartner;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(MODELS . "Ansprechpartner.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("name", $_POST) && array_key_exists("email", $_POST) && array_key_exists("id_abteilung", $_POST)) {

        $name           = sanitize_string($_POST["name"]);
        $email          = sanitize_string($_POST["email"]);
        $id_abteilung   = sanitize_string($_POST["id_abteilung"]);

        if (!empty($name) && !empty($email) && !empty($id_abteilung)) {

            global $pdo;
            $ansprechpartner = new Ansprechpartner($name, $email, $id_abteilung);

            $statement = $pdo->prepare(
                "INSERT INTO " . T_ANSPRECHPARTNER . "(Name, Email, ID_Abteilung)
                VALUES (:name, :email, :id_abteilung);"
            );

            if ($statement->execute([
                ":name"         => $ansprechpartner->Name,
                ":email"        => $ansprechpartner->Email,
                ":id_abteilung" => $ansprechpartner->ID_Abteilung ])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
