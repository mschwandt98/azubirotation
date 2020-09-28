<?php
// globales Datenbankobjekt
$pdo = new PDO("mysql:dbname=azubirotation;host=localhost", "root", "");

// Datenbank-Tabellen
define("T_ABTEILUNGEN", "abteilungen");
define("T_ACCOUNTS", "accounts");
define("T_ANSPRECHPARTNER", "ansprechpartner");
define("T_AUSBILDUNGSBERUFE", "ausbildungsberufe");
define("T_AUSZUBILDENDE", "auszubildende");
define("T_PLAENE", "pläne");
define("T_STANDARDPLAENE", "standardpläne");

// Pfade
define("BASE", __DIR__);
define("HELPER", BASE . "/core/helper/");
define("MODELS", BASE . "/models/");

function include_models() {
    foreach (glob(MODELS . "*.php") as $filename) {
        include_once $filename;
    }
}

function is_logged_in() {

    if (array_key_exists("user_id", $_SESSION) && !empty($_SESSION["user_id"])) return true;

    return false;
}
