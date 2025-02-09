<?php
/**
 * Standardplan.php
 *
 * Die Template für zum Bearbeiten eines Standardplans
 *
 * Für diese Template muss die Variable $viewBag gesetzt sein.
 *
 * Im Gegensatz zu den Formularen der anderen Datentypen (Abteilungen,
 * Ausbildungsberufe, Ansprechpartner, Auszubildende) wird dieses Formular nicht
 * per JavaScript erstellt. Der Hintergrund war der Zeitrahmen des Projekts. Es
 * wäre aus Sicht der Menge an Übertragungsdaten besser diese Template per
 * JavaScript erstellen zu lassen.
 *
 * TODO: Diese Template per JavaScript erstellen lassen.
 */
?>

<?php if (empty($viewBag)) return; ?>

<div><?= $viewBag['Standardplan']->Ausbildungsberuf ?></div>
<div class="plan-phasen">

    <?php foreach ($viewBag['Standardplan']->Phasen as $phase) : ?>

        <div class="phase">
            <label>
                <span>Abteilung auswählen</span>
                <select name="id_abteilung" required>

                    <?php foreach ($viewBag['Abteilungen'] as $abteilung) : ?>

                        <option value="<?= $abteilung['ID'] ?>"
                                <?= ($phase->ID_Abteilung == $abteilung['ID']) ? 'selected' : '' ?>>
                            <?= $abteilung['Bezeichnung']; ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </label>
            <label>
                <span>Wochen</span>
                <input type="number" name="wochen" value="<?= $phase->Wochen ?>" required />
            </label>
            <label>
                <span>Präferieren</span>
                <input type="checkbox" name="praeferieren" <?= ($phase->Praeferieren) ? 'checked' : ''; ?> />
            </label>
            <label>
                <span>Optional</span>
                <input type="checkbox" name="optional" <?= ($phase->Optional) ? 'checked' : ''; ?> />
            </label>
            <input type="button" class="delete-phase" value="Phase löschen" />
        </div>

    <?php endforeach; ?>

</div>
<div>
    <input type="button" class="add-phase" value="Phase hinzufügen" />
</div>
