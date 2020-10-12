<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Ansprechpartner, sortiert nach dem Namen.
 *
 * TODO: SELECT-Anfrage gegen Helper ersetzen
 */

include_once(dirname(dirname(__DIR__)) . "/config.php");

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_ANSPRECHPARTNER . " ORDER BY `Name` ASC;");
$statement->execute();
$ansprechpartner = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($ansprechpartner));
