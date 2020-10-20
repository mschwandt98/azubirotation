<?php
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
    form label {
        cursor: pointer;
        display: block;
        width: fit-content;
    }
</style>

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

    <form id="Register" action="rest/Authorization/Register" method="POST">
        <label>
            <span>Username: </span>
            <input type="text" name="username" required />
        </label>
        <label>
            <span>Passwort: </span>
            <input type="password" name="password" required />
        </label>
        <label>
            <span>Passwort wiederholen: </span>
            <input type="password" name="passwordRepeated" required />
        </label>
        <input type="submit" value="Registrieren" />
    </form>

<?php else : ?>

    <div>Momentan ist es nicht erlaubt sich zu registrieren.</div>

<?php endif; ?>

<?php ob_end_flush(); ?>
