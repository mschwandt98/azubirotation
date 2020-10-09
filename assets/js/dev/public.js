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
