<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Azubis, sortiert nach dem Nachnamen.
 */

use Core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER. "DataHelper.php");

$auszubildende = (new DataHelper())->GetAzubis();
exit(json_encode($auszubildende));
