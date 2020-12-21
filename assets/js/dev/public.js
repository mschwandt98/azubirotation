jQuery(function($) {
    $(document).ready(function() {

        $("#LoadingSpinner").show();
        $("#Plan").load("rest/Refresh/Plan", _ => {

            $("#LoadingSpinner").hide();
            $("#Filter").show();
            $("#PlanActions").show();

            // Scrollt zum aktuellen Datum in der Planung
            let timePeriods = $("#Plan th.month");

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

                        $("#Plan").animate({
                            "scrollLeft": scrollLeft
                        }, 400)

                        break;
                    }
                }
            }
        });

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
        function HideShowAzubisOfBeruf(row, hide) {

            var row = $(row);
            while (true) {

                if (row.next().hasClass("azubi")) {

                    row = row.next();

                    if (hide) {
                        HideAzubiRow();
                    } else {
                        ShowAzubiRow();
                    }
                } else {
                    break;
                }
            }
        }

        /**
         * Setzt die Visibility der Reihe auf "collapse".
         *
         * @param {HTMLTableRowElement} row Die zu versteckende Reihe.
         */
        function HideAzubiRow(row) {
            $(row).css({ visibility: "collapse" });
        }

        /**
         * Setzt die Visibility der Reihe auf "visible".
         *
         * @param {HTMLTableRowElement} row Die zu zeigende Reihe.
         */
        function ShowAzubiRow(row) {
            $(row).css({ visibility: "visible" });
        }

        /**
         * Verhindert, dass das Formular zur Filterung submitted werden kann.
         */
        $("#Filter").on("submit", function(e) {
            e.preventDefault();
        });

        /**
         * Filtert die Azubis in der Planung nach dem eingegebenen Suchtext.
         * Gefiltert wird in den Nachnamen, den Vornamen und den Kürzeln.
         */
        $("#Filter").on("input", 'input[type="search"]', function() {

            var searchText = ($(this).val()).toLowerCase();
            var azubis = $("#Plan .azubi");

            azubis.each(index => {

                let azubi = $(azubis[index]);
                let data = azubi.find("> th:lt(3)");
                let nachname = data.eq(0).text().toLowerCase();
                let vorname = data.eq(1).text().toLowerCase();
                let kuerzel = data.eq(2).text().toLowerCase();

                if (nachname.includes(searchText)) {
                    ShowAzubiRow(azubi);
                    return;
                }

                if (vorname.includes(searchText)) {
                    ShowAzubiRow(azubi);
                    return;
                }

                if (kuerzel.includes(searchText)) {
                    ShowAzubiRow(azubi);
                    return;
                }

                HideAzubiRow(azubi);
            });

            var rows = $("#Plan .ausbildungsberuf").closest("tr");
            rows.each(index => {

                let berufsRow = $(rows[index]);
                let row = berufsRow;
                let hide = true;

                while (true) {
                    if (row.next().hasClass("azubi")) {

                        row = row.next();
                        if (row.css("visibility") === "visible") {
                            hide = false;
                            break;
                        }

                    } else {
                        break;
                    }
                }

                if (hide) {
                    berufsRow.hide();
                } else {
                    berufsRow.show();
                }
            });
        });

        /**
         * Öffnet die druckbare Version des Plans.
         */
        $("#PrintPlan").on("click", function() {
            window.open(window.location.href + "print", "_blank", "height=600,width=1000,menubar=0,status=0,titlebar=0,toolbar=0");
        });

        /**
         * Azubipläne eines Ausbildungsberufes werden ein- bzw ausgeblendet.
         */
        $("#Plan").on("click", "tr .ausbildungsberuf", function() {

            var el = $(this);
            icon = el.find("div").first();

            if (icon.hasClass("icon-triangle-b")) {
                HideShowAzubisOfBeruf(el.closest("tr"), true);
            } else {
                HideShowAzubisOfBeruf(el.closest("tr"));
            }

            icon.toggleClass("icon-triangle-b icon-triangle-r");
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
