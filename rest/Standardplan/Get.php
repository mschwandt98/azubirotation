<?php
use core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER . "/DataHelper.php");

$helper = new DataHelper();
$standardplaene = $helper->GetStandardPlaene($_GET["id_ausbildungsberuf"] ?? null);

if (array_key_exists("id_ausbildungsberuf", $_GET) && !empty($_GET["id_ausbildungsberuf"])) {
    exit(json_encode(array_shift($standardplaene)));
}

exit(json_encode($standardplaene));
