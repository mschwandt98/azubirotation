<?php
define("BASE", dirname(dirname(__DIR__)));
include_once(BASE . "/config.php");
include_once(BASE . "/models/Abteilung.php");

use Models\Abteilung;

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_ABTEILUNGEN . " ORDER BY Bezeichnung ASC;");
$statement->execute();
$abteilungen = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($abteilungen));
