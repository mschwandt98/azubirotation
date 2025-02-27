<?php
/**
 * Standardplaene.php
 *
 * Die Template für die Formulare zum Anzeigen, Anlegen, Bearbeiten und Löschen
 * von Standardplänen.
 */
?>

<div class="data-item standardplaene-item">
    <div class="data-actions">
        <i class="icon-plus" title="Anlegen"></i>
        <i class="icon-minus" title="Ausblenden" style="display: none;"></i>
        <i class="icon-chevron-down" title="Anzeigen"></i>
        <i class="icon-chevron-up" title="Ausblenden" style="display: none;"></i>
        <input type="button" class="show-data" hidden />
        <input type="button" class="add-data" hidden />
    </div>
    <div class="title">Standardpläne</div>
    <div id="Standardplaene" class="container" style="display: none;"></div>
    <form id="AddStandardplanForm" method="post" style="display: none;">
        <label>
            <span>Ausbildungsberuf auswählen</span>
            <select name="id_ausbildungsberuf" required></select>
        </label>
        <div class="plan-phasen">
            <div class="phase">
                <label>
                    <span>Abteilung auswählen</span>
                    <select name="id_abteilung" required></select>
                </label>
                <label>
                    <span>Wochen</span><input type="number" name="wochen" required />
                </label>
                <label>
                    <span>Präferieren</span><input type="checkbox" name="praeferieren" />
                </label>
                <label>
                    <span>Optional</span><input type="checkbox" name="optional" />
                </label>
                <input type="button" class="delete-phase" value="Phase löschen" />
            </div>
        </div>
        <div>
            <input type="button" class="add-phase" value="Phase hinzufügen" />
        </div>
        <div class="submit-button">
            <input type="submit" value="Standardplan anlegen" />
        </div>
    </form>
    <form id="EditStandardplanForm" method="post" style="display: none;">
        <input type="hidden" name="id_ausbildungsberuf" />
        <div class="plan"></div>
        <div class="submit-button">
            <input type="submit" value="Änderungen speichern" />
        </div>
    </form>
</div>
