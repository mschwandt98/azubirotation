$(document).ready(function() {

    $('#LoadingSpinner').show();
    $('#Plan').load('rest/Refresh/Plan', _ => {

        $('#LoadingSpinner').hide();

        // Scrollt zum aktuellen Datum in der Planung
        let timePeriods = $('#Plan th.month');
        let timePeriodsLength = timePeriods.length;

        if (timePeriodsLength > 0) {

            let time = new Date().getTime();
            let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;

            for (let i = 0; i < timePeriodsLength; i++) {

                let dates = timePeriods.eq(i).attr('title').split(' - ');
                let firstDateTime = new Date(dates[0].replace(pattern,'$3-$2-$1')).getTime();
                let secondDateTime = new Date(dates[1].replace(pattern,'$3-$2-$1')).getTime();

                if (time >= firstDateTime && time <= secondDateTime) {

                    let j = i;

                    let scrollLeft = ((j - 1 > 0) ? --j : j) * ($(timePeriods[i]).innerWidth() + 1); // 1 = Border-width

                    // + 400, da Sticky-Columns on mobile disabled sind
                    if ($(window).width() <= 991) {
                        scrollLeft += 400;
                    }

                    $('#Plan').scrollLeft(scrollLeft);

                    break;
                }
            }
        }

        var cookie = getCookie('hidden-columns');
        var wrapper = $('#Information');
        if (cookie) {
            let hiddenColumns = cookie.split('-');

            hiddenColumns.forEach(column => {
                wrapper.find(`input[type="checkbox"][value="${ column }"]`).click();
            });
        } else {
            wrapper.find('input[type="checkbox"][value="zeitraum"]').click();
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

            let nextRow = row.next();
            if (nextRow.hasClass('azubi')) {

                row = nextRow;

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
        $(row).css({ visibility: 'collapse' });
    }

    /**
     * Setzt die Visibility der Reihe auf "visible".
     *
     * @param {HTMLTableRowElement} row Die zu zeigende Reihe.
     */
    function ShowAzubiRow(row) {
        $(row).css({ visibility: 'visible' });
    }

    /**
     * Verhindert, dass das Formular zur Filterung submitted werden kann.
     */
    $('#Filter').on('submit', function(e) {
        e.preventDefault();
    });

    /**
     * Triggert die Filterung.
     */
    $('#Filter').on('input', 'input[type="search"]', function() {

        var rows = $('#Plan tr .ausbildungsberuf').closest('tr');
        rows.each(index => {
            let row = $(rows[index]);
            row.find('th i').removeClass('icon-triangle-right').addClass('icon-triangle-down');
            HideShowAzubisOfBeruf(row, false);
        });

        FilterAzubis();
    });

    /**
     * Filtert die Azubis in der Planung nach dem eingegebenen Suchtext.
     * Gefiltert wird in den Nachnamen, den Vornamen und den Kürzeln.
     */
    function FilterAzubis() {

        var searchText = ($('#Filter input[type="search"]').val()).trim().toLowerCase();
        searchText = ReplaceUmlaute(searchText);

        var azubis = $('#Plan .azubi');

        azubis.each(index => {

            let azubi = $(azubis[index]);
            let data = azubi.find('> th:lt(3)');
            let nachname = ReplaceUmlaute(data.eq(0).text().toLowerCase());
            let vorname = ReplaceUmlaute(data.eq(1).text().toLowerCase());
            let kuerzel = ReplaceUmlaute(data.eq(2).text().toLowerCase());

            if (nachname.includes(searchText) ||
                vorname.includes(searchText) ||
                kuerzel.includes(searchText) ||
                (nachname + ' ' + vorname).includes(searchText) ||
                (vorname + ' ' + nachname).includes(searchText)) {
                ShowAzubiRow(azubi);
                return;
            }

            HideAzubiRow(azubi);
        });

        var rows = $('#Plan .ausbildungsberuf').closest('tr');
        rows.each(index => {

            let berufsRow = $(rows[index]);
            let row = berufsRow;
            let hide = true;

            while (true) {

                row = row.next();

                if (row.hasClass('azubi')) {

                    if (row.css('visibility') === 'visible') {
                        hide = false;
                        break;
                    }

                } else {
                    break;
                }
            }

            if (hide) {
                berufsRow.hide();
                berufsRow.nextAll('.space-row').first().hide();
            } else {
                berufsRow.show();
                berufsRow.nextAll('.space-row').first().show();
            }
        });
    }

    /**
     * Ersetzt alle Umlaute in einem String.
     *
     * @var {string} string Der String, in dem Umlaute ersetzt werden sollen.
     *
     * @return {string} Der String mit den ersetzten Umlauten.
     */
    function ReplaceUmlaute(string) {
        string = string.replaceAll('ä', 'ae');
        string = string.replaceAll('ö', 'oe');
        string = string.replaceAll('ü', 'ue');
        string = string.replaceAll('ß', 'ss');

        return string;
    }

    /**
     * Öffnet die druckbare Version des Plans.
     */
    $('#PrintPlan').on('click', function() {

        let href = window.location.href;
        if (href.match(/.*\/index.php/) || href.match(/.*\/index/)) {
            href = href.substring(0, href.indexOf('index') - 1);
        }
        window.open(href + '/print', '_blank', 'height=600,width=1000,menubar=0,status=0,titlebar=0,toolbar=0');
    });

    /**
     * Azubipläne eines Ausbildungsberufes werden ein- bzw ausgeblendet.
     */
    $('#Plan').on('click', 'tr .ausbildungsberuf', function() {

        var el = $(this);
        icon = el.find('i');

        if (icon.hasClass('icon-triangle-down')) {
            HideShowAzubisOfBeruf(el.closest('tr'), true);
        } else if ($('#Filter input[type="search"]').val()) {
            HideShowAzubisOfBeruf(el.closest('tr'), false, FilterAzubis);
        } else {
            HideShowAzubisOfBeruf(el.closest('tr'), false);
        }

        icon.toggleClass('icon-triangle-down icon-triangle-right');
    });

    /**
     * Toggled dem DarkMode.
     */
    $('#DarkMode').on('click', function() {

        let el = $(this);
        let checkbox = el.find('input[type="checkbox"]');
        let text = el.find('> div:last-of-type');
        let html = $('html');

        if (html.attr('data-theme') == 'dark') {
            html.attr('data-theme', 'light');
            checkbox.prop('checked', false);
            text.text('Light Mode');
            document.cookie = 'darkmode=false; max-age=2592000';
        } else {
            html.attr('data-theme', 'dark');
            checkbox.prop('checked', true);
            text.text('Dark Mode');
            document.cookie = 'darkmode=true; max-age=2592000';
        }
    }).find('.slider').click(function() {
        let checkbox = $(this).siblings('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
        $('#DarkMode').click();
    });;

    /**
     * Blendet das Menü aus.
     */
    $('#HideMenu').on('click', function() {
        $('#Menu').hide();
        $('#SubMenu').hide();
        $('#Plan').addClass('full-height');
        $('#ShowMenu').css('display', 'flex');
    });

    /**
     * Blendet das Menü ein.
     */
    $('#ShowMenu').on('click', function() {
        $(this).hide();
        $('#Menu').show();
        $('#Plan').removeClass('full-height');
    });

    /**
     * Öffnet bzw schließt den angeklickten Menüpunkt. Sofern der Menüpunkt
     * bereits sichtbar ist, wird er versteckt. Ansonsten werden alle
     * anderen Menüpunkte versteckt und der angeklickte wird angezeigt.
     */
    $('#Menu').on('click', '.menu-point', function() {

        var action = $(this).attr('class').match(/action-.*/);

        if (action) {

            action = action[0].replace('action-', '');
            let actionMenu = $('#' + action.charAt(0).toUpperCase() + action.slice(1));
            let submenu = $('#SubMenu');

            if (actionMenu.is(':hidden')) {
                submenu.find('.menu-action').hide();
                submenu.show();
                actionMenu.show();
            } else {
                actionMenu.hide();
                submenu.hide();
            }
        }
    });

    /**
     * Blendet die jeweiligen Spalten mit den Azubiinformationen in der
     * Tabelle bzw in der Planung aus.
     */
    $('#Information').on('click', 'input[type="checkbox"]', function(e) {

        $('#Information label').removeClass('disabled');
        $('#Information input:checkbox').prop('disabled', false);

        var plan = $('#Plan');
        var columns = plan.find('th.' + $(this).val());

        // Aufgerufen per eval()
        const cFirst = 0;
        const cSecond = 117;
        const cThird = 234;
        const cFourth = 292;

        if (this.checked) {
            columns.show();
        } else {
            columns.hide();
        }

        var nachname = plan.find('th.nachname');
        var vorname = plan.find('th.vorname');
        var kuerzel = plan.find('th.kuerzel');
        var zeitraum = plan.find('th.zeitraum');

        var topLeft = plan.find('thead th.top-left-sticky').first();
        var ausbildungsBerufe = plan.find('tbody th.ausbildungsberuf');

        var nachnameHidden = nachname.is(':hidden');
        var vornameHidden = vorname.is(':hidden');
        var kuerzelHidden = kuerzel.is(':hidden');
        var zeitraumHidden = zeitraum.is(':hidden');

        var visibleColumnsNumber = !nachnameHidden + !vornameHidden + !kuerzelHidden + !zeitraumHidden;
        topLeft.attr('colspan', visibleColumnsNumber);
        ausbildungsBerufe.attr('colspan', visibleColumnsNumber);

        if (visibleColumnsNumber === 1) {
            let checkbox = $('#Information input:checkbox:checked');
            checkbox.closest('label').addClass('disabled');
            checkbox.prop('disabled', true);
        }

        var einheit = 'px';
        var order = 0;

        if (!nachnameHidden) {
            order++;
            nachname.css(
                { 'left': eval('c' + getPosition(order)) + einheit
            });
        }

        if (!vornameHidden) {
            order++;
            vorname.css(
                { 'left': eval('c' + getPosition(order)) + einheit
            });
        }

        if (!kuerzelHidden) {
            order++;
            kuerzel.css(
                { 'left': eval('c' + getPosition(order)) + einheit
            });
        }

        if (!zeitraumHidden) {
            order++;
            zeitraum.css(
                { 'left': eval('c' + getPosition(order)) + einheit
            });
        }

        if (order === 4) {
            document.cookie = 'hidden-columns=-; max-age=2592000';
            return;
        }

        // Bug: Zeitraum-Spalte wird nicht richtig positioniert
        if (!zeitraumHidden && !kuerzelHidden) {

            let kuerzelLeft = kuerzel.css('left');

            zeitraum.css(
                'left',
                parseInt(kuerzelLeft.substring(0, kuerzelLeft.length - 2)) + kuerzel.innerWidth() + 'px'
            );

            topLeft.attr('colspan', parseInt(topLeft.attr('colspan')) + 1);
            ausbildungsBerufe.attr('colspan', parseInt(ausbildungsBerufe.attr('colspan')) + 1);
        }

        // Cookies sollen nur gesetzt werden, wenn Benutzer manuell auf die Checkboxen geklickt hat
        if (e.which !== 1) return;

        let hiddenColumns = [];
        nachnameHidden ? hiddenColumns.push('nachname') : null;
        vornameHidden ? hiddenColumns.push('vorname') : null;
        kuerzelHidden ? hiddenColumns.push('kuerzel') : null;
        zeitraumHidden ? hiddenColumns.push('zeitraum') : null;
        document.cookie = `hidden-columns=${ hiddenColumns.join('-') }; max-age=2592000`;
    });

    function getPosition(order) {
        switch (order) {
            case 1:
                return 'First';
            case 2:
                return 'Second';
            case 3:
                return 'Third';
            case 4:
                return 'Fourth';
            default:
                return '';
        }
    }

    /**
     * @seen https://www.w3schools.com/js/js_cookies.asp
     */
    function getCookie(cname) {
        var name = cname + '=';
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        var caLength = ca.length;
        for (let i = 0; i < caLength; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return false;
    }
});
