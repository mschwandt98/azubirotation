<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Abteilungen</div>
    <div class="show-add-buttons">
        <input type="button" id="ShowAbteilungenButton" value="Anzeigen" />
        <input type="button" id="ShowAddAbteilungForm" value="Anlegen" />
    </div>
    <div id="Abteilungen" style="display: none;"></div>
    <form id="AddAbteilungForm" method="post" style="display: none">
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" />
        </label>
        <label>
            <span>Maximale Anzahl an Auszubildenden</span>
            <input type="number" name="maxazubis" />
        </label>
        <label>
            <span>Farbe auswählen</span>
            <input type="color" name="farbe" value="ffffff" />
        </label>
        <div>
            <input type="submit" value="Abteilung anlegen" />
        </div>
    </form>
    <form id="EditAbteilungForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Bezeichnung</span>
            <input type="text" name="bezeichnung" />
        </label>
        <label>
            <span>Maximale Anzahl an Auszubildenden</span>
            <input type="number" name="maxazubis" />
        </label>
        <label>
            <span>Farbe auswählen</span>
            <input type="color" name="farbe" value="ffffff" />
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
