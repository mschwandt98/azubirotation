<?php
use core\helper\DataHelper;

session_start();
include_once(dirname(dirname(__DIR__)) . "/config.php");

if (is_logged_in() && is_token_valid()) {

    $helper = new DataHelper();

    $ansprechpartner = $helper->GetAnsprechpartner();
    $azubis = $helper->GetAzubis();

    $emails = [];
    foreach ($ansprechpartner as $person) $emails[] = $person->Email;
    foreach ($azubis as $person) $emails[] = $person->Email;

    $empfaenger = implode(", ", $emails);
    $betreff = "=?utf-8?b?" . base64_encode("Änderungen an der Azubirotation") . "?=";
    $url = $_SERVER["HTTP_REFERER"];
    $nachricht = "
    <html>
    <head>
        <title>$betreff</title>
    </head>
    <body>
        <div>Es wurden Änderungen an der Azubirotation vorgenommen.</div>
        <div>
            Klicke <a href=\"$url\">hier</a>, um den aktuellen Ausbildungsplan dir anzuschauen.
        </div>
    </body>
    </html>
    ";

    $header[] = "MIME-Version: 1.0";
    $header[] = "Content-type: text/html; charset=utf-8";

    if (mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header))) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    exit;
}

http_response_code(401);
exit;
