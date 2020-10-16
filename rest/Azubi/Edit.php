<?php
/**
 * Edit.php
 *
 * Der API-Endpunkt zum Bearbeiten eines Azubis.
 */

use models\Auszubildender;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("id", $_POST) && array_key_exists("vorname", $_POST) && array_key_exists("nachname", $_POST) &&
        array_key_exists("email", $_POST) && array_key_exists("id_ausbildungsberuf", $_POST) &&
        array_key_exists("ausbildungsstart", $_POST) && array_key_exists("ausbildungsende", $_POST) &&
        array_key_exists("planung_erstellen", $_POST)) {

        $id                     = sanitize_string($_POST["id"]);
        $vorname                = sanitize_string($_POST["vorname"]);
        $nachname               = sanitize_string($_POST["nachname"]);
        $email                  = sanitize_string($_POST["email"]);
        $id_ausbildungsberuf    = sanitize_string($_POST["id_ausbildungsberuf"]);
        $ausbildungsstart       = sanitize_string($_POST["ausbildungsstart"]);
        $ausbildungsende        = sanitize_string($_POST["ausbildungsende"]);
        $planungErstellen       = sanitize_string($_POST["planung_erstellen"]);

        if (!empty($id) && !empty($vorname) && !empty($nachname) &&
            !empty($email) && !empty($id_ausbildungsberuf) &&
            !empty($ausbildungsstart) && !empty($ausbildungsende)) {

            global $pdo;
            $azubi = new Auszubildender($vorname, $nachname, $email, $id_ausbildungsberuf, $ausbildungsstart, $ausbildungsende, $id);

            $statement = $pdo->prepare(
                "UPDATE " . T_AUSZUBILDENDE . "
                SET Vorname = :vorname, Nachname = :nachname, Email = :email, ID_Ausbildungsberuf = :id_ausbildungsberuf, Ausbildungsstart = :ausbildungsstart, Ausbildungsende = :ausbildungsende
                WHERE ID = :id"
            );

            if ($statement->execute([
                ":id"                   => $azubi->ID,
                ":vorname"              => $azubi->Vorname,
                ":nachname"             => $azubi->Nachname,
                ":email"                => $azubi->Email,
                ":id_ausbildungsberuf"  => $azubi->ID_Ausbildungsberuf,
                "ausbildungsstart"      => $azubi->Ausbildungsstart,
                "ausbildungsende"       => $azubi->Ausbildungsende ])) {

                if (filter_var($planungErstellen, FILTER_VALIDATE_BOOLEAN)) {

                    $statement = $pdo->prepare(
                        "DELETE FROM " . T_PLAENE . "
                        WHERE ID_Auszubildender = :id"
                    );

                    if (!$statement->execute([ ":id" => $azubi->ID ])) {
                        http_response_code(400);
                        exit;
                    }

                    $azubi_id = $azubi->ID;
                    include_once(BASE . "/core/CreatePlan.php");
                }

                http_response_code(200);
                exit;
            }
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
