<?php
/**
 * footer.php
 *
 * Der Footer der Anwendung, in dem die Legende für die Planung dargestellt ist.
 */

use Core\Helper\DataHelper;
include_once(__DIR__ . "/core/helper/DataHelper.php");
$Abteilungen = (new DataHelper())->GetAbteilungen();
?>

<div class="toggle-legende">Legende</div>
<div class="legenden-list visible">
    <div class="legende">
        <div class="symbol begin-end">
            <div></div>
        </div>
        <div>Ausbildungsanfang/-ende</div>
    </div>
    <div class="legende">
        <div class="symbol marker">
            <div></div>
        </div>
        <div>Termin</div>
    </div>

    <?php foreach ($Abteilungen as $abteilung) : ?>

    <div class="legende">
        <div class="symbol" style="background-color: <?= $abteilung->Farbe; ?>;"></div>
        <div><?= $abteilung->Bezeichnung; ?></div>
    </div>

    <?php endforeach; ?>

</div>
