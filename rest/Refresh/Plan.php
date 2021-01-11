<?php
/**
 * Plan.php
 *
 * Der API-Endpunkt zum Holen der aktuellen Version der Planung.
 */

include_once(dirname(dirname(__DIR__)) . '/config.php');

if (file_exists(BASE . '_cache/Plan.php')) {
    unlink(BASE . '_cache/Plan.php');
}

ob_start('minifier');
include_once(BASE . '/core/Plan.php');
ob_end_flush();
