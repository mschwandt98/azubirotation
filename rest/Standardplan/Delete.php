<?php
/**
 * Delete.php
 *
 * Der API-Endpunkt zum Löschen eines Standardplans.
 */

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("id_ausbildungsberuf", $_POST)) {

        $id = intval(sanitize_string($_POST["id_ausbildungsberuf"]));

        if ($id !== 0 && !empty($id)) {

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
    exit;
}

http_response_code(401);
