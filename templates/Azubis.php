<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Auszubildende</div>
    <div class="show-add-buttons">
        <input type="button" id="ShowAzubisButton" value="Anzeigen" />
        <input type="button" id="ShowAddAzubiForm" value="Anlegen" />
    </div>
    <div id="Azubis" style="display: none;"></div>
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
            <span>Musterplanung erstellen?</span>
            <input type="checkbox"
                   name="musterplanung_erstellen"
                   checked />
        </label>
        <div>
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
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
