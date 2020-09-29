<?php session_start(); ?>
<?php include(__DIR__ . "/config.php"); ?>
<?php ob_start("minifier"); ?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool für die Azubirotation</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <?php if (is_logged_in()) : ?>

        <link rel="stylesheet" href="assets/css/style.css">

    <?php else : ?>

        <link rel="stylesheet" href="assets/css/public-style.css">

    <?php endif; ?>

</head>
<body>
    <header><?php include_once(__DIR__ . "/header.php") ?></header>
    <main><?php include_once(__DIR__ . "/main.php") ?></main>
    <footer><?php include_once(__DIR__ . "/footer.php") ?></footer>

    <?php if (is_logged_in()) : ?>

        <script src="assets/js/abteilung.js"></script>
        <script src="assets/js/ausbildungsberuf.js"></script>
        <script src="assets/js/ansprechpartner.js"></script>
        <script src="assets/js/azubi.js"></script>
        <script src="assets/js/standardplaene.js"></script>
        <script src="assets/js/plan.js"></script>
        <script src="assets/js/general.js"></script>

    <?php endif; ?>

</body>
</html>

<?php ob_end_flush(); ?>
