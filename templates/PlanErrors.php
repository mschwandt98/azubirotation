<?php
if (empty($errors)) return;

use Core\Helper\DataHelper;
use Core\PlanErrorCodes;

include_once(dirname(__DIR__) . "/config.php");
include_once(HELPER . "/DataHelper.php");
include_once(BASE . "/core/PlanErrorCodes.php");

$helper = new DataHelper();
?>

<?php if (array_key_exists(PlanErrorCodes::Ausbildungszeitraum, $errors)) : ?>

    <div>Folgende Planungen liegen außerhalb des Ausbildungszeitraums des jeweiligen Auszubildenden:</div>

    <?php foreach ($errors[PlanErrorCodes::Ausbildungszeitraum] as $id_azubi => $errorList) : ?>

        <div>
            <?= $helper->GetAzubis($id_azubi)->Nachname; ?>,
            <?= $helper->GetAzubis($id_azubi)->Vorname; ?>
        </div>

        <?php foreach ($errorList as $week => $plan) : ?>

            <li>Woche: <?= $week; ?></li>

        <?php endforeach; ?>

    <?php endforeach; ?>
<?php endif; ?>
<?php if (array_key_exists(PlanErrorCodes::PraeferierteAbteilungen, $errors)) : ?>

    <div>Folgende Auszubildenden sind laut Standardplan am Anfang ihrer Ausbildungen in den falschen Abteilungen:</div>

    <?php foreach ($errors[PlanErrorCodes::PraeferierteAbteilungen] as $id_azubi => $week) : ?>

        <div>
            <?= $helper->GetAzubis($id_azubi)->Nachname; ?>,
            <?= $helper->GetAzubis($id_azubi)->Vorname; ?>:
            <?= $week; ?>
        </div>

    <?php endforeach; ?>
<?php endif; ?>
<?php if (array_key_exists(PlanErrorCodes::AbteilungenMaxAzubis, $errors)) : ?>

    <div>In folgenden Zeiträumen haben die jeweiligen Abteilungen mehr Auszubildende als erlaubt.</div>

    <?php foreach ($errors[PlanErrorCodes::AbteilungenMaxAzubis] as $id_abteilung => $errorList) : ?>

        <div><?= $helper->GetAbteilungen($id_abteilung)->Bezeichnung; ?></div>
        <div>Maximale Anzahl an Auszubildenden: <?= $helper->GetAbteilungen($id_abteilung)->MaxAzubis; ?></div>

        <?php foreach ($errorList as $week => $anzahlAzubis) : ?>

            <li>Woche: <?= $week; ?>, Anzahl an Auszubildenden: <?= $anzahlAzubis; ?></li>

        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
