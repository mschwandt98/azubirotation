const API = "rest/";
const APIABTEILUNG = API + "Abteilung/";
const APIANSPRECHPARTNER = API + "Ansprechpartner/";
const APIAUSBILDUNGSBERUF = API + "Ausbildungsberuf/";
const APIAZUBI = API + "Azubi/";
const APISTANDARDPLAN = API + "Standardplan/";
const TIME = 400;

jQuery(function($) {
    $(document).ready(function() {

        $(".data-item").on("click", ".minimize-data-item", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find("form").hide(TIME);
            container.find(".title").css({ marginBottom: 0 });
            var upperDivs = container.find("> div");
            upperDivs.slice(upperDivs.length - 2, upperDivs.length).hide(TIME);
            el.removeClass("minimize-data-item");
            el.addClass("expand-data-item");
        });

        $(".data-item").on("click", ".expand-data-item", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find(".title").removeAttr("style");
            container.find("> div.show-add-buttons").show(TIME);
            el.removeClass("expand-data-item").addClass("minimize-data-item");
        });
    });
});
