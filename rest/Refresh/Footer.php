<?php
/**
 * Footer.php
 *
 * Der API-Endpunkt zum Updaten des Footers bzw der Legende im Footer.
 */

use Core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");

$Abteilungen = (new DataHelper())->GetAbteilungen();
ob_start("minifier");
include_once(BASE . "/footer.php");
ob_end_flush();
