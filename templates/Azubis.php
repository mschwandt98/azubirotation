<div class="data-item">
    <div class="title">Auszubildende</div>
    <div>
        <input type="button" id="ShowAzubisButton" value="Anzeigen" />
        <input type="button" id="ShowAddAzubiForm" value="Anlegen" />
    </div>
    <div id="Azubis" style="display: none;"></div>
    <form id="AddAzubiForm" method="post" style="display: none;">
        <label>
            <div>Vorname</div>
            <input type="text" name="vorname" />
        </label>
        <label>
            <div>Nachname</div>
            <input type="text" name="nachname" />
        </label>
        <label>
            <div>Email</div>
            <input type="email" name="email" />
        </label>
        <label>
            <div>Ausbildungsberuf auswählen</div>
            <select name="id_ausbildungsberuf"></select>
        </label>
        <label>
            <div>Ausbildungsstart</div>
            <input type="date"
                    name="ausbildungsstart"
                    min="<?= date("Y-m-d"); ?>" />
        </label>
        <label>
            <div>Ausbildungsende</div>
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
            <div>Vorname</div>
            <input type="text" name="vorname" />
        </label>
        <label>
            <div>Nachname</div>
            <input type="text" name="nachname" />
        </label>
        <label>
            <div>Email</div>
            <input type="email" name="email" />
        </label>
        <label>
            <div>Ausbildungsberuf auswählen</div>
            <select name="id_ausbildungsberuf"></select>
        </label>
        <label>
            <div>Ausbildungsstart</div>
            <input type="date" name="ausbildungsstart" />
        </label>
        <label>
            <div>Ausbildungsende</div>
            <input type="date" name="ausbildungsende" />
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
