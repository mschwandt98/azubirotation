<?php
/**
 * Abteilungen.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Abteilungen.
 */
?>

<div class="data-item abteilung-item">
    <div class="data-actions">
        <i class="icon-plus" title="Anlegen"></i>
        <i class="icon-minus" title="Ausblenden" style="display: none;"></i>
        <i class="icon-eye" title="Anzeigen"></i>
        <i class="icon-eye-blocked" title="Ausblenden" style="display: none;"></i>
        <input type="button" class="show-data" hidden />
        <input type="button" class="add-data" hidden />
    </div>
    <div class="title">Abteilungen</div>
    <div id="Abteilungen" class="container" style="display: none;"></div>
    <form id="AddAbteilungForm" method="post" style="display: none">
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <label>
            <span>Maximale Anzahl an Auszubildenden</span>
            <input type="number" name="maxazubis" required />
        </label>
        <label>
            <span>Farbe auswählen</span>
            <input type="color" name="farbe" value="#ffffff" required />
        </label>
        <div class="submit-button">
            <input type="submit" value="Abteilung anlegen" />
        </div>
    </form>
    <form id="EditAbteilungForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <label>
            <span>Maximale Anzahl an Auszubildenden</span>
            <input type="number" name="maxazubis" required />
        </label>
        <label>
            <span>Farbe auswählen</span>
            <input type="color" name="farbe" value="#ffffff" required />
        </label>
        <div class="submit-button">
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
