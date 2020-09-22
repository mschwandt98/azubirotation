<?php session_start(); ?>
<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azubirotation</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <header><?php include_once("header.php") ?></header>
    <main><?php include_once("main.php") ?></main>
    <footer><?php include_once("footer.php") ?></footer>
    <script src="assets/js/abteilung.js"></script>
    <script src="assets/js/ausbildungsberuf.js"></script>
    <script src="assets/js/ansprechpartner.js"></script>
    <script src="assets/js/azubi.js"></script>
    <script src="assets/js/standardplaene.js"></script>
    <script src="assets/js/general.js"></script>
</body>
</html>
