<?php
use Core\Helper;
use DateTime;

include_once(dirname(__DIR__) . "/config.php");
include_once("Helper.php");
include_models();

$helper = new Helper();

$abteilungen        = $helper->GetAbteilungen();
$ansprechpartner    = $helper->GetAnsprechpartner();
$ausbildungsberufe  = $helper->GetAusbildungsberufe();
$azubis             = $helper->GetAzubis();
$standardplaene     = $helper->GetStandardPlaene();

$planung = [];

foreach ($azubis as $azubi) {

    $startDatum = new DateTime($azubi->Ausbildungsstart);
    $endDatum = new DateTime($azubi->Ausbildungsstart);

    foreach ($ausbildungsberufe as $ausbildungsberuf) {

        if ($ausbildungsberuf->ID === $azubi->ID_Ausbildungsberuf ) {
            $beruf = $ausbildungsberuf;
            break;
        }
    }

    if (empty($beruf)) {
        return;
    }


}
