<?php
/**
 * menu.php
 *
 * Das Menü der Anwendung, in welchem alle Aktionen gebündelt sind.
 */
?>

<div id="Menu">
    <nav>
        <ul>
            <li title="Nach Namen filtern..." id="Filter" class="menu-option">
                <form>
                    <label>
                        <input type="search" placeholder="Nach Namen filtern..." />
                    </label>
                </form>
            </li>
            <li title="Zeige Zeiträume ohne Ausbilder" id="EmptyColumnFilter" class="menu-option">
                <div>
                    <label class="checkbox">
                        <span>Zeige Balken ohne Ausbilder</span>
                        <input type="checkbox" checked />
                        <span class="checkmark"></span>
                    </label>
                </div>
            </li>
            <li class="menu-point action-information" title="Spalten umschalten">
                <div class="menu-icon">
                    <i class="icon-columns"></i>
                </div>
                <div>Spalten umschalten</div>
            </li>
            <li class="menu-point action-legende" title="Legende anzeigen">
                <div class="menu-icon">
                    <i class="icon-list"></i>
                </div>
                <div>Legende</div>
            </li>

            <?php if (is_logged_in()) : ?>

                <li class="menu-point action-data" title="Stammdaten verwalten">
                    <div class="menu-icon">
                        <i class="icon-database"></i>
                    </div>
                    <div>Stammdaten</div>
                </li>
                <li class="menu-point" title="Planung speichern" id="SavePlan">
                    <div class="menu-icon">
                        <i class="icon-save"></i>
                    </div>
                    <div>Speichern</div>
                </li>
                <li class="menu-point" title="Auf Fehler testen" id="TestPlan">
                    <div class="menu-icon">
                        <i class="icon-test"></i>
                    </div>
                    <div>Testen</div>
                </li>
                <li class="menu-point" title="Benachrichtigungen senden" id="SendMail">
                    <div class="menu-icon">
                        <i class="icon-mail"></i>
                    </div>
                    <div>Senden</div>
                </li>
                <li class="menu-point" title="Anleitung" id="InfoButton">
                    <div class="menu-icon">
                        <i class="icon-help"></i>
                    </div>
                    <div>Anleitung</div>
                </li>

            <?php endif; ?>

            <li class="menu-point" title="Drucken" id="PrintPlan">
                <div class="menu-icon">
                    <i class="icon-print"></i>
                </div>
                <div>Drucken</div>
            </li>
            <li class="menu-point" title="Dark Mode an/aus" id="DarkMode">
                <div class="menu-icon">
                    <label class="switch">
                            <input type="checkbox"
                                <?= (array_key_exists('darkmode', $_COOKIE) && $_COOKIE['darkmode'] === 'true') ? 'checked' : '' ?> />
                            <i class="slider"></i>
                    </label>
                </div>
                <div>
                    <?= (array_key_exists('darkmode', $_COOKIE) && $_COOKIE['darkmode'] === 'true') ? 'Dark' : 'Light' ?> Mode
                </div>
            </li>
            <li class="menu-point" title="Menü ausblenden" id="HideMenu">
                <div class="menu-icon">
                    <i class="icon-compress"></i>
                </div>
            </li>
        </ul>
    </nav>
</div>
<div id="SubMenu">
    <div class="menu-action" id="Information">
        <div>
            <label class="checkbox">
                <span>Nachname</span>
                <input type="checkbox" value="nachname" checked />
                <span class="checkmark"></span>
            </label>
            <label class="checkbox">
                <span>Vorname</span>
                <input type="checkbox" value="vorname" checked />
                <span class="checkmark"></span>
            </label>
            <label class="checkbox">
                <span>Kürzel</span>
                <input type="checkbox" value="kuerzel" checked />
                <span class="checkmark"></span>
            </label>
            <label class="checkbox">
                <span>Zeitraum</span>
                <input type="checkbox" value="zeitraum" checked />
                <span class="checkmark"></span>
            </label>
        </div>
    </div>
    <div class="menu-action" id="Legende">
        <?php include_once('legende.php'); ?>
    </div>

    <?php if (is_logged_in()) : ?>

        <div class="menu-action" id="Data">
            <div class="grid">
                <div>
                    <?php include_once('templates/Abteilungen.php'); ?>
                    <?php include_once('templates/Standardplaene.php'); ?>
                </div>
                <div>
                    <?php include_once('templates/Ausbildungsberufe.php'); ?>
                    <?php include_once('templates/Azubis.php'); ?>
                </div>
                <div>
                    <?php include_once('templates/Ansprechpartner.php'); ?>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>
