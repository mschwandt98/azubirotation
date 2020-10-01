<?php
use Core\Helper\DataHelper;
use Core\Helper\DateHelper;
use Models\Plan;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("azubis", $_POST)) {

        $azubis = $_POST["azubis"];

        if (!empty($azubis)) {

            include_once(MODELS . "Plan.php");
            include_once(HELPER . "DataHelper.php");
            include_once(HELPER . "DateHelper.php");

            global $pdo;

            foreach ($azubis as $azubi) {

                $id_azubi = sanitize_string($azubi["id"]);
                $phasen = [];

                foreach ($azubi["phasen"] as $phase) {

                    $startDate = sanitize_string($phase["date"]);
                    $endDate = DateHelper::NextSunday($startDate);

                    if (empty($phase["id_abteilung"])) {
                        continue;
                    }

                    if (empty($phase["id_ansprechpartner"])) {
                        $ansprechpartner_id = NULL;
                    } else {
                        $ansprechpartner_id = sanitize_string($phase["id_ansprechpartner"]);

                        if ($ansprechpartner = (new DataHelper())->GetAnsprechpartner($ansprechpartner_id)) {

                            if (!empty($ansprechpartner->ID_Abteilung) && $ansprechpartner->ID_Abteilung != $phase["id_abteilung"]) {
                                $ansprechpartner_id = NULL;
                            }
                        }
                    }

                    $phasen[] = new Plan(
                        $id_azubi,
                        $ansprechpartner_id,
                        sanitize_string($phase["id_abteilung"]),
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

                if (!empty($sql)) {
                    $statement = $pdo->prepare($sql);

                    if (!$statement->execute([ ":null" => NULL ])) {
                        http_response_code(400);
                        exit;
                    }
                }
            }

            http_response_code(200);
            ob_start("minifier");
            include_once(BASE . "/core/Plan.php");
            ob_end_flush();
            exit;
        }
    }

    http_response_code(400);
    exit;
}

http_response_code(401);
