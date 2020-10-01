<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Ausbildungsberufe</div>
    <div class="show-add-buttons">
        <input type="button" id="ShowAusbildungsberufeButton" value="Anzeigen" />
        <input type="button" id="ShowAddAusbildungsberufForm" value="Anlegen" />
    </div>
    <div id="Ausbildungsberufe" style="display: none;"></div>
    <form id="AddAusbildungsberufForm" method="post" style="display: none;">
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <div>
            <input type="submit" value="Ausbildungsberuf anlegen" />
        </div>
    </form>
    <form id="EditAusbildungsberufForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" required />
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
