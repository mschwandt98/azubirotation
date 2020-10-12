<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Azubis, sortiert nach dem Nachnamen.
 *
 * TODO: SELECT-Anfrage gegen Helper ersetzen
 */

include_once(dirname(dirname(__DIR__)) . "/config.php");

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_AUSZUBILDENDE . " ORDER BY `Nachname` ASC;");
$statement->execute();
$auszubildende = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($auszubildende));
