<?php
/**
 * legende.php
 *
 * Die Legende zur Planung.
 */

use core\helper\DataHelper;

$Abteilungen = (new DataHelper())->GetAbteilungen();
?>

<div class="legenden-list visible">
    <div class="legende">
        <div class="symbol">
            <div class="icon-mark-begin"></div>
            <div class="icon-mark-end"></div>
        </div>
        <div>Ausbildungsanfang/-ende</div>
    </div>
    <div class="legende">
        <div class="symbol">
            <div class="icon-plan-mark-separat"></div>
        </div>
        <div>Einzelner Termin</div>
    </div>
    <div class="legende">
        <div class="symbol">
            <div class="icon-plan-mark"></div>
        </div>
        <div>Längerer Termin</div>
    </div>

    <?php foreach ($Abteilungen as $abteilung) : ?>

    <div class="legende">
        <div class="symbol" style="background-color: <?= $abteilung->Farbe; ?>;"></div>
        <div><?= $abteilung->Bezeichnung; ?></div>
    </div>

    <?php endforeach; ?>

</div>
