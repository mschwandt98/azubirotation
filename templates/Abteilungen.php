<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Abteilungen</div>
    <div>
        <input type="button" id="ShowAbteilungenButton" value="Anzeigen" />
        <input type="button" id="ShowAddAbteilungForm" value="Anlegen" />
    </div>
    <div id="Abteilungen" style="display: none;"></div>
    <form id="AddAbteilungForm" method="post" style="display: none">
        <label>
            <div>Bezeichnung</div>
            <input type="text" name="bezeichnung" />
        </label>
        <label>
            <div>Maximale Anzahl an Auszubildenden</div>
            <input type="number" name="maxazubis" />
        </label>
        <label>
            <div>Farbe auswählen</div>
            <input type="color" name="farbe" value="ffffff" />
        </label>
        <div>
            <input type="submit" value="Abteilung anlegen" />
        </div>
    </form>
    <form id="EditAbteilungForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <div>Bezeichnung</div>
            <input type="text" name="bezeichnung" />
        </label>
        <label>
            <div>Maximale Anzahl an Auszubildenden</div>
            <input type="number" name="maxazubis" />
        </label>
        <label>
            <div>Farbe auswählen</div>
            <input type="color" name="farbe" value="ffffff" />
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
