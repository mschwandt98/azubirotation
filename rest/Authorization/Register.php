<?php
session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (!is_logged_in()) {

    if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST) && array_key_exists("passwordRepeated", $_POST)) {

        $username = sanitize_string($_POST["username"]);
        $password = sanitize_string($_POST["password"]);
        $passwordRepeated = sanitize_string($_POST["passwordRepeated"]);

        if (!empty($username) && !empty($password) && !empty($passwordRepeated)) {

            $backButtonString = '<form action="' . $_SERVER["HTTP_REFERER"] . '"><input type="submit" value="Zurück" /></form>';
            if ($password === $passwordRepeated) {

                $password = password_hash($password, PASSWORD_DEFAULT);

                global $pdo;
                if (($pdo->prepare("INSERT INTO " . T_ACCOUNTS . " (Username, Password) VALUES('$username', '$password')"))->execute()) {
                    $_SESSION["user_id"] = $pdo->lastInsertId();
                    $_SESSION["csrf_token"] = uniqid("", true);
                    $url = explode("/register", $_SERVER["HTTP_REFERER"])[0];
                    header("Location: $url");
                    exit;
                } else {
                    echo $backButtonString;
                    echo "Der Username existiert bereits. Wähle einen anderen aus.";
                    exit;
                }
            } else {
                echo $backButtonString;
                echo "Die Passwörter stimmen nicht überein. Wiederhole den Vorgang";
                exit;
            }
        }
    }
} else {

    if (array_key_exists("allowRegistration", $_POST)) {

        $value = sanitize_string($_POST["allowRegistration"]);
        global $pdo;

        ($pdo->prepare(
            "UPDATE " . T_SETTINGS . " SET value = '$value' WHERE name = 'allow-registration';"
        ))->execute();

        $url = explode("/register", $_SERVER["HTTP_REFERER"])[0];
        header("Location: $url");
    }
}
