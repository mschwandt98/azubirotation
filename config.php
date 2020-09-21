<?php
// globales Datenbankobjekt
$pdo = new PDO("mysql:dbname=azubirotation;host=localhost", "root", "");

// Datenbank-Tabellen
define("T_ABTEILUNGEN", "abteilungen");
define("T_ANSPRECHPARTNER", "ansprechpartner");
define("T_AUSBILDUNGSBERUFE", "ausbildungsberufe");
define("T_AUSZUBILDENDE", "auszubildende");
define("T_PLAENE", "pläne");
define("T_STANDARDPLAENE", "standardpläne");

// Pfade
define("BASE", __DIR__);
define("MODELS", BASE . "/models/");

function include_models() {
    foreach (glob(MODELS . "*.php") as $filename) {
        include_once $filename;
    }
}
