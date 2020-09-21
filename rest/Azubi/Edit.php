<?php
if (array_key_exists("id", $_POST) && array_key_exists("vorname", $_POST) && array_key_exists("nachname", $_POST) &&
    array_key_exists("email", $_POST) && array_key_exists("id_ausbildungsberuf", $_POST) &&
    array_key_exists("ausbildungsstart", $_POST) && array_key_exists("ausbildungsende", $_POST)) {

    $id = intval($_POST["id"]);
    $vorname = $_POST["vorname"];
    $nachname = $_POST["nachname"];
    $email = $_POST["email"];
    $id_ausbildungsberuf = intval($_POST["id_ausbildungsberuf"]);
    $ausbildungsstart = $_POST["ausbildungsstart"];
    $ausbildungsende = $_POST["ausbildungsende"];

    if (!empty($id) && !empty($vorname) && !empty($nachname) &&
        !empty($email) && !empty($id_ausbildungsberuf) &&
        !empty($ausbildungsstart) && !empty($ausbildungsende)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "UPDATE " . T_AUSZUBILDENDE . "
            SET Vorname = :vorname, Nachname = :nachname, Email = :email, ID_Ausbildungsberuf = :id_ausbildungsberuf, Ausbildungsstart = :ausbildungsstart, Ausbildungsende = :ausbildungsende
            WHERE ID = :id"
        );

        if ($statement->execute([
            ":id"                   => $id,
            ":vorname"              => $vorname,
            ":nachname"             => $nachname,
            ":email"                => $email,
            ":id_ausbildungsberuf"  => $id_ausbildungsberuf,
            "ausbildungsstart"      => $ausbildungsstart,
            "ausbildungsende"       => $ausbildungsende ])) {

            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
