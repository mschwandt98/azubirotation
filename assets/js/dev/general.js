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
        $(".data-item").on("click", ".icon-minus", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find("form").hide(TIME);
            container.find(".title").css({ marginBottom: 0 });
            var upperDivs = container.find("> div");
            upperDivs.slice(upperDivs.length - 2, upperDivs.length).hide(TIME);
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
            container.find(".title").removeAttr("style");
            container.find("> div.show-add-buttons").show(TIME);
            el.removeClass("icon-plus").addClass("icon-minus");
        });
    });
});
