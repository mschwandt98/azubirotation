<?php
use Core\Helper;

include_once(dirname(dirname(__DIR__)) . "/config.php");
include_once(BASE . "/core/Helper.php");

$helper = new Helper();

$ansprechpartner = $helper->GetAnsprechpartner();
$azubis = $helper->GetAzubis();

$emails = [];
foreach ($ansprechpartner as $person) $emails[] = $person->Email;
foreach ($azubis as $person) $emails[] = $person->Email;

$empfaenger = implode(", ", $emails);
$betreff = "Änderungen an der Azubirotation";
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

$header[] = 'MIME-Version: 1.0';
$header[] = 'Content-type: text/html; charset=iso-8859-1';

if (mail($empfaenger, $betreff, $nachricht, implode("\r\n", $header))) {
    http_response_code(200);
    exit();
}

http_response_code(400);
exit();
