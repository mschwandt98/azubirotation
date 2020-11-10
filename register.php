<?php
/**
 * register.php
 *
 * Die Registrierungs-Seite der Anwendung.
 */

use core\helper\DataHelper;

session_start();
include_once('config.php');

$value = (new DataHelper())->GetSetting('allow-registration')->Value;
$formStartseite = '<form action="' . explode('register.php', $_SERVER['PHP_SELF'])[0] . '">
    <input type="submit" value="Zur Startseite" />
</form>';

ob_start('minifier');
?>

<style>
    #root {
        border: 1px solid #009fda;
        border-radius: 8px;
        font-family: "Calibri";
        padding: 8px 16px;
        position: absolute;
        top: 15%;
        left: 25%;
        right: 25%;
    }

    input[type="submit"] {
        background-color: transparent;
        border: 0;
        border-radius: 8px;
        color: #009fda;
        margin: 8px 0;
        outline: 0;
    }

    input[type="submit"]:hover {
        background-color: #e6f5fb;
    }

    form label {
        cursor: pointer;
        display: block;
        width: fit-content;
    }
</style>

<div id="root">
    <?= $formStartseite; ?>

    <?php if (is_logged_in()) : ?>

        <p>Du bist bereits eingeloggt und kannst dich daher nicht registrieren.</p>
        <div>
            Da du eingeloggt bist kannst du jedoch entscheiden, ob sich weitere
            Personen registrieren dürfen.
        </div>
        <form action="rest/Authorization/Register" method="POST">
            <div>Sollen sich weitere Personen registrieren dürfen?</div>
            <label>
                <span>Ja</span>
                <input type="radio"
                    name="allowRegistration"
                    value="true"
                    <?= ($value === 'true') ? 'checked' : ''; ?> />
            </label>
            <label>
                <span>Nein</span>
                <input type="radio"
                    name="allowRegistration"
                    value="false"
                    <?= ($value !== 'true') ? 'checked' : ''; ?> />
            </label>
            <input type="submit" value="Auswahl speichern" />
        </form>

    <?php elseif ($value === 'true') : ?>

        <style>
            #root table {
                text-align: right;
            }
        </style>

        <form id="Register" action="rest/Authorization/Register" method="POST">
            <table>
                <tr>
                    <td>Username: </td>
                    <td><input type="text" name="username" required /></td>
                </tr>
                <tr>
                    <td>Passwort: </td>
                    <td><input type="password" name="password" required /></td>
                </tr>
                <tr>
                    <td>Passwort wiederholen: </td>
                    <td><input type="password" name="passwordRepeated" required /></td>
                </tr>
            </table>
            <input type="submit" value="Registrieren" />
        </form>

    <?php else : ?>

        <div>Momentan ist es nicht erlaubt sich zu registrieren.</div>

    <?php endif; ?>

</div>

<?php ob_end_flush(); ?>
