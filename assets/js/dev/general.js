const API = "rest/";
const APIABTEILUNG = API + "Abteilung/";
const APIANSPRECHPARTNER = API + "Ansprechpartner/";
const APIAUSBILDUNGSBERUF = API + "Ausbildungsberuf/";
const APIAZUBI = API + "Azubi/";
const APISTANDARDPLAN = API + "Standardplan/";
const TIME = 400;

jQuery(function($) {
    $(document).ready(function() {

        var timePeriods = $("#Plan th.month");

        if (timePeriods.length > 0) {

            let time = new Date().getTime();
            let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;

            for (let i = 0; i < timePeriods.length; i++) {

                let dates = timePeriods.eq(i).attr("title").split(" - ");
                let firstDateTime = new Date(dates[0].replace(pattern,'$3-$2-$1')).getTime();
                let secondDateTime = new Date(dates[1].replace(pattern,'$3-$2-$1')).getTime();

                if (time >= firstDateTime && time <= secondDateTime) {

                    let scrollLeft = (i) * $(timePeriods[i]).outerWidth();

                    // Sticky-Columns disabled on mobile
                    if ($(window).width() <= 991) {
                        scrollLeft += 400;
                    }

                    $("#Plan .horizontal-scroll").scrollLeft(scrollLeft);
                    break;
                }
            }
        }

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
