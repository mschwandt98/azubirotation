<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Abteilungen, sortiert nach der Bezeichnung.
 */

use core\helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . '/config.php');

$abteilungen = (new DataHelper())->GetAbteilungen();
exit(json_encode($abteilungen));
