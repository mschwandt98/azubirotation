<?php
/**
 * Ansprechpartner.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Ansprechpartnern.
 */
?>

<div class="data-item">
    <div class="icon-minus"></div>
    <div class="title">Ansprechpartner</div>
    <div class="show-add-buttons">
        <input type="button" id="ShowAnsprechpartnerButton" value="Anzeigen" />
        <input type="button" id="ShowAddAnsprechpartnerForm" value="Anlegen" />
    </div>
    <div id="Ansprechpartner" style="display: none;"></div>
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
        <div>
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
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
