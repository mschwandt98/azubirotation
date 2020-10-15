<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Ansprechpartner, sortiert nach dem Namen.
 */

use core\helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");

$ansprechpartner = (new DataHelper())->GetAnsprechpartner();
exit(json_encode($ansprechpartner));
