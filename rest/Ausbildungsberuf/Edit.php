<?php
/**
 * Edit.php
 *
 * Der API-Endpunkt zum Bearbeiten eines Ausbildungsberufes.
 */

use models\Ausbildungsberuf;

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('id', $_POST) && array_key_exists('bezeichnung', $_POST)) {

        $id             = sanitize_string($_POST['id']);
        $bezeichnung    = sanitize_string($_POST['bezeichnung']);

        if (!empty($id) && !empty($bezeichnung)) {

            global $pdo;
            $ausbildungsberuf = new Ausbildungsberuf($bezeichnung, $id);

            $statement = $pdo->prepare(
                'UPDATE ' . T_AUSBILDUNGSBERUFE . '
                SET Bezeichnung = :bezeichnung
                WHERE ID = :id'
            );

            if ($statement->execute([
                ':id'           => $ausbildungsberuf->ID,
                ':bezeichnung'  => $ausbildungsberuf->Bezeichnung ])) {

                http_response_code(200);
                exit;
            }
        }
    }

    http_response_code(400);
}

http_response_code(401);
