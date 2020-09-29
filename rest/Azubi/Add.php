<?php
session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("vorname", $_POST) && array_key_exists("nachname", $_POST) &&
        array_key_exists("email", $_POST) && array_key_exists("id_ausbildungsberuf", $_POST) &&
        array_key_exists("ausbildungsstart", $_POST) && array_key_exists("ausbildungsende", $_POST)) {

        $vorname = $_POST["vorname"];
        $nachname = $_POST["nachname"];
        $email = $_POST["email"];
        $id_ausbildungsberuf = intval($_POST["id_ausbildungsberuf"]);
        $ausbildungsstart = $_POST["ausbildungsstart"];
        $ausbildungsende = $_POST["ausbildungsende"];

        if (!empty($vorname) && !empty($nachname) &&
            !empty($email) && !empty($id_ausbildungsberuf) &
            !empty($ausbildungsstart) && !empty($ausbildungsende)) {

            global $pdo;

            $statement = $pdo->prepare(
                "INSERT INTO " . T_AUSZUBILDENDE . "(Vorname, Nachname, Email, ID_Ausbildungsberuf, Ausbildungsstart, Ausbildungsende)
                VALUES (:vorname, :nachname, :email, :id_ausbildungsberuf, :ausbildungsstart, :ausbildungsende);"
            );

            if ($statement->execute([
                ":vorname"              => $vorname,
                ":nachname"             => $nachname,
                ":email"                => $email,
                ":id_ausbildungsberuf"  => $id_ausbildungsberuf,
                ":ausbildungsstart"     => $ausbildungsstart,
                ":ausbildungsende"      => $ausbildungsende ])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
