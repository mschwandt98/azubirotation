<?php
define("BASE", dirname(dirname(__DIR__)));
include_once(BASE . "/config.php");
include_once(BASE . "/models/Phase.php");
include_once(BASE . "/models/Standardplan.php");

use Models\Standardplan;
use Models\Phase;

global $pdo;

$sql_where = "";

if (array_key_exists("id_ausbildungsberuf", $_GET)) {
    $sql_where = " WHERE ab.ID = " . intval($_GET["id_ausbildungsberuf"]);
}

$statement = $pdo->prepare(
    "SELECT sp.*, ab.Bezeichnung AS Ausbildungsberuf, a.Bezeichnung AS Abteilung
    FROM standardpläne sp
    JOIN ausbildungsberufe ab ON sp.ID_Ausbildungsberuf = ab.ID
    JOIN abteilungen a ON sp.ID_Abteilung = a.ID
    $sql_where
    ORDER BY ab.Bezeichnung ASC;"
);

$statement->execute();
$standardplan_phasen = $statement->fetchAll(PDO::FETCH_ASSOC);

$standardplaene = [];

foreach ($standardplan_phasen as $phase) {

    $phase = (object) $phase;

    $needed_phase = new Phase(
        $phase->ID_Abteilung,
        $phase->Abteilung,
        $phase->AnzahlWochen
    );

    if (array_key_exists($phase->Ausbildungsberuf, $standardplaene)) {
        $standardplaene[$phase->Ausbildungsberuf]->Phasen[] = $needed_phase;
    } else {
        $standardplaene[$phase->Ausbildungsberuf] = new Standardplan(
            $phase->ID_Ausbildungsberuf,
            $phase->Ausbildungsberuf,
            [ $needed_phase ]
        );
    }
}

if (array_key_exists("id_ausbildungsberuf", $_GET) && !empty($_GET["id_ausbildungsberuf"])) {
    exit(json_encode(array_shift($standardplaene)));
}

exit(json_encode($standardplaene));
