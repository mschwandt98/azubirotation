<?php
/**
 * menu.php
 *
 * Das Menü der Anwendung, in welchem alle Aktionen gebündelt sind.
 */
?>

<div id="Menu">
    <nav>
        <li class="menu-point action-filter" title="Planung filtern">
            <i class="icon-filter"></i>
            <div>Filter</div>
        </li>
        <li class="menu-point action-information" title="Spalten umschalten">
            <i class="icon-list"></i>
            <div>Spalten umschalten</div>
        </li>
        <li class="menu-point action-legende" title="Legende anzeigen">
            <i class="icon-list"></i>
            <div>Legende</div>
        </li>

        <?php if (is_logged_in()) : ?>

            <li class="menu-point action-data" title="Stammdaten verwalten">
                <i class="icon-database"></i>
                <div>Stammdaten</div>
            </li>
            <li class="menu-point" title="Planung speichern" id="SavePlan">
                <i class="icon-save"></i>
                <div>Speichern</div>
            </li>
            <li class="menu-point" title="Auf Fehler testen" id="TestPlan">
                <i class="icon-test"></i>
                <div>Testen</div>
            </li>
            <li class="menu-point" title="Benachrichtigungen senden" id="SendMail">
                <i class="icon-mail"></i>
                <div>Senden</div>
            </li>
            <li class="menu-point" title="Anleitung" id="InfoButton">
                <i class="icon-help"></i>
                <div>Anleitung</div>
                <?php include_once('templates/InfoButton.php'); ?>
            </li>

        <?php endif; ?>

        <li class="menu-point" title="Drucken" id="PrintPlan">
            <i class="icon-print"></i>
            <div>Drucken</div>
        </li>
        <li class="menu-point" title="Dark Mode an/aus" id="DarkMode">
            <i class="icon-sun<?= (array_key_exists('darkmode', $_COOKIE) && $_COOKIE['darkmode'] == 'true') ? '-dark' : ''; ?>"></i>
            <div>Dark Mode</div>
        </li>

    </nav>
    <div id="SubMenu" style="position: relative;">
        <div>
            <form class="menu-action" id="Filter">
                <label>
                    <span>Auszubildende filtern: </span>
                    <input type="search" />
                </label>
            </form>
            <div class="menu-action" id="Legende">
                <?php include_once('legende.php'); ?>
            </div>
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
            <i class="icon-cross" title="Submenu schließen"></i>
        </div>
    </div>
</div>
