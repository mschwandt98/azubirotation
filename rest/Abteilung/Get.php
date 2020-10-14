<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Abteilungen, sortiert nach der Bezeichnung.
 */

use Core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER . "DataHelper.php");

$abteilungen = (new DataHelper())->GetAbteilungen();
exit(json_encode($abteilungen));
