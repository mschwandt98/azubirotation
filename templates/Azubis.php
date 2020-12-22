<?php
/**
 * Azubis.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Auszubildenden.
 */
?>

<div class="data-item azubis-item">
    <div class="data-actions">
        <i class="icon-plus" title="Anlegen"></i>
        <i class="icon-minus" title="Ausblenden" style="display: none;"></i>
        <i class="icon-eye" title="Anzeigen"></i>
        <i class="icon-eye-blocked" title="Ausblenden" style="display: none;"></i>
        <input type="button" class="show-data" hidden />
        <input type="button" class="add-data" hidden />
    </div>
    <div class="title">Auszubildende</div>
    <div id="Azubis" class="container" style="display: none;"></div>
    <form id="AddAzubiForm" method="post" style="display: none;">
        <label>
            <span>Vorname</span>
            <input type="text" name="vorname" required />
        </label>
        <label>
            <span>Nachname</span>
            <input type="text" name="nachname" required />
        </label>
        <label>
            <span>Kürzel</span>
            <input type="text" name="kuerzel" required />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" required />
        </label>
        <label>
            <span>Ausbildungsberuf auswählen</span>
            <select name="id_ausbildungsberuf" required></select>
        </label>
        <label>
            <span>Ausbildungsstart</span>
            <input type="date"
                   name="ausbildungsstart" required />
        </label>
        <label>
            <span>Ausbildungsende</span>
            <input type="date"
                   name="ausbildungsende" required />
        </label>
        <label>
            <span>Planung erstellen?</span>
            <input type="checkbox"
                   name="planung_erstellen"
                   checked />
        </label>
        <div class="submit-button">
            <input type="submit" value="Auszubildenden anlegen" />
        </div>
    </form>
    <form id="EditAzubiForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Vorname</span>
            <input type="text" name="vorname" required />
        </label>
        <label>
            <span>Nachname</span>
            <input type="text" name="nachname" required />
        </label>
        <label>
            <span>Kürzel</span>
            <input type="text" name="kuerzel" maxlength="2" required />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" required />
        </label>
        <label>
            <span>Ausbildungsberuf auswählen</span>
            <select name="id_ausbildungsberuf" required></select>
        </label>
        <label>
            <span>Ausbildungsstart</span>
            <input type="date" name="ausbildungsstart" required />
        </label>
        <label>
            <span>Ausbildungsende</span>
            <input type="date" name="ausbildungsende" required />
        </label>
        <label>
            <span>Planung aktualisieren?</span>
            <input type="checkbox"
                   name="planung_erstellen" />
        </label>
        <div class="submit-button">
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
