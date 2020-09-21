<?php
if (array_key_exists("id", $_POST) && array_key_exists("bezeichnung", $_POST) && array_key_exists("maxAzubis", $_POST) && array_key_exists("farbe", $_POST)) {

    /**
     * @var int ID
     */
    $id = intval($_POST["id"]);
    /**
     * @var string Bezeichnung
     */
    $bezeichnung = $_POST["bezeichnung"];
    /**
     * @var int Maximale Anzahl an Azubis
     */
    $maxAzubis = intval($_POST["maxAzubis"]);
    /**
     * @var string HEX-Farben-Code
     */
    $farbe = $_POST["farbe"];

    if (!empty($id) && !empty($bezeichnung) && !empty($maxAzubis) && !empty($farbe)) {

        include_once(dirname(dirname(__DIR__)) . "/config.php");

        global $pdo;

        $statement = $pdo->prepare(
            "UPDATE " . T_ABTEILUNGEN . "
            SET Bezeichnung = :bezeichnung, MaxAzubis = :maxAzubis, Farbe = :farbe
            WHERE ID = :id"
        );

        if ($statement->execute([
            ":id"           => $id,
            ":bezeichnung"  => $bezeichnung,
            ":maxAzubis"    => $maxAzubis,
            ":farbe"        => $farbe])) {

            http_response_code(200);
            exit;
        }
    }
}

http_response_code(400);
