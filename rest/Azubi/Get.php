<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Azubis, sortiert nach dem Nachnamen.
 */

use core\helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . '/config.php');

$auszubildende = (new DataHelper())->GetAzubis();
exit(json_encode($auszubildende));
