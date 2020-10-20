<?php
/**
 * Add.php
 *
 * Der API-Endpunkt zum Hinzufügen eines Ausbildungsberufes.
 */

use models\Ausbildungsberuf;

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('bezeichnung', $_POST)) {

        $bezeichnung = sanitize_string($_POST['bezeichnung']);

        if (!empty($bezeichnung)) {

            global $pdo;
            $ausbildungsberuf = new Ausbildungsberuf($bezeichnung);

            $statement = $pdo->prepare(
                'INSERT INTO ' . T_AUSBILDUNGSBERUFE . '(Bezeichnung)
                VALUES (:bezeichnung);'
            );

            if ($statement->execute([ ':bezeichnung' => $ausbildungsberuf->Bezeichnung ])) {
                http_response_code(200);
                exit;
            }
        }
    }
    http_response_code(400);
    exit;
}

http_response_code(401);
