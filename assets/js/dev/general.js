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
         * Mit den Tastenkombinationen SHIFT+ und SHIFT- lassen sich die Boxen
         * der Daten aus- bzw einklappen.
         */
        $(document).on("keydown", function(e) {

            // 16 = shift
            if (e.shiftKey && e.which !== 16) {

                switch (e.which) {
                    // +
                    case 107:
                    case 187:
                        $(".data-item .icon-plus").click();
                        break;
                    // -
                    case 109:
                    case 189:
                        $(".data-item .icon-minus").click();
                        break;
                }
            }
        });

        /**
         * Minimiert die jeweilige Ansicht des Datentyps und zeigt einen Button
         * zum Ausklappen der Ansicht des jeweiligen Datentyps.
         * Datentypen sind in diesem Fall Abteilungen, Ansprechpartner,
         * Ausbildungsberufe, Azubis und Standardpläne.
         */
        $(".data-item").on("click", ".icon-minus", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find("form").hide(TIME);
            container.find(".title").removeAttr("style");
            var upperDivs = container.find("> div");
            upperDivs.stop().slice(upperDivs.length - 2, upperDivs.length).hide(TIME);
            el.removeClass("icon-minus").addClass("icon-plus");
        });

        /**
         * Klappt die jeweilige Ansicht des Datentyps aus und zeigt einen Button
         * zum Minimieren der Ansicht des jeweiligen Datentyps.
         * Datentypen sind in diesem Fall Abteilungen, Ansprechpartner,
         * Ausbildungsberufe, Azubis und Standardpläne.
         */
        $(".data-item").on("click", ".icon-plus", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find(".title").css({ marginBottom: 8 });
            var showButtons = container.find("> div.show-add-buttons");
            showButtons.find(".show-data").click();
            showButtons.stop().show(400);
            el.removeClass("icon-plus").addClass("icon-minus");
        });

        /**
         * Schließt die Fehler der Planung.
         */
        $("#PlanErrors").on("click", ".icon-close", function() {
            $("#PlanErrors").empty();
        });
    });
});
