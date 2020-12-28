<?php
/**
 * index.php
 *
 * Gibt die benötigte CSS-Datei minimiert und komprimiert zurück.
 */

session_start();
include_once('../../config.php');

ob_start('minifier');
header('Content-type: text/css; charset: UTF-8');
header('Cache-Control: public, max-age=31536000');

if (is_logged_in()) {
    readfile(BASE . 'assets/css/style.css');
} else {
    readfile(BASE . 'assets/css/public-style.css');
}

ob_end_flush();
