<div class="data-item">
    <div class="minimize-data-item"></div>
    <div class="title">Standardpläne</div>
    <div>
        <input type="button" id="ShowStandardplaeneButton" value="Anzeigen" />
        <input type="button" id="ShowAddStandardplanForm" value="Anlegen" />
    </div>
    <div id="Standardplaene" style="display: none;"></div>
    <form id="AddStandardplanForm" method="post" style="display: none;">
        <label>
            <div>Ausbildungsberuf auswählen</div>
            <select name="id_ausbildungsberuf"></select>
        </label>
        <div class="plan-phasen">
            <div class="phase">
                <label>
                    <div>Abteilung auswählen</div>
                    <select name="id_abteilung"></select>
                </label>
                <div>
                    <span>Wochen: </span><input type="number" name="wochen" />
                </div>
                <div>
                    <label>
                        <span>Präferieren: </span><input type="checkbox" name="praeferieren" />
                    </label>
                </div>
                <div>
                    <label>
                        <span>Optional: </span><input type="checkbox" name="optional" />
                    </label>
                </div>
                <input type="button" class="delete-phase" value="Phase löschen" />
            </div>
        </div>
        <div>
            <input type="button" class="add-phase" value="Phase hinzufügen" />
        </div>
        <div>
            <input type="submit" value="Standardplan anlegen" />
        </div>
    </form>
    <form id="EditStandardplanForm" method="post" style="display: none;">
        <input type="hidden" name="id_ausbildungsberuf" />
        <div class="plan"></div>
        <div>
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
