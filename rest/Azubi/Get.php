<?php
define("BASE", dirname(dirname(__DIR__)));
include_once(BASE . "/config.php");
include_once(BASE . "/models/Auszubildender.php");

use Models\Auszubildender;

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_AUSZUBILDENDE . " ORDER BY `Nachname` ASC;");
$statement->execute();
$auszubildende = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($auszubildende));
