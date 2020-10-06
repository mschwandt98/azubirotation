<?php if (is_logged_in()) : ?>

    <input type="hidden" id="CsrfToken" value="<?= $_SESSION["csrf_token"]; ?>" />
    <div class="grid">
        <div><?php include_once("templates/Abteilungen.php"); ?></div>
        <div><?php include_once("templates/Ausbildungsberufe.php"); ?></div>
        <div><?php include_once("templates/Ansprechpartner.php"); ?></div>
        <div><?php include_once("templates/Standardplaene.php"); ?></div>
        <div><?php include_once("templates/Azubis.php"); ?></div>
    </div>
    <div id="PlanSettings">
        <div class="settings-box">
            <label>
                Termin eintragen <input id="SetMark" type="checkbox" />
            </label>
        </div>
        <div id="InfoButton">
            <div style="display: none;">
                <div>
                    Zum Planen einer Woche auf das entsprechende Feld klicken und die Abteilung sowie den Ansprechpartner in
                    den erscheinenden Popups anklicken.
                </div>
                <div>
                    Zum Planen mehrerer Wochen die linke Maustaste gedrückt halten und über die entsprechenden Felder ziehen. Danach
                    wie o.g. fortführen.
                </div>
                <div>
                    Wenn ein Zeitraum einer Planung verschoben werden soll, muss die STRG-Taste und die linke Maustaste
                    gedrückt gehalten werden. Der komplette Zeitraum kann nun nach belieben verschoben werden.
                </div>
                <div>
                    Um Termine wie beispielsweise "Zeugnisse" einzutragen, den Haken im Kästchen hinter "Termin
                    eintragen" setzen. Daraufhin auf das entsprechende Feld klicken, im aufkommenden Popup die
                    Terminbezeichnung eintragen und diese bestätigen.
                </div>
                <div>
                    Zum Speichern der Planung den Button "Planung speichern" drücken.
                </div>
                <div>
                    Um zu testen, ob die Planung Fehler beinhaltet, auf den Button "Auf Fehler testen" drücken.
                </div>
                <div>
                    Mit Klick auf den Button "Benachrichtigungen senden" wird eine Email an alle Ansprechpartner und
                    Auszubildenen geschickt. In dieser Email wird auf den Plan verlinkt.
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<div id="Plan"><?php include_once("core/Plan.php"); ?></div>

<?php if (is_logged_in()) : ?>

<div class="plan-actions">
    <div>
        <input type="button" id="SavePlan" value="Planung speichern" />
        <input type="button" id="TestPlan" value="Auf Fehler testen" />
    </div>
    <div>
        <span style="display: none; color: limegreen;">Die Benachrichtigungen wurden erfolgreich versendet.</span>
        <input type="button" id="SendMail" value="Benachrichtigungen senden" />
    </div>
</div>
<div id="PlanErrors"></div>

<?php endif; ?>
