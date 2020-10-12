<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Abteilungen, sortiert nach der Bezeichnung.
 *
 * TODO: SELECT-Anfrage gegen Helper ersetzen
 */

include_once(dirname(dirname(__DIR__)) . "/config.php");

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_ABTEILUNGEN . " ORDER BY Bezeichnung ASC;");
$statement->execute();
$abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($abteilungen));
