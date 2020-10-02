<h1>Tool für die Azubirotation</h1>

<?php if (is_logged_in()) : ?>

    <div>
        <div id="LoadingSpinner" style="display: none;">
            <div class="bounce-1"></div>
            <div class="bounce-2"></div>
            <div class="bounce-3"></div>
        </div>
    </div>
    <form id="Logout">
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

    <div><?php // ignore -> placeholder in grid ?></div>
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
