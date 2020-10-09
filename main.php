<?php if (is_logged_in()) : ?>

    <input type="hidden" id="CsrfToken" value="<?= $_SESSION["csrf_token"]; ?>" />
    <div class="grid">
        <div><?php include_once("templates/Abteilungen.php"); ?></div>
        <div><?php include_once("templates/Ausbildungsberufe.php"); ?></div>
        <div><?php include_once("templates/Ansprechpartner.php"); ?></div>
        <div><?php include_once("templates/Standardplaene.php"); ?></div>
        <div><?php include_once("templates/Azubis.php"); ?></div>
    </div>
    <div><?php include_once("templates/InfoButton.php"); ?></div>

<?php endif; ?>

<div id="Plan"><?php include_once("core/Plan.php"); ?></div>

<?php if (is_logged_in()) : ?>

<div class="plan-actions">
    <div>
        <input type="button" id="SavePlan" value="Planung speichern" />
        <input type="button" id="TestPlan" value="Auf Fehler testen" />
    </div>
    <div>
        <span style="display: none; color: limegreen;">Die Benachrichtigungen wurden erfolgreich versendet.</span>
        <input type="button" id="SendMail" value="Benachrichtigungen senden" />
    </div>
</div>
<div id="PlanErrors"></div>

<?php endif; ?>
