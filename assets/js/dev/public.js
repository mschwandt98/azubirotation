jQuery(function($) {
    $(document).ready(function() {

        $("#LoadingSpinner").show();
        $("#Plan").load("rest/Refresh/Plan", _ => {

            $("#LoadingSpinner").hide();
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
         * @param {requestCallback}     callback
         */
        function HideShowAzubisOfBeruf(row, hide, callback = null) {

            var row = $(row);
            while (true) {

                if (row.next().hasClass("azubi")) {

                    row = row.next();

                    if (hide) {
                        HideAzubiRow(row);
                    } else {
                        ShowAzubiRow(row);
                    }
                } else {
                    break;
                }
            }

            if (callback && !hide) {
                callback();
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
         * Triggert die Filterung.
         */
        $("#Filter").on("input", 'input[type="search"]', function() {

            var rows = $("#Plan tr .ausbildungsberuf").closest("tr");
            rows.each(index => {
                let row = $(rows[index]);
                row.find("th i").removeClass("icon-triangle-right").addClass("icon-triangle-down");
                HideShowAzubisOfBeruf(row, false);
            });

            FilterAzubis();
        });

        /**
         * Filtert die Azubis in der Planung nach dem eingegebenen Suchtext.
         * Gefiltert wird in den Nachnamen, den Vornamen und den Kürzeln.
         */
        function FilterAzubis() {

            var searchText = ($('#Filter input[type="search"]').val()).toLowerCase();
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

                    row = row.next();

                    if (row.hasClass("azubi")) {

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
                    berufsRow.nextAll(".space-row").first().hide();
                } else {
                    berufsRow.show();
                    berufsRow.nextAll(".space-row").first().show();
                }
            });
        }

        /**
         * Öffnet die druckbare Version des Plans.
         */
        $("#PrintPlan").on("click", function() {
            window.open(window.location.origin + window.location.pathname + "print", "_blank", "height=600,width=1000,menubar=0,status=0,titlebar=0,toolbar=0");
        });

        /**
         * Azubipläne eines Ausbildungsberufes werden ein- bzw ausgeblendet.
         */
        $("#Plan").on("click", "tr .ausbildungsberuf", function() {

            var el = $(this);
            icon = el.find("i");

            if (icon.hasClass("icon-triangle-down")) {
                HideShowAzubisOfBeruf(el.closest("tr"), true);
            } else if ($('#Filter input[type="search"]').val()) {
                HideShowAzubisOfBeruf(el.closest("tr"), false, FilterAzubis);
            } else {
                HideShowAzubisOfBeruf(el.closest("tr"), false);
            }

            icon.toggleClass("icon-triangle-down icon-triangle-right");
        });

        /**
         * Toggled dem DarkMode.
         */
        $("#DarkMode").on("click", function() {

            let el = $(this).find("i");
            let html = $("html");

            if (html.attr("data-theme") == "dark") {
                html.attr("data-theme", "light");
                el.addClass("icon-sun").removeClass("icon-sun-dark");
                document.cookie = "darkmode=false; max-age=2592000";
            } else {
                html.attr("data-theme", "dark");
                el.addClass("icon-sun-dark").removeClass("icon-sun");
                document.cookie = "darkmode=true; max-age=2592000";
            }
        });

        /**
         *
         */
        $("#Menu").on("click", ".menu-point", function() {

            var el = $(this);
            var action = el.attr("class").match(/action-.*/);

            if (action) {

                action = action[0].replace("action-", "");

                var submenu = $("#SubMenu");
                submenu.find(".menu-action").hide();
                submenu.show();

                $("#" + action.charAt(0).toUpperCase() + action.slice(1)).show();
            }
        })

        /**
         *
         */
        $("#SubMenu").on("click", "i.icon-cross", function() {
            var submenu = $("#SubMenu");
            submenu.find(".menu-action").hide();
            submenu.hide();
        });
    });
});
