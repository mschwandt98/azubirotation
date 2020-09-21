<?php
if (array_key_exists("id_ausbildungsberuf", $_POST) && array_key_exists("phasen", $_POST)) {

    $id_ausbildungsberuf = intval($_POST["id_ausbildungsberuf"]);
    $phasen = $_POST["phasen"];

    if (!empty($id_ausbildungsberuf) && !empty($phasen)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "DELETE FROM " . T_STANDARDPLAENE . " WHERE ID_Ausbildungsberuf = :id_ausbildungsberuf;"
        );

        if ($statement->execute([ ":id_ausbildungsberuf" => $id_ausbildungsberuf ])) {
            $test = 0;
        }

        foreach ($phasen as $phase) {

            $statement = $pdo->prepare(
                "INSERT INTO " . T_STANDARDPLAENE . "(ID_Ausbildungsberuf, ID_Abteilung, AnzahlWochen)
                VALUES (:id_ausbildungsberuf, :id_abteilung, :wochen);"
            );

            if (!$statement->execute([
                ":id_ausbildungsberuf"  => $id_ausbildungsberuf,
                ":id_abteilung"         => intval($phase["id_abteilung"]),
                ":wochen"               => intval($phase["wochen"]) ])) {

                http_response_code(400);
                exit;
            }
        }

        exit;
    }
}

http_response_code(400);
