<?php if (is_logged_in()) : ?>

    <div class="grid">
        <div><?php include_once("templates/Abteilungen.php"); ?></div>
        <div><?php include_once("templates/Ausbildungsberufe.php"); ?></div>
        <div><?php include_once("templates/Ansprechpartner.php"); ?></div>
        <div><?php include_once("templates/Azubis.php"); ?></div>
        <div><?php include_once("templates/Standardplaene.php"); ?></div>
    </div>

<?php endif; ?>
<div id="Plan"><?php include_once("core/Plan.php"); ?></div>
