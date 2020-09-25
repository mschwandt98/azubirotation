<h1>Tool für die Azubirotation</h1>

<?php if (is_logged_in()) : ?>

    <form id="Logout">
        <input type="submit" value="Logout" />
    </form>
    <script>
        jQuery(function($) {
            $(document).ready(function() {

                $("#Logout").on("submit", function(e) {

                    e.preventDefault();

                    $.ajax({
                        type: "GET",
                        url: "rest/Authorization/Logout",
                        success: function() { location.reload() },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr.responseText);
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
            <input type="text" name="username" />
        </label>
        <label>
            <span>Passwort: </span>
            <input type="password" name="password" />
        </label>
        <input type="submit" value="Login" />
    </form>
    <script>
        jQuery(function($) {
            $(document).ready(function() {

                $("#Login").on("submit", function(e) {

                    e.preventDefault();

                    $.ajax({
                        type: "POST",
                        url: "rest/Authorization/Login",
                        data: {
                            username: $('#Login input[name="username"]').val(),
                            password: $('#Login input[name="password"]').val()
                        },
                        success: function() { location.reload() },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr.responseText);
                        }
                    });
                });
            });
        });
    </script>

<?php endif; ?>
