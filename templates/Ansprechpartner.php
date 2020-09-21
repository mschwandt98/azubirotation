<div class="data-item">
    <div class="title">Ansprechpartner</div>
    <div>
        <input type="button" id="ShowAnsprechpartnerButton" value="Anzeigen" />
        <input type="button" id="ShowAddAnsprechpartnerForm" value="Anlegen" />
    </div>
    <div id="Ansprechpartner" style="display: none;"></div>
    <form id="AddAnsprechpartnerForm" method="post" style="display: none;">
        <label>
            <div>Name</div>
            <input type="text" name="name" />
        </label>
        <label>
            <div>Email</div>
            <input type="email" name="email" />
        </label>
        <label>
            <div>Abteilung auswählen</div>
            <select name="id_abteilung"></select>
        </label>
        <div>
            <input type="submit" value="Ansprechpartner anlegen" />
        </div>
    </form>
    <form id="EditAnsprechpartner" method="post" style="display: none;">
        <input type="hidden" name="id" />
        <label>
            <div>Name</div>
            <input type="text" name="name" />
        </label>
        <label>
            <div>Email</div>
            <input type="email" name="email" />
        </label>
        <label>
            <div>Abteilung auswählen</div>
            <select name="id_abteilung"></select>
        </label>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
