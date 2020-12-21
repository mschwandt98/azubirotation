<?php
/**
 * main.php
 *
 * Zeigt den Kern der Anwendung. Wenn der Benutzer angemeldet ist, hat er
 * Zugriff auf die Daten, darf die Planung veränderung und Aktionen zur Planung
 * ausführen. Wenn der Benutzer nicht angemeldet ist, hat er lediglich Zugriff
 * auf die Planung ohne die Berechtigung Daten daran zu verändern.
 */
?>

<?php if (is_logged_in()) : ?>

    <input type="hidden" id="CsrfToken" value="<?= $_SESSION['csrf_token']; ?>" />
    <div class="grid">
        <div>
            <?php include_once('templates/Abteilungen.php'); ?>
            <?php include_once('templates/Standardplaene.php'); ?>
        </div>
        <div>
            <?php include_once('templates/Ausbildungsberufe.php'); ?>
            <?php include_once('templates/Azubis.php'); ?>
        </div>
        <div>
            <?php include_once('templates/Ansprechpartner.php'); ?>
        </div>
    </div>

<?php endif; ?>

<div style="display: grid; grid-template-columns: auto 1fr; margin: 16px 0;">
    <div>
        <form id="Filter" style="display: none;">
            <label>
                <span>Auszubildende filtern: </span>
                <input type="search" />
            </label>
        </form>
    </div>
    <div>

        <?php if (is_logged_in()) : ?>
            <?php include_once('templates/InfoButton.php'); ?>
        <?php endif; ?>

    </div>
</div>

<div id="Plan"></div>
<div id="PlanActions" style="display: none;">

    <?php if (is_logged_in()) : ?>

        <div>
            <input type="button" id="SavePlan" value="Planung speichern" />
            <input type="button" id="TestPlan" value="Auf Fehler testen" />
        </div>

    <?php endif; ?>

    <div>

        <?php if (is_logged_in()) : ?>

            <span style="display: none; color: limegreen;">Die Benachrichtigungen wurden erfolgreich versendet.</span>
            <input type="button" id="SendMail" value="Benachrichtigungen senden" />

        <?php endif; ?>

        <input type="button" id="PrintPlan" value="Plan drucken" />
    </div>
</div>
<div id="PlanErrors"></div>
