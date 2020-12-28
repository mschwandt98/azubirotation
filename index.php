<?php
/**
 * index.php
 *
 * Die Hauptseite der Anwendung, in der alle benötigten Komponenten, Skripte und
 * Styles eingebunden werden.
 */

session_start();
include(__DIR__ . '/config.php');
ob_start('minifier');
?>

<!DOCTYPE html>
<html lang="de"
      data-theme="<?= (array_key_exists('darkmode', $_COOKIE) && $_COOKIE['darkmode'] == 'true') ? 'dark' : 'light'; ?>"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-status-bar" content="#009fda" />
    <meta name="theme-color" content="#009fda">
    <meta name="author" content="Marian Schwandt">
    <meta name="description" content="Der Ausbildungsplaner ist ein Tool zur Erleichterung der Ausbildungsplanung in der SelectLine Software GmbH, welches den Ausbildungsplan jedem Mitarbeiter zur Verfügung stellt.">
    <link rel="shortcut icon" href="favicon.ico" />
    <title>SelectLine Ausbildungsplaner</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/">
</head>
<body>
    <?php include_once(__DIR__ . '/header.php') ?>
    <?php include_once(__DIR__ . '/main.php') ?>
    <div id="ErrorMessageBox" style="display: none;">
        <div class="message"></div>
    </div>

    <?php if (is_logged_in()) : ?>

        <input type="hidden" id="CsrfToken" value="<?= $_SESSION['csrf_token']; ?>" />
        <div id="Popup"></div>

    <?php endif; ?>

    <script type="text/javascript" src="assets/js/"></script>
</body>
</html>

<?php ob_end_flush(); ?>
