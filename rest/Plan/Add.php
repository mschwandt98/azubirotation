<?php
if (array_key_exists("azubis", $_POST)) {

    $azubis = $_POST["azubis"];

    if (!empty($azubis)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        foreach ($azubis as $azubi) {

            $id_azubi = intval($azubi["id"]);
            $phasen = [];

            foreach ($azubi["phasen"] as $phase) {

                $endDate = date("Y-m-d", strtotime($phase["date"] . " next sunday"));

                $phasen[] = [
                    "Startdatum" => $phase["date"],
                    "Enddatum" => $endDate,
                    "ID_Abteilung" => intval($phase["id_abteilung"]),
                    "ID_Ansprechpartner" => (empty($phase["id_ansprechpartner"])) ? NULL : intval($phase["id_ansprechpartner"])
                ];
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
                        ($phase["ID_Ansprechpartner"] ?? ":null" ) . ", " .
                        $phase["ID_Abteilung"] . ", '" .
                        $phase["Startdatum"] . "', '" .
                        $phase["Enddatum"] ."'
                    );";
            }

            $statement = $pdo->prepare($sql);

            if (!$statement->execute([ ":null" => NULL ])) {
                http_response_code(400);
                exit;
            }
        }

        http_response_code(200);
        // TODO: Plan zurückgeben
        exit();
    }
}

http_response_code(400);
