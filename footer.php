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
