<?php
define("BASE", dirname(dirname(__DIR__)));
include_once(BASE . "/config.php");
include_once(BASE . "/models/Ausbildungsberuf.php");

use Models\Ausbildungsberuf;

global $pdo;

$sql_where = "";

if (array_key_exists("id", $_GET) && !empty($_GET["id"])) {
    $sql_where = " WHERE ID = " . intval($_GET["id"]);
}

$statement = $pdo->prepare("SELECT * FROM " . T_AUSBILDUNGSBERUFE . $sql_where . " ORDER BY Bezeichnung ASC;");
$statement->execute();
$ausbildungsberufe = $statement->fetchAll(PDO::FETCH_ASSOC);
exit(json_encode($ausbildungsberufe));
