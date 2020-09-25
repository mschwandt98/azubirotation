<?php
if (array_key_exists("username", $_POST) && array_key_exists("password", $_POST)) {

    session_start();

    $username = $_POST["username"];
    $password = $_POST["password"];

    include_once(dirname(dirname(__DIR__)) . "/config.php");

    global $pdo;

    $statement = $pdo->prepare("SELECT * FROM " . T_ACCOUNTS . " WHERE Username = '$username';");
    $statement->execute();
    $account = $statement->fetch(PDO::FETCH_ASSOC);

    if (!empty($account)) {

        if ($username === $account["Username"] && $password === $account["Password"]) {
            $_SESSION["user_id"] = $account["ID"];
            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
exit;
