<?php
/**
 * Email.php
 *
 * Template für die Benachrichtigungsmail, die in der SendMail.php versendet
 * wird.
 * Um die Template nutzen zu können, müssen die Variablen $betreff und $url
 * initialisiert sein.
 */
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $betreff; ?></title>
    <style>
        body {
            font-family: "Calibri";
            font-size: 16px;
            margin: 0;
            padding: 16px;
            position: relative;
            width: 100%;
        }

        h1,
        h2 {
            color: #009fda;
        }

        h2 {
            margin-bottom: 4px;
        }

        a {
            color: #003359 !important;
            font-weight: 600;
            text-decoration: underline;
        }

        #Wrapper {
            background-color: #dedde5;
            border: 1px solid #003359;
            box-sizing: border-box;
            color: #4b556a;
            margin: 0 auto;
            padding: 16px 32px 32px;
            position: relative;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="Wrapper">
        <h1>Ausbildungsplaner</h1>
        <p>
            Es wurden Änderungen an der Azubirotation vorgenommen.
            <br>
            Klicke <a href="<?= $url; ?>" target="_blank">hier</a>, um den aktuellen Ausbildungsplan dir anzuschauen.
        </p>
        <hr>
        <div>
            <h2>Der Link funktioniert nicht?</h2>
            <div>
                Um Zugang auf den Ausbildungsplaner zu erhalten, musst du im internen Netzwerk der SelectLine Software GmbH angemeldet sein!
            </div>
        </div>
        <hr>
        <div>
            <h2>Du bist im Netzwerk der Firma angemeldet?</h2>
            <div>
                Dann melde dich bitte in der Personalabteilung, dass der Ausbildungsplaner Fehler aufweist.
            </div>
        </div>
        <hr>
        <div>
            <h2>Warum erhälst du diese E-Mail?</h2>
            <div>
                Du erhälst diese E-Mail, da deine E-Mail-Adresse in der Ausbildungsplanung bei einem Auszubildenden oder einem Ansprechpartner hinterlegt ist.
            </div>
        </div>
        <hr>
        <div>
            <h2>Du möchtest die Planung ausdrucken?</h2>
            <div>
                Folge <a href="<?= $url . '/print'; ?>" target="_blank">diesem Link</a>, um den aktuellen Ausbildungsplan auszudrucken.
            </div>
        </div>
    </div>
</body>
