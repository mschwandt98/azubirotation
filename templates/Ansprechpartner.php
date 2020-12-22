<?php
/**
 * Ansprechpartner.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Ansprechpartnern.
 */
?>

<div class="data-item ansprechpartner-item">
    <div class="data-actions">
        <i class="icon-plus" title="Anlegen"></i>
        <i class="icon-minus" title="Ausblenden" style="display: none;"></i>
        <i class="icon-eye" title="Anzeigen"></i>
        <i class="icon-eye-blocked" title="Ausblenden" style="display: none;"></i>
        <input type="button" class="show-data" hidden />
        <input type="button" class="add-data" hidden />
    </div>
    <div class="title">Ansprechpartner</div>
    <div id="Ansprechpartner" class="container" style="display: none;"></div>
    <form id="AddAnsprechpartnerForm" method="post" style="display: none;">
        <label>
            <span>Name</span>
            <input type="text" name="name" required />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" required />
        </label>
        <label>
            <span>Abteilung auswählen</span>
            <select name="id_abteilung" required></select>
        </label>
        <div class="submit-button">
            <input type="submit" value="Ansprechpartner anlegen" />
        </div>
    </form>
    <form id="EditAnsprechpartner" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Name</span>
            <input type="text" name="name" required />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" required />
        </label>
        <label>
            <span>Abteilung auswählen</span>
            <select name="id_abteilung" required></select>
        </label>
        <div class="submit-button">
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
