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
    <header id="Header"><?php include_once(__DIR__ . "/header.php") ?></header>
    <main id="Main"><?php include_once(__DIR__ . "/main.php") ?></main>
    <footer id="Footer"><?php include_once(__DIR__ . "/footer.php") ?></footer>
    <div id="ErrorMessageBox" style="display: none;">
        <div class="message"></div>
    </div>

    <?php if (is_logged_in()) : ?>
        <div id="Popup"></div>
        <script src="assets/js/script.js"></script>

    <?php endif; ?>

    <script>
        $("#Footer").on("click", ".toggle-legende", function() {

            var list = $("#Footer .legenden-list");
            var classVisible = "visible";

            if (list.hasClass(classVisible)) {
                $("#Footer").animate({ bottom: "-" + list.outerHeight() + "px" });
                list.removeClass(classVisible);
            } else {
                $("#Footer").animate({ bottom: 0 });
                list.addClass(classVisible);
            }
        });
    </script>
</body>
</html>

<?php ob_end_flush(); ?>
