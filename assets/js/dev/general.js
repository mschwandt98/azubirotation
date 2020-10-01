const API = "rest/";
const APIABTEILUNG = API + "Abteilung/";
const APIANSPRECHPARTNER = API + "Ansprechpartner/";
const APIAUSBILDUNGSBERUF = API + "Ausbildungsberuf/";
const APIAZUBI = API + "Azubi/";
const APISTANDARDPLAN = API + "Standardplan/";

jQuery(function($) {
    $(document).ready(function() {

        var animationDuration = 400;

        $(".data-item").on("click", ".minimize-data-item", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find("form").hide(animationDuration);
            container.find(".title").css({ marginBottom: 0 });
            var upperDivs = container.find("> div");
            upperDivs.slice(upperDivs.length - 2, upperDivs.length).hide(animationDuration);
            el.removeClass("minimize-data-item");
            el.addClass("expand-data-item");
        });

        $(".data-item").on("click", ".expand-data-item", function() {
            var el = $(this);
            var container = el.closest(".data-item");
            container.find(".title").removeAttr("style");
            container.find("> div.show-add-buttons").show(animationDuration);
            el.removeClass("expand-data-item");
            el.addClass("minimize-data-item");
        });

        $("#Footer").on("click", ".toggle-legende", function() {

            var list = $("#Footer .legenden-list");
            var classVisible = "visible";

            if (list.hasClass(classVisible)) {
                $("#Footer").animate({ bottom: "-" + list.outerHeight() + "px" });
                list.removeClass(classVisible);
            } else {
                $("#Footer").animate({ bottom: 0 });
                list.addClass(classVisible);
            }
        });
    });
});
