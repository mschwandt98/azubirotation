<?php
use Core\Helper\DataHelper;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(HELPER . "DataHelper.php");

$Abteilungen = (new DataHelper())->GetAbteilungen();
ob_start("minifier");
include_once(BASE . "/footer.php");
ob_end_flush();
