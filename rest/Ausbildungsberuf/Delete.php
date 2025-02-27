<?php
/**
 * Delete.php
 *
 * Der API-Endpunkt zum Löschen eines Ausbildungsberufes.
 */

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('id', $_POST)) {

        $id = intval(sanitize_string($_POST['id']));

        if ($id !== 0 && !empty($id)) {

            global $pdo;

            ($pdo->prepare(
                'DELETE FROM ' . T_STANDARDPLAENE . ' WHERE ID_Ausbildungsberuf = :id;'
            ))->execute([ ':id' => $id ]);

            ($pdo->prepare(
                'UPDATE ' . T_AUSZUBILDENDE . ' SET ID_Ausbildungsberuf = :null WHERE ID_Ausbildungsberuf = :id;'
            ))->execute([ ':null' => NULL, ':id' => $id ]);

            $statement = $pdo->prepare('DELETE FROM ' . T_AUSBILDUNGSBERUFE . ' WHERE ID = :id;');
            if ($statement->execute([ ':id' => $id ])) {
                http_response_code(200);
                exit;
            }
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
