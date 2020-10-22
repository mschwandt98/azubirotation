<?php
/**
 * Get.php
 *
 * Der API-Endpunkt zum Holen aller Standardpläne.
 */

use core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . '/config.php');

$id_ausbildungsberuf = array_key_exists('id_ausbildungsberuf', $_GET)
    ? sanitize_string($_GET['id_ausbildungsberuf'])
    : null;

$helper = new DataHelper();
$standardplaene = $helper->GetStandardPlaene($id_ausbildungsberuf);

if (!empty($id_ausbildungsberuf)) {
    exit(json_encode(array_shift($standardplaene)));
}

exit(json_encode($standardplaene));
