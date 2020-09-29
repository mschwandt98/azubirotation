<?php
include_once(dirname(dirname(__DIR__)) . "/config.php");

ob_start("minifier");
include_once(BASE . "/core/Plan.php");
ob_end_flush();
