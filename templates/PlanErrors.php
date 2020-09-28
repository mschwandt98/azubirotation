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

    <?php foreach ($errors[PlanErrorCodes::Ausbildungszeitraum] as $error) : ?>

        <li>
            <?= $helper->GetAzubis($error["Azubi"]->ID)->Nachname; ?>,
            <?= $helper->GetAzubis($error["Azubi"]->ID)->Vorname; ?> -
            Woche vom <?= $error["Plan"]->Startdatum; ?> bis zum <?= $error["Plan"]->Enddatum; ?>
        </li>

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
