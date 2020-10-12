<?php
/**
 * config.php
 *
 * Konfigurationsdatei, die am Anfang jeder PHP-Datei eingebunden werden sollte.
 */

// globales Datenbankobjekt
try {
    $pdo = new PDO("mysql:dbname=azubirotation;host=localhost", "root", "");
} catch (Exception $e) {
    exit($e->getMessage());
}

// Datenbank-Tabellen
define("T_ABTEILUNGEN", "abteilungen");
define("T_ACCOUNTS", "accounts");
define("T_ANSPRECHPARTNER", "ansprechpartner");
define("T_AUSBILDUNGSBERUFE", "ausbildungsberufe");
define("T_AUSZUBILDENDE", "auszubildende");
define("T_PLAENE", "pläne");
define("T_SETTINGS", "settings");
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

function is_token_valid() {
    if (!array_key_exists("csrfToken", $_POST)) return false;
    if (sanitize_string($_POST["csrfToken"]) === $_SESSION["csrf_token"]) return true;
    return false;
}

function minifier($code) {
    $search = [
        "/\>[^\S ]+/s",     // Remove whitespaces after tags
        "/[^\S ]+\</s",     // Remove whitespaces before tags
        "/(\s)+/s",         // Remove multiple whitespace sequences
        "/<!--(.|\s)*?-->/" // Removes comments
    ];
    $replace = [ ">", "<", "\\1" ];
    return preg_replace($search, $replace, $code);
}

function sanitize_string($data) {
    $data = strip_tags($data);
    return htmlspecialchars($data);
}
