<?php
/**
 * Ausbildungsberufe.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Ausbildungsberufen.
 */
?>

<div class="data-item ausbildungsberufe-item">
    <div class="icon-plus"></div>
    <div class="title">Ausbildungsberufe</div>
    <div class="show-add-buttons" style="display: none;">
        <input type="button" class="show-data" value="Anzeigen" />
        <input type="button" class="add-data" value="Anlegen" />
    </div>
    <div id="Ausbildungsberufe" style="display: none;"></div>
    <form id="AddAusbildungsberufForm" method="post" style="display: none;">
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <div class="submit-button">
            <input type="submit" value="Ausbildungsberuf anlegen" />
        </div>
    </form>
    <form id="EditAusbildungsberufForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <div class="submit-button">
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
