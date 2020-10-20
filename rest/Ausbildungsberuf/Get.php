<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Ausbildungsberufe, sortiert nach der
 * Bezeichnung.
 */

use core\helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . '/config.php');

$ausbildungsberufe = (new DataHelper())->GetAusbildungsberufe(
    (array_key_exists('id', $_GET) && !empty($_GET['id'])) ? sanitize_string($_GET['id']) : null
);
exit(json_encode($ausbildungsberufe));
