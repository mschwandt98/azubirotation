<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Ausbildungsberufe, sortiert nach der
 * Bezeichnung.
 *
 * TODO: SELECT-Anfrage gegen Helper ersetzen
 */

include_once(dirname(dirname(__DIR__)) . "/config.php");

global $pdo;

$sql_where = "";

if (array_key_exists("id", $_GET) && !empty($_GET["id"])) {
    $sql_where = " WHERE ID = " . intval(sanitize_string($_GET["id"]));
}

$statement = $pdo->prepare("SELECT * FROM " . T_AUSBILDUNGSBERUFE . $sql_where . " ORDER BY Bezeichnung ASC;");
$statement->execute();
$ausbildungsberufe = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($ausbildungsberufe));
