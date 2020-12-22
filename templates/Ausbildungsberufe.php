<?php
/**
 * Ausbildungsberufe.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Ausbildungsberufen.
 */
?>

<div class="data-item ausbildungsberufe-item">
    <div class="data-actions">
        <i class="icon-plus" title="Anlegen"></i>
        <i class="icon-minus" title="Ausblenden" style="display: none;"></i>
        <i class="icon-eye" title="Anzeigen"></i>
        <i class="icon-eye-blocked" title="Ausblenden" style="display: none;"></i>
        <input type="button" class="show-data" hidden />
        <input type="button" class="add-data" hidden />
    </div>
    <div class="title">Ausbildungsberufe</div>
    <div id="Ausbildungsberufe" class="container" style="display: none;"></div>
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
