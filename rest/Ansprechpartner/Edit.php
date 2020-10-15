<?php
/**
 * Edit.php
 *
 * Der API-Endpunkt zum Bearbeiten eines Ansprechpartners.
 */

use models\Ansprechpartner;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("id", $_POST) && array_key_exists("name", $_POST) && array_key_exists("email", $_POST) && array_key_exists("id_abteilung", $_POST)) {

        $id             = sanitize_string($_POST["id"]);
        $name           = sanitize_string($_POST["name"]);
        $email          = sanitize_string($_POST["email"]);
        $id_abteilung   = sanitize_string($_POST["id_abteilung"]);

        if (!empty($id) && !empty($name) && !empty($email) && !empty($id_abteilung)) {

            global $pdo;
            $ansprechpartner = new Ansprechpartner($name, $email, $id_abteilung, $id);

            $statement = $pdo->prepare(
                "UPDATE " . T_ANSPRECHPARTNER . "
                SET Name = :name, Email = :email, ID_Abteilung = :id_abteilung
                WHERE ID = :id"
            );

            if ($statement->execute([
                ":id"           => $ansprechpartner->ID,
                ":name"         => $ansprechpartner->Name,
                ":email"        => $ansprechpartner->Email,
                ":id_abteilung" => $ansprechpartner->ID_Abteilung ])) {

                http_response_code(200);
                exit;
            }
        }
    }

    http_response_code(400);
}

http_response_code(401);
