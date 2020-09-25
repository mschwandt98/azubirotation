<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Auszubildende</div>
    <div>
        <input type="button" id="ShowAzubisButton" value="Anzeigen" />
        <input type="button" id="ShowAddAzubiForm" value="Anlegen" />
    </div>
    <div id="Azubis" style="display: none;"></div>
    <form id="AddAzubiForm" method="post" style="display: none;">
        <label>
            <span>Vorname</span>
            <input type="text" name="vorname" />
        </label>
        <label>
            <span>Nachname</span>
            <input type="text" name="nachname" />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" />
        </label>
        <label>
            <span>Ausbildungsberuf auswählen</span>
            <select name="id_ausbildungsberuf"></select>
        </label>
        <label>
            <span>Ausbildungsstart</span>
            <input type="date"
                    name="ausbildungsstart"
                    min="<?= date("Y-m-d"); ?>" />
        </label>
        <label>
            <span>Ausbildungsende</span>
            <input type="date"
                    name="ausbildungsende"
                    min="<?= date("Y-m-d"); ?>" />
        </label>
        <div>
            <input type="submit" value="Auszubildenden anlegen" />
        </div>
    </form>
    <form id="EditAzubiForm" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <span>Vorname</span>
            <input type="text" name="vorname" />
        </label>
        <label>
            <span>Nachname</span>
            <input type="text" name="nachname" />
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" />
        </label>
        <label>
            <span>Ausbildungsberuf auswählen</span>
            <select name="id_ausbildungsberuf"></select>
        </label>
        <label>
            <span>Ausbildungsstart</span>
            <input type="date" name="ausbildungsstart" />
        </label>
        <label>
            <span>Ausbildungsende</span>
            <input type="date" name="ausbildungsende" />
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
