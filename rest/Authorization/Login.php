<?php
/**
 * Login.php
 *
 * Der API-Endpunkt zum Anmelden.
 */

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (!is_logged_in()) {

    if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST)) {

        $username = sanitize_string($_POST["username"]);
        $password = sanitize_string($_POST["password"]);

        global $pdo;

        $statement = $pdo->prepare("SELECT * FROM " . T_ACCOUNTS . " WHERE Username = '$username';");
        $statement->execute();
        $account = $statement->fetch(PDO::FETCH_ASSOC);

        if (!empty($account)) {

            if (password_verify($password, $account["Password"])) {
                $_SESSION["user_id"] = $account["ID"];
                $_SESSION["csrf_token"] = uniqid("", true);
                http_response_code(200);
                exit;
            }
        }

        http_response_code(401);
        exit;
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
