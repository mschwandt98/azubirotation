<?php if (!empty($Abteilungen)) : ?>

    <footer>

        <?php foreach ($Abteilungen as $abteilung) : ?>

        <div class="abteilung">
            <div class="farbe" style="background-color: <?= $abteilung->Farbe; ?>;"></div>
            <div><?= $abteilung->Bezeichnung; ?></div>
        </div>

        <?php endforeach; ?>

    </footer>

<?php endif; ?>
