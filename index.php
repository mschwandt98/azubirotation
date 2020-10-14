<?php
/**
 * index.php
 *
 * Die Hauptseite der Anwendung, in der alle benötigten Komponenten, Skripte und
 * Styles eingebunden werden.
 */

session_start();
include(__DIR__ . "/config.php");
ob_start("minifier");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-status-bar" content="#009fda" />
    <meta name="theme-color" content="#009fda">
    <meta name="author" content="Marian Schwandt">
    <link rel="shortcut icon" href="favicon.ico" />
    <title>SelectLine Ausbildungsplaner</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <?php if (is_logged_in()) : ?>

        <link rel="stylesheet" href="assets/css/style.css">

    <?php else : ?>

        <link rel="stylesheet" href="assets/css/public-style.css">

    <?php endif; ?>

</head>
<body>
    <header id="Header"><?php include_once(__DIR__ . "/header.php") ?></header>
    <main id="Main"><?php include_once(__DIR__ . "/main.php") ?></main>
    <footer id="Footer"><?php include_once(__DIR__ . "/footer.php") ?></footer>
    <div id="ErrorMessageBox" style="display: none;">
        <div class="message"></div>
    </div>

    <?php if (is_logged_in()) : ?>

        <div id="Popup"></div>
        <script src="assets/js/script.js"></script>

    <?php else: ?>

        <script src="assets/js/public-script.js"></script>

    <?php endif; ?>

</body>
</html>

<?php ob_end_flush(); ?>
