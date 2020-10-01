<?php use Core\Helper\DataHelper; ?>
<?php include_once(__DIR__ . "/core/helper/DataHelper.php"); ?>
<?php $Abteilungen = (new DataHelper())->GetAbteilungen(); ?>

<div class="toggle-legende">Legende</div>
<div class="legenden-list visible">
    <div class="legende">
        <div class="symbol marker">
            <div></div>
        </div>
        <div>Ausbildungsanfang/-ende</div>
    </div>

    <?php foreach ($Abteilungen as $abteilung) : ?>

    <div class="legende">
        <div class="symbol" style="background-color: <?= $abteilung->Farbe; ?>;"></div>
        <div><?= $abteilung->Bezeichnung; ?></div>
    </div>

    <?php endforeach; ?>

</div>
