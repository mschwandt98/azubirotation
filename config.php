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
define("BASE", __DIR__ . "/");

spl_autoload_register(function ($class) {

    $fileName = $class . ".php";

    if (file_exists(BASE . $fileName)) {
        include_once(BASE . $fileName);
    } else {
        $test = 0;
    }
});

/**
 * Prüft, ob der Benutzer eingeloggt ist.
 *
 * @return bool Status, ob der Benutzer eingeloggt ist.
 */
function is_logged_in() {
    if (array_key_exists("user_id", $_SESSION) && !empty($_SESSION["user_id"])) return true;
    return false;
}

/**
 * Prüft, ob ein CSRF-Token existiert und ob der CSRF-Token valide ist.
 *
 * @return bool Der Status, ob der CSRF-Token existiert bzw. valide ist.
 */
function is_token_valid() {
    if (!array_key_exists("csrfToken", $_POST)) return false;
    if (sanitize_string($_POST["csrfToken"]) === $_SESSION["csrf_token"]) return true;
    return false;
}

/**
 * Minmiert den hereingegebenden Code.
 *
 * @param string $code Der Code, der minimiert werden soll.
 *
 * @return string Der minimierte Code.
 */
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

/**
 * Säubert einen String; Entfernt Tags und wandelt HTML-Special-Chars um.
 *
 * @param string $data Der zu säubernde String.
 *
 * @param string Der gesäuberte String.
 */
function sanitize_string($data) {
    $data = strip_tags($data);
    return htmlspecialchars($data);
}
