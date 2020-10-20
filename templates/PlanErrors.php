<?php
/**
 * PlanErrors.php
 *
 * Die Template für die Anzeige der Verstöße gegen Planungsrichtlinien.
 *
 * Für diese Template muss die Variable $errors gesetzt sein.
 */

if (empty($errors)) return;

use core\helper\DataHelper;
use core\helper\DateHelper;
use core\PlanErrorCodes;

include_once(dirname(__DIR__) . '/config.php');

$helper = new DataHelper();
?>

<div>

    <?php if (array_key_exists(PlanErrorCodes::Ausbildungszeitraum, $errors)) : ?>

        <div class="error-container">
            <div class="description">
                Planungen außerhalb des Ausbildungszeitraums des jeweiligen Auszubildenden
            </div>

            <?php foreach ($errors[PlanErrorCodes::Ausbildungszeitraum] as $id_azubi => $errorList) : ?>
                <?php $azubi = $helper->GetAzubis($id_azubi); ?>

                <div>
                    <?= $azubi->Nachname; ?>,
                    <?= $azubi->Vorname; ?>
                </div>

                <?php foreach ($errorList as $zeitraum) : ?>
                    <?php $dates = DateHelper::GetDatesFromString($zeitraum); ?>

                    <li>
                        <?= DateHelper::FormatDate($dates['StartDatum']); ?> -
                        <?= DateHelper::FormatDate($dates['EndDatum']); ?>
                    </li>

                <?php endforeach; ?>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>
    <?php if (array_key_exists(PlanErrorCodes::PraeferierteAbteilungen, $errors)) : ?>

        <div class="error-container">
            <div class="description">
                Auszubildende in nicht präferierten Abteilungen am Anfang ihrer jeweiligen Ausbildung
            </div>

            <?php foreach ($errors[PlanErrorCodes::PraeferierteAbteilungen] as $id_azubi => $abteilungen) : ?>
                <?php $azubi = $helper->GetAzubis($id_azubi); ?>

                <div>

                    <?= $azubi->Nachname; ?>,
                    <?= $azubi->Vorname; ?>:

                    <?php $abteilungenMissing = [] ?>
                    <?php foreach ($abteilungen as $id_abteilung) : ?>
                        <?php $abteilungenMissing[] = $helper->GetAbteilungen($id_abteilung)->Bezeichnung; ?>
                    <?php endforeach; ?>

                    <?= implode(', ', $abteilungenMissing); ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>
    <?php if (array_key_exists(PlanErrorCodes::AbteilungenMaxAzubis, $errors)) : ?>

        <div class="error-container">
            <div class="description">
                Zeiträume, in denen Abteilungen mehr Auszubildene als vorgesehen zugeordnet sind
            </div>

            <?php foreach ($errors[PlanErrorCodes::AbteilungenMaxAzubis] as $id_abteilung => $errorList) : ?>
                <?php $abteilung = $helper->GetAbteilungen($id_abteilung); ?>

                <div>Abteilung <?= $abteilung->Bezeichnung; ?></div>
                <div>Maximale Anzahl an Auszubildenden: <?= $abteilung->MaxAzubis; ?></div>

                <?php foreach ($errorList as $zeitraum => $anzahlAzubis) : ?>
                    <?php $dates = DateHelper::GetDatesFromString($zeitraum); ?>

                    <li>
                        <?= DateHelper::FormatDate($dates['StartDatum']); ?> -
                        <?= DateHelper::FormatDate($dates['EndDatum']); ?>,
                        Anzahl an Auszubildenden: <?= $anzahlAzubis; ?>
                    </li>

                <?php endforeach; ?>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>
    <?php if (array_key_exists(PlanErrorCodes::WochenInAbteilungen, $errors)) : ?>

        <div class="error-container">
            <div class="description">
                Auszubildene, die länger in Abteilungen sind als vorgeschrieben
            </div>

            <?php foreach ($errors[PlanErrorCodes::WochenInAbteilungen] as $id_azubi => $abteilungen) : ?>
                <?php $azubi = $helper->GetAzubis($id_azubi); ?>

                <div>

                    <?= $azubi->Nachname; ?>,
                    <?= $azubi->Vorname; ?>:

                    <?php $abteilungenWithErrors = []; ?>
                    <?php foreach ($abteilungen as $id_abteilung) : ?>
                        <?php $abteilungenWithErrors[] = $helper->GetAbteilungen($id_abteilung)->Bezeichnung; ?>
                    <?php endforeach; ?>

                    <?= implode(', ', $abteilungenWithErrors); ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
