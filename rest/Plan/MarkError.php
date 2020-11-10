<?php
/**
 * MarkError.php
 *
 * Markiert die Fehler als akzeptiert oder nicht akzeptiert.
 * Akzeptierte Fehler werden beim erneuten Test der Planung nicht angezeigt.
 */

session_start();
include_once(dirname(dirname(__DIR__)) . '/config.php');

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists('id_error', $_POST) && array_key_exists('bool', $_POST)) {

        global $pdo;

        $id = intval(sanitize_string($_POST['id_error']));
        $bool = filter_var(sanitize_string($_POST['bool']), FILTER_VALIDATE_BOOLEAN);

        $statement = $pdo->prepare(
            'UPDATE `errors`
            SET `Accepted` = :accept
            WHERE ID = :id;'
        );

        if ($statement->execute([ ':id' => $id, ':accept' => $bool ])) {
            http_response_code(200);
            exit;
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
exit;
