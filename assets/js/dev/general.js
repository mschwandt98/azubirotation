// Globale Konstanten, die in mehreren JS-Dateien gebraucht werden.
const API = "rest/";
const APIABTEILUNG = API + "Abteilung/";
const APIANSPRECHPARTNER = API + "Ansprechpartner/";
const APIAUSBILDUNGSBERUF = API + "Ausbildungsberuf/";
const APIAZUBI = API + "Azubi/";
const APISTANDARDPLAN = API + "Standardplan/";
const TIME = 400;

jQuery(function($) {
    $(document).ready(function() {

        /**
         * Minimiert die jeweilige Ansicht des Datentyps und zeigt einen Button
         * zum Ausklappen der Ansicht des jeweiligen Datentyps.
         * Datentypen sind in diesem Fall Abteilungen, Ansprechpartner,
         * Ausbildungsberufe, Azubis und Standardpläne.
         */
        $(".data-item").on("click", ".icon-eye-blocked", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find("form").hide(TIME);
            container.find(".container").hide(TIME);
            container.find(".icon-eye").show();
            el.siblings(".icon-plus").show();
            el.siblings(".icon-minus").hide();
            el.hide();
        });

        /**
         * Klappt die jeweilige Ansicht des Datentyps aus und zeigt einen Button
         * zum Minimieren der Ansicht des jeweiligen Datentyps.
         * Datentypen sind in diesem Fall Abteilungen, Ansprechpartner,
         * Ausbildungsberufe, Azubis und Standardpläne.
         */
        $(".data-item").on("click", ".icon-eye", function() {
            var el = $(this);
            el.siblings(".icon-eye-blocked").show();
            el.siblings(".icon-plus").show();
            el.siblings(".icon-minus").hide();
            el.siblings(".show-data").click();
            el.hide();
        });

        /**
         * Öffnet die Anzeige zum Anlegen eines Datensatzes des jeweiligen
         * Datentyps.
         */
        $(".data-item").on("click", ".icon-plus", function() {
            var el = $(this);
            el.siblings(".add-data").click();
            el.siblings(".icon-eye-blocked").hide();
            el.siblings(".icon-eye").show();
            el.siblings(".icon-minus").show();
            el.hide();
        });

        /**
         * Blendet die Anzeige zum Anlegen eines Datensatzes des jeweiligen
         * Datentyps aus.
         */
        $(".data-item").on("click", ".icon-minus", function() {
            var el = $(this);
            el.closest(".data-item").find("form").hide(TIME);
            el.siblings(".icon-eye-blocked").hide();
            el.siblings(".icon-eye").show();
            el.siblings(".icon-plus").show();
            el.hide();
        });

        /**
         * Schließt die Fehler der Planung.
         */
        $("#PlanErrors").on("click", ".icon-cross", function() {
            $("#PlanErrors").empty();
        });
    });
});
