<?php
use Core\Helper\DateHelper;
use Models\Plan;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(MODELS . "Plan.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("azubis", $_POST)) {

        $azubis = $_POST["azubis"];

        if (!empty($azubis)) {

            include_once(HELPER . "/DateHelper.php");

            global $pdo;

            foreach ($azubis as $azubi) {

                $id_azubi = sanitize_string($azubi["id"]);
                $phasen = [];

                foreach ($azubi["phasen"] as $phase) {

                    $startDate = sanitize_string($phase["date"]);
                    $endDate = DateHelper::NextSunday($startDate);

                    $phasen[] = new Plan(
                        $id_azubi,
                        sanitize_string($phase["id_ansprechpartner"]),
                        (empty($phase["id_abteilung"])) ? NULL : sanitize_string($phase["id_abteilung"]),
                        $startDate,
                        $endDate
                    );
                }

                $statement = $pdo->prepare("DELETE FROM " . T_PLAENE . " WHERE ID_Auszubildender = $id_azubi");

                if (!$statement->execute()) {
                    http_response_code(400);
                    exit;
                }

                $sql = "";

                foreach ($phasen as $phase) {

                    $sql .= "INSERT INTO " . T_PLAENE . "(ID_Auszubildender, ID_Ansprechpartner, ID_Abteilung, Startdatum, Enddatum)
                        VALUES (
                            $id_azubi, " .
                            ($phase->ID_Ansprechpartner ?? ":null") . ", " .
                            $phase->ID_Abteilung . ", '" .
                            $phase->Startdatum . "', '" .
                            $phase->Enddatum ."'
                        );";
                }

                $statement = $pdo->prepare($sql);

                if (!$statement->execute([ ":null" => NULL ])) {
                    http_response_code(400);
                    exit;
                }
            }

            http_response_code(200);
            ob_start("minifier");
            include_once(BASE . "/core/Plan.php");
            ob_end_flush();
            exit;
        }
    }
}

http_response_code(400);
