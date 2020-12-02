<?php
/**
 * config.php
 *
 * Konfigurationsdatei, die am Anfang jeder PHP-Datei eingebunden werden sollte.
 */

// DB-Zugang
define('DB_DATABASE', 'ausbildungsplaner');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

// globales Datenbankobjekt
try {
    $pdo = new PDO('mysql:dbname=' . DB_DATABASE .  ';host=' . DB_HOST, DB_USER, DB_PASS);
} catch (Exception $e) {
    exit($e->getMessage());
}

// Datenbank-Tabellen
define('T_ABTEILUNGEN', 'abteilungen');
define('T_ACCOUNTS', 'accounts');
define('T_ANSPRECHPARTNER', 'ansprechpartner');
define('T_AUSBILDUNGSBERUFE', 'ausbildungsberufe');
define('T_AUSZUBILDENDE', 'auszubildende');
define('T_ERRORS', 'errors');
define('T_PLAENE', 'plaene');
define('T_SETTINGS', 'settings');
define('T_STANDARDPLAENE', 'standardplaene');
define('T_TERMINE', 'termine');

// Pfade
define('BASE', __DIR__ . '/');

spl_autoload_register(function ($class) {

    $fileName = strtr($class, "\\", '/') . '.php';

    if (file_exists(BASE . $fileName)) {
        include_once(BASE . $fileName);
    }
});

/**
 * Prüft, ob der Benutzer eingeloggt ist.
 *
 * @return bool Status, ob der Benutzer eingeloggt ist.
 */
function is_logged_in() {
    if (array_key_exists('user_id', $_SESSION) && !empty($_SESSION['user_id'])) return true;
    return false;
}

/**
 * Prüft, ob ein CSRF-Token existiert und ob der CSRF-Token valide ist.
 *
 * @return bool Der Status, ob der CSRF-Token existiert bzw. valide ist.
 */
function is_token_valid() {
    if (!array_key_exists('csrfToken', $_POST)) return false;
    if (sanitize_string($_POST['csrfToken']) === $_SESSION['csrf_token']) return true;
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
        "/<!--(.|\s)*?-->/" // Remove comments
    ];
    $replace = [ '>', '<', "\\1" ];
    $minified_code = preg_replace($search, $replace, $code);

    if (array_key_exists('HTTP_ACCEPT_ENCODING', $_SERVER) && !empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {

        $compressionMethods = explode(', ', $_SERVER['HTTP_ACCEPT_ENCODING']);

        if (in_array('gzip', $compressionMethods)) {
            header('Content-Encoding: gzip');
            return gzencode($minified_code);
        }
    }

    return $minified_code;
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
    $data = htmlspecialchars($data, ENT_HTML5);
    return str_replace('&amp;', '&', $data);
}
