<?php
/**
 * manual.php
 *
 * Die Anleitung zur Bearbeitung der Planung.
 */

session_start();
include_once(__DIR__ . '/config.php');

if (!is_logged_in()) {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = explode('/manual', $url)[0];
    header('Location: ' . $url);
    exit;
}

ob_start('minifier');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" />
    <title>SelectLine Ausbildungsplaner | Anleitung</title>
    <style>
        body {
            background-color: #bdbdcb;
            color: #003359;
            margin: 0;
        }

        #Info > div {
            border: 1px solid #003359;
            border-radius: 4px;
            margin: 10% 10% 0;
            overflow: auto;
            padding: 16px 32px;
        }

        #Info > div > div {
            display: grid;
            grid-gap: 32px;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: justify;
        }

        @media screen and (max-width: 1199px) {
            #Info > div {
                margin: 3% 10%;
            }
        }

        @media screen and (max-width: 1199px) and (min-width: 992px) {
            #Info > div > div {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media screen and (max-width: 991px) {
            #Info > div > div {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include_once('templates/Info.php'); ?>
</body>
</html>

<?php ob_end_flush(); ?>
