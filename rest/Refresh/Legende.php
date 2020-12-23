<?php
/**
 * Legende.php
 *
 * Der API-Endpunkt zum Updaten der Legende.
 */

use core\helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . '/config.php');

$Abteilungen = (new DataHelper())->GetAbteilungen();
ob_start('minifier');
include_once(BASE . '/legende.php');
ob_end_flush();
