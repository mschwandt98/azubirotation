<?php
session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    if (array_key_exists("id_ausbildungsberuf", $_POST) && array_key_exists("phasen", $_POST)) {

        $id_ausbildungsberuf = intval($_POST["id_ausbildungsberuf"]);
        $phasen = $_POST["phasen"];

        if (!empty($id_ausbildungsberuf) && !empty($phasen)) {

            global $pdo;

            $statement = $pdo->prepare(
                "DELETE FROM " . T_STANDARDPLAENE . " WHERE ID_Ausbildungsberuf = :id_ausbildungsberuf;"
            );
            $statement->execute([ ":id_ausbildungsberuf" => $id_ausbildungsberuf ]);

            foreach ($phasen as $phase) {

                $statement = $pdo->prepare(
                    "INSERT INTO " . T_STANDARDPLAENE . "(ID_Ausbildungsberuf, ID_Abteilung, AnzahlWochen, Praeferieren, Optional)
                    VALUES (:id_ausbildungsberuf, :id_abteilung, :wochen, :praeferieren, :optional);"
                );

                $phase["praeferieren"] = filter_var($phase["praeferieren"], FILTER_VALIDATE_BOOLEAN);
                $phase["optional"] = filter_var($phase["optional"], FILTER_VALIDATE_BOOLEAN);

                if (!$statement->execute([
                    ":id_ausbildungsberuf"  => $id_ausbildungsberuf,
                    ":id_abteilung"         => intval($phase["id_abteilung"]),
                    ":wochen"               => intval($phase["wochen"]),
                    ":praeferieren"         => $phase["praeferieren"],
                    ":optional"             => $phase["optional"] ])) {

                    http_response_code(400);
                    exit;
                }
            }

            exit;
        }
    }
}

http_response_code(400);
