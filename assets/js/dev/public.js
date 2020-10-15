jQuery(function($) {
    $(document).ready(function() {

        // Scrollt zum aktuellen Datum in der Planung
        var timePeriods = $("#Plan th.month");

        if (timePeriods.length > 0) {

            let time = new Date().getTime();
            let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;

            for (let i = 0; i < timePeriods.length; i++) {

                let dates = timePeriods.eq(i).attr("title").split(" - ");
                let firstDateTime = new Date(dates[0].replace(pattern,'$3-$2-$1')).getTime();
                let secondDateTime = new Date(dates[1].replace(pattern,'$3-$2-$1')).getTime();

                if (time >= firstDateTime && time <= secondDateTime) {

                    let j = i;

                    let scrollLeft = ((j - 1 > 0) ? --j : j) * ($(timePeriods[i]).innerWidth() + 1); // 1 = Border-width

                    // + 400, da Sticky-Columns on mobile disabled sind
                    if ($(window).width() <= 991) {
                        scrollLeft += 400;
                    }

                    $("#Plan").scrollLeft(scrollLeft);
                    break;
                }
            }
        }

        /**
         * Blendet alle Azubipläne eines Ausbildungsberufes ein bzw aus.
         *
         * @param {HTMLTableRowElement} row     Die Reihe des
         *                                      Ausbildungsberufes, zu dem die
         *                                      Pläne ein- bzw ausgeblendet
         *                                      werden sollen.
         * @param {boolean}             hide    True = Reihen ausblenden
         *                                      False = Reihen einblenden
         */
        function HideShowAzubiRow(row, hide) {

            var row = $(row);
            while (true) {

                if (row.next().hasClass("azubi")) {

                    row = row.next();

                    if (hide) {
                        row.css({ visibility: "collapse" });
                    } else {
                        row.css({ visibility: "visible" });
                    }
                } else {
                    break;
                }
            }
        }

        /**
         * Azubipläne eines Ausbildungsberufes werden ausgeblenden.
         */
        $("#Plan").on("click", "tr .triangle-bottom", function() {

            var el = $(this);
            el.addClass("triangle-right").removeClass("triangle-bottom");
            HideShowAzubiRow(el.closest("tr"), true);
        });

        /**
         * Azubipläne eines Ausbildungsberufes werden eingeblenden.
         */
        $("#Plan").on("click", "tr .triangle-right", function() {

            var el = $(this);
            el.addClass("triangle-bottom").removeClass("triangle-right");
            HideShowAzubiRow(el.closest("tr"), false);
        });

        /**
         * Toggled die Sichtbarkeit der Legende.
         */
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
