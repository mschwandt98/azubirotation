<?php
/**
 * header.php
 *
 * Der Header der Anwendung. Der Header beihaltet den Titel der Anwendung und
 * ein Login- bzw Logout-Formular. Welches Formular davon geladen wird hängt
 * davon ab, ob der Benutzer eingeloggt ist.
 */
?>

<header id="Header">
    <div id="TopHeader">
        <h1>Ausbildungsplaner</h1>
        <div>
            <div id="LoadingSpinner" style="display: none;">
                <div class="bounce-1"></div>
                <div class="bounce-2"></div>
                <div class="bounce-3"></div>
            </div>
        </div>
        <div>
            <div id="ShowMenu" title="Menü einblenden">
                <span>Menü einblenden</span>
                <i class="icon-expand"></i>
            </div>
        </div>

        <?php if (is_logged_in()) : ?>

            <form id="Logout">
                <span style="padding-right: 16px;">
                    Angemeldet als <b><?= $_SESSION['user_name']; ?></b>
                </span>
                <input type="submit" value="Logout" />
            </form>
            <script>
                jQuery(function($) {
                    $(document).ready(function() {

                        $("#Logout").on("submit", function(e) {

                            e.preventDefault();
                            $("#LoadingSpinner").show();

                            $.ajax({
                                type: "GET",
                                url: "rest/Authorization/Logout",
                                success: function() { location.reload() },
                                error: function () {
                                    $("#LoadingSpinner").hide();
                                    var emb = $("#ErrorMessageBox");
                                    emb.find(".message").text("Ein unbekannter Fehler ist beim Logout aufgetreten.");
                                    emb.show();
                                    setTimeout(() => { emb.fadeOut().text(); }, 10000);
                                }
                            });
                        });
                    });
                });
            </script>

        <?php else: ?>

            <form id="Login">
                <label>
                    <span>Username: </span>
                    <input type="text" name="username" required />
                </label>
                <label>
                    <span>Passwort: </span>
                    <input type="password" name="password" required />
                </label>
                <input type="submit" value="Login" />
            </form>
            <script>
                jQuery(function($) {
                    $(document).ready(function() {

                        $("#Login").on("submit", function(e) {

                            e.preventDefault();
                            $("#LoadingSpinner").show();

                            $.ajax({
                                type: "POST",
                                url: "rest/Authorization/Login",
                                data: {
                                    username: $('#Login input[name="username"]').val(),
                                    password: $('#Login input[name="password"]').val()
                                },
                                success: function() { location.reload() },
                                error: function () {
                                    $("#LoadingSpinner").hide();
                                    var emb = $("#ErrorMessageBox");
                                    emb.find(".message").text("Die Anmeldedaten sind falsch.");
                                    emb.show();
                                    setTimeout(() => { emb.fadeOut().text(); }, 10000);
                                }
                            });
                        });
                    });
                });
            </script>

        <?php endif; ?>

    </div>

    <?php include_once('menu.php'); ?>

</header>
