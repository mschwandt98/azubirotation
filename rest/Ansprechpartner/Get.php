<?php
define("BASE", dirname(dirname(__DIR__)));
include_once(BASE . "/config.php");
include_once(BASE . "/models/Ansprechpartner.php");

use Models\Ansprechpartner;

global $pdo;

$statement = $pdo->prepare("SELECT * FROM " . T_ANSPRECHPARTNER . " ORDER BY `Name` ASC;");
$statement->execute();
$ansprechpartner = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($ansprechpartner));
