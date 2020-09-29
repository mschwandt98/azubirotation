<?php
use Models\Auszubildender;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(MODELS . "Auszubildender.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("vorname", $_POST) && array_key_exists("nachname", $_POST) &&
        array_key_exists("email", $_POST) && array_key_exists("id_ausbildungsberuf", $_POST) &&
        array_key_exists("ausbildungsstart", $_POST) && array_key_exists("ausbildungsende", $_POST)) {

        $vorname                = sanitize_string($_POST["vorname"]);
        $nachname               = sanitize_string($_POST["nachname"]);
        $email                  = sanitize_string($_POST["email"]);
        $id_ausbildungsberuf    = sanitize_string($_POST["id_ausbildungsberuf"]);
        $ausbildungsstart       = sanitize_string($_POST["ausbildungsstart"]);
        $ausbildungsende        = sanitize_string($_POST["ausbildungsende"]);

        if (!empty($vorname) && !empty($nachname) &&
            !empty($email) && !empty($id_ausbildungsberuf) &
            !empty($ausbildungsstart) && !empty($ausbildungsende)) {

            global $pdo;
            $azubi = new Auszubildender($vorname, $nachname, $email, $id_ausbildungsberuf, $ausbildungsstart, $ausbildungsende);

            $statement = $pdo->prepare(
                "INSERT INTO " . T_AUSZUBILDENDE . "(Vorname, Nachname, Email, ID_Ausbildungsberuf, Ausbildungsstart, Ausbildungsende)
                VALUES (:vorname, :nachname, :email, :id_ausbildungsberuf, :ausbildungsstart, :ausbildungsende);"
            );

            if ($statement->execute([
                ":vorname"              => $azubi->Vorname,
                ":nachname"             => $azubi->Nachname,
                ":email"                => $azubi->Email,
                ":id_ausbildungsberuf"  => $azubi->ID_Ausbildungsberuf,
                ":ausbildungsstart"     => $azubi->Ausbildungsstart,
                ":ausbildungsende"      => $azubi->Ausbildungsende ])) {

                http_response_code(200);
                exit;
            }
        }
    }
}

http_response_code(400);
