<?php
/**
 * index.php
 *
 * Gibt die benötigte JS-Datei minimiert und komprimiert zurück.
 */

session_start();
include_once('../../config.php');

ob_start('minifier');
header('Content-type: text/javascript; charset: UTF-8');
header('Cache-Control: public, max-age=31536000');

if (is_logged_in()) {
    readfile(BASE . 'assets/js/script.js');
} else {
    readfile(BASE . 'assets/js/public-script.js');
}

ob_end_flush();
