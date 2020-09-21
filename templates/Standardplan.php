<?php $viewBag = (isset($viewBag)) ? $viewBag : []; ?>

<div>
    <div><?= $viewBag["Standardplan"]->Ausbildungsberuf ?></div>
    <div class="plan-phasen">

        <?php foreach ($viewBag["Standardplan"]->Phasen as $phase) : ?>

            <div class="phase">
                <label>
                    <div>Abteilung auswählen</div>
                    <select name="id_abteilung">

                        <?php foreach ($viewBag["Abteilungen"] as $abteilung) : ?>

                            <option value="<?= $abteilung["ID"] ?>"
                                    <?= ($phase->ID_Abteilung == $abteilung["ID"]) ? "selected" : "" ?>>
                                <?= $abteilung["Bezeichnung"]; ?>
                            </option>

                        <?php endforeach ?>

                    </select>
                </label>
                <div>
                    <span>Wochen: </span>
                    <input type="number" name="wochen" value="<?= $phase->Wochen ?>" />
                </div>
                <input type="button" class="delete-phase" value="Phase löschen" />
            </div>

        <?php endforeach; ?>

    </div>
    <div>
        <input type="button" class="add-phase" value="Phase hinzufügen" />
    </div>
</div>
