$(document).ready(function () {
    // TODO: Abteilungen und Ansprechpartner nicht beim Laden der Seite holen -> dann wenn sie gebraucht werden!
    var Abteilungen;
    var Ansprechpartner;

    var clicking = false;
    var tdItems = [];

    /**
     * Leert das Popup und setzt füllt dieses mit neuem Inhalt. Sofern eine
     * Position übergeben wurde, wird die Position des Popups angepasst.
     *
     * @param {HTMLElement} content Die einzufügenden HTML-Elemente.
     * @param {object} position Die Position, die das Popup bekommen soll.
     */
    function SetPopupContent(content, position = 0) {
        var popup = $('#Popup');
        popup.empty().append(content);

        if (position !== 0) {
            let screenWidth = $('body').width();
            let popupWidth = popup.width();
            let spacing = 64;

            if (position.left + popupWidth + spacing > screenWidth) {
                popup.css({ top: position.top, left: position.left - popupWidth - spacing / 2 });
            } else {
                popup.css({ top: position.top, left: position.left + spacing });
            }
        }
    }

    /**
     * Erstellt ein Popup mit allen Ansprechpartnern für eine Abteilung.
     *
     * @param {number} id   Die ID der Abteilung, zu denen die Ansprechpartner
     *                      ermittelt werden sollen.
     * @param {*} position  Die Position, die das Popup erhalten soll.
     *                      Optional -> muss Position nicht verändern.
     */
    function CreateAnsprechpartnerPopup(id, position = 0) {
        var popupAnsprechpartner = $('<div></div>').addClass('set-ansprechpartner-popup vertical-scroll');
        var ansprechpartnerList = $('<ul></ul>');
        var ansprechpartnerExist = false;

        Ansprechpartner.forEach((ansprechpartner) => {
            if (ansprechpartner.ID_Abteilung == id) {
                ansprechpartnerExist = true;
                ansprechpartnerList
                    .append($('<li></li>').attr('data-id', ansprechpartner.ID).text(ansprechpartner.Name))
                    .append($('<hr>'));
            }
        });

        if (ansprechpartnerExist) {
            ansprechpartnerList.find(':last-child').remove();
            popupAnsprechpartner.append(ansprechpartnerList);
            SetPopupContent(popupAnsprechpartner, position);
        } else {
            SetPopupContent('');
            RemoveSelectedStatus();
        }
    }

    /**
     * Holt die komplette Phase in einer Abteilung.
     *
     * @param {HTMLTableCellElement} currentTd  Die einzelne Phase, zu der
     *                                          die komplette Phase
     *                                          ermittelt werden soll.
     */
    function GetFullPhase(currentTd) {
        var tds = [];
        var el = $(currentTd);
        var tempEl = el;
        tds.push(tempEl);

        while (true) {
            let prevEl = tempEl.prev();
            if (prevEl !== null && prevEl.attr('data-id-abteilung') === el.attr('data-id-abteilung')) {
                tempEl = prevEl;
                tds.push(tempEl);
            } else {
                break;
            }
        }

        tempEl = el;
        while (true) {
            let nextEl = tempEl.next();
            if (nextEl !== null && nextEl.attr('data-id-abteilung') === el.attr('data-id-abteilung')) {
                tempEl = nextEl;
                tds.push(tempEl);
            } else {
                break;
            }
        }

        let idAnsprechpartner = el.attr('data-id-ansprechpartner');
        let returnTds = [];
        $(tds).each((index) => {
            let td = $(tds[index]);
            if (td.attr('data-id-ansprechpartner') === idAnsprechpartner) {
                returnTds.push(td);
            }
        });

        return returnTds;
    }

    /**
     * Entfernt die Klasse 'selected' von allen ausgewählten Plan-Phasen.
     */
    function RemoveSelectedStatus() {
        tdItems.forEach((item) => {
            $(item).removeClass('selected');
        });
        tdItems.length = 0;
    }

    /**
     * Sortiert die Items in tdItems.
     */
    function SortTdItems(a, b) {
        let aDate = $(a).data('date');
        let bDate = $(b).data('date');
        return aDate < bDate ? -1 : aDate > bDate ? 1 : 0;
    }

    /**
     * Holt initial alle Abteilungen per AJAX-Anfrage des Typs GET.
     */
    $.get(APIABTEILUNG + 'Get', function (data) {
        Abteilungen = JSON.parse(data);
    });

    /**
     * Holt initial alle Ansprechpartner per AJAX-Anfrage des Typs GET.
     */
    $.get(APIANSPRECHPARTNER + 'Get', function (data) {
        Ansprechpartner = JSON.parse(data);
    });

    /**
     * Bei Klick aufs Dokument werden alle ausgewählten Plan-Einheiten
     * (Phasen), die Informationen des Info-Buttons und das Popup versteckt,
     * sofern der Klick außerhalb der Planung ist.
     */
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#Plan table').length && !$(e.target).closest('#Popup').length) {
            if (
                !$(e.target).parents('.set-abteilung-popup').length &&
                !$(e.target).parents('.set-ansprechpartner-popup').length &&
                !$(e.target).parents('.context-popup').length &&
                !$(e.target).parents('.set-mark-popup').length
            ) {
                SetPopupContent('');
                RemoveSelectedStatus();
            }
        }
    });

    /**
     * Beim Klick auf eine Phase wird ein Popup zur Auswahl der Abteilung
     * erzeugt.
     *
     * @param {Event} e Das ausgelöste click-Event.
     */
    $('#Plan')
        .on('click', '.plan-phase', function (e) {
            // 1 = Linksklick und Prüfung auf undefined, falls diese Funktion im Code per .click() ausgelöst wurde
            if (e.which === 1 || typeof e.which === 'undefined') {
                var el = $(this);
                el.addClass('selected');
                tdItems.push(el);
                tdItems.sort(SortTdItems);

                var popup = $('<div></div>').addClass('set-abteilung-popup vertical-scroll');
                var abteilungenList = $('<ul></ul>');

                Abteilungen.forEach((abteilung) => {
                    abteilungenList
                        .append($('<li></li>').attr('data-id', abteilung.ID).text(abteilung.Bezeichnung))
                        .append($('<hr>'));
                });

                abteilungenList.append($('<li></li>').text('Löschen').css({ color: 'red' }));
                popup.append(abteilungenList);
                SetPopupContent(popup, el.position());
            }
        })
        .find('.plan-mark')
        .click(function () {
            $(this).closest('.plan-phase').click();
        });

    /**
     * Sofern die Steuerungs-Taste (STRG/CTRL) gedrückt wird, wird bei der
     * aktuellen Phase das Attribut draggable auf true gesetzt.
     * Ansonsten wird das Attribut draggable entfernt und eine globale
     * Hilfvariable für andere Funktionen auf true gesetzt.
     *
     * @param {Event} e Das ausgelöste mousedown-Event.
     */
    $('#Plan').on('mousedown', '.plan-phase', function (e) {
        if ($(e.target).parents('.plan-phase').length > 0) return;
        SetPopupContent('');
        RemoveSelectedStatus();

        var el = $(this);

        if (e.ctrlKey && el.attr('data-id-abteilung')) {
            el.attr('draggable', 'true');
        } else {
            el.removeAttr('draggable');
            clicking = true;
        }
    });

    /**
     * Die aktuelle Phase wird als ausgewählt markiert und in ein Array mit
     * allen ausgewählten Phasen aufgenommen.
     *
     * @param {Event} e Das ausgelöste mousemove-Event.
     */
    $('#Plan').on('mousemove', '.plan-phase', function (e) {
        if (e.which !== 1 && e.which !== 3) return;
        if (!clicking) return;
        var currentTd = $(e.target);

        if (e.which === 3 && !currentTd.data('id-abteilung')) return;

        if (tdItems.length < 1) {
            currentTd.addClass('selected');
            tdItems.push(currentTd);
        } else {
            let lastAddedTd = $(tdItems[tdItems.length - 1]);
            let currentTdDate = currentTd.data('date');
            let lastAddedTdDate = lastAddedTd.data('date');

            if (lastAddedTd.closest('tr').data('id') != currentTd.closest('tr').data('id')) return;
            if (lastAddedTdDate == currentTdDate) return;

            let nextTd = lastAddedTd.next();
            let prevTd = lastAddedTd.prev();

            if (
                (prevTd.data('date') == currentTdDate && prevTd.hasClass('selected')) ||
                (nextTd.data('date') == currentTdDate && nextTd.hasClass('selected'))
            ) {
                lastAddedTd.removeClass('selected');
                tdItems.pop();
            } else if (prevTd.data('date') == currentTdDate || nextTd.data('date') == currentTdDate) {
                // Falls ein Child-Element ausgewählt wurde
                if (currentTd.parents('.plan-phase').length > 0) {
                    currentTd = currentTd.parents('.plan-phase');
                }

                let exists = false;
                tdItems.forEach(function (item) {
                    if ($(item).data('date') === currentTdDate) {
                        exists = true;
                    }
                });

                if (!exists) {
                    currentTd.addClass('selected');
                    tdItems.push(currentTd);
                }
            } else if (currentTdDate < lastAddedTdDate) {
                let td = lastAddedTd;

                while (currentTdDate < td.data('date')) {
                    td = td.prev();
                    td.addClass('selected');
                    tdItems.push(td);
                }
            } else if (currentTdDate > lastAddedTdDate) {
                let td = lastAddedTd;

                while (currentTdDate > td.data('date')) {
                    td = td.next();
                    td.addClass('selected');
                    tdItems.push(td);
                }
            }
        }
    });

    /**
     * Bei der zuletzt ausgewählten Phase wird das click-Event ausgelöst und
     * die globale Hilfsvariable 'clicking' auf false gesetzt.
     *
     * @param {Event} e Das ausgelöste mouseup-Event.
     */
    $('#Plan').on('mouseup', '.plan-phase', function (e) {
        if ($(e.target).parents('.plan-phase').length > 0) return;

        if (e.which === 1) {
            $(tdItems[tdItems.length - 1]).click();
        }
        clicking = false;
    });

    /**
     * Erstellt ein Kontext-Menü zum Setzen eines Termins. Sofern bereits
     * ein Termin zur ausgewählten Phase existiert, kann dieser bearbeitet
     * oder gelöscht werden.
     * Die jeweiligen Optionen werden in einem erstellten Popup gelistet.
     *
     * @param {Event} e Das ausgelöste contextmenu-Event.
     */
    $('#Plan')
        .on('contextmenu', '.plan-phase', function (e) {
            e.preventDefault();
            if ($(e.target).parents('.plan-phase').length > 0) return;
            if ($(e.target).parents('.set-abteilung-popup').length > 0) return;
            if ($(e.target).parents('.set-ansprechpartner-popup').length > 0) return;
            if ($(e.target).parents('.set-mark-popup').length > 0) return;
            if ($(e.target).parents('.context-popup').length > 0) return;

            var el = $(this);

            if (!el.data('id-abteilung')) return;

            el.addClass('selected');

            if (!tdItems.length) {
                tdItems.push(el);
            }

            var popup = $('<div></div>').addClass('context-popup');
            var contextList = $('<ul></ul>');

            if (tdItems.length === 1) {
                if (el.attr('data-id-ansprechpartner')) {
                    contextList.append(
                        $('<li></li>').text('Ansprechpartner ersetzen').data('update-ansprechpartner', 'true')
                    );
                    contextList.append($('<hr>'));
                    contextList.append(
                        $('<li></li>').text('Ansprechpartner löschen').data('delete-ansprechpartner', 'true')
                    );
                } else {
                    contextList.append(
                        $('<li></li>').text('Ansprechpartner setzen').data('add-ansprechpartner', 'true')
                    );
                }

                contextList.append($('<hr>'));
            }

            if (el.find('.plan-mark').length > 0) {
                contextList.append($('<li></li>').text('Termin umbenennen').data('update-termin', 'true'));
                contextList.append($('<hr>'));
                contextList.append($('<li></li>').text('Termin löschen').data('delete-termin', 'true'));
            } else {
                contextList.append($('<li></li>').text('Termin setzen').data('add-termin', 'true'));
            }

            popup.append(contextList);
            SetPopupContent(popup, el.position());
        })
        .find('.ansprechpartner-name, .plan-mark')
        .contextmenu(function (e) {
            $(this).closest('.plan-phase').contextmenu();
        });

    /**
     * Führt die angeklickte Option des Kontext-Menüs aus.
     */
    $('#Popup').on('click', '.context-popup li', function () {
        var el = $(this);
        var td = $(tdItems[0]);

        if (el.data('add-ansprechpartner') || el.data('update-ansprechpartner') || el.data('delete-ansprechpartner')) {
            tdItems.length = 0;
            tdItems = tdItems.concat(GetFullPhase(td));

            if (el.data('delete-ansprechpartner')) {
                $(tdItems).each((index) => {
                    let selectedTd = $(tdItems[index]);
                    selectedTd.removeAttr('data-id-ansprechpartner').addClass('changed');
                    selectedTd.find('.ansprechpartner-name').remove();
                });
                SetPopupContent('');
                RemoveSelectedStatus();
                return;
            }

            CreateAnsprechpartnerPopup(td.data('id-abteilung'), td.position());
            return;
        }

        if (el.data('delete-termin')) {
            tdItems.forEach((item) => {
                let jItem = $(item);
                jItem.find('.plan-mark').remove();
                jItem.addClass('changed');
            });

            SetPopupContent('');
            RemoveSelectedStatus();
            return;
        }

        if (el.data('add-termin') || el.data('update-termin')) {
            let wrapper = $('<div></div>')
                .addClass('set-mark-popup')
                .append(
                    $('<form></form>')
                        .append(
                            $('<label></label>')
                                .append(
                                    $('<div></div>').text(
                                        (el.data('update-termin') ? 'Neue ' : '') + 'Terminbezeichnung'
                                    )
                                )
                                .append($('<input type="text" />').css({ minWidth: '100%' }).attr('required', 'true'))
                        )
                        .append('<br>')
                        .append($('<input type="button" value="Termin setzen" />'))
                );

            SetPopupContent(wrapper);
        }
    });

    /**
     * Die Bezeichnung für den Termin wird in der Phase eingetragen.
     */
    $('#Popup').on('click', '.set-mark-popup input[type="button"]', function (e) {
        e.preventDefault();
        var markerBezeichnung = $('#Popup .set-mark-popup').find('input[type="text"]').val();
        let tdItemsLength = tdItems.length;

        for (let i = 0; i < tdItemsLength; i++) {
            let jItem = $(tdItems[i]);

            jItem.find('.plan-mark').remove();
            jItem.addClass('changed').append(
                $('<div></div>')
                    .attr('title', markerBezeichnung)
                    .addClass('plan-mark ' + (tdItemsLength > 1 ? 'icon-plan-mark' : 'icon-plan-mark-separat'))
            );
        }

        $('#Popup').empty();
        RemoveSelectedStatus();
    });

    /**
     * Die angeklickte Abteilung wird in der Phase gesetzt, indem das
     * data-Attribute 'id-abteilung' gesetzt wird und der Hintergrund sowie
     * die Border in der Abteilungsfarbe markiert wird.
     * Zudem wird ein Popup-Formular erstellt, in der ein Ansprechpartner
     * für die Phase ausgewählt werden kann. Es werden nur Ansprechpartner
     * angezeigt, die für die angeklickte Abteilung als Ansprechpartner
     * gesetzt sind.
     */
    $('#Popup').on('click', '.set-abteilung-popup li', function () {
        var idAbteilung = $(this).data('id');

        if (!idAbteilung) {
            tdItems.forEach((item) => {
                $(item)
                    .removeAttr('data-id-abteilung data-id-ansprechpartner draggable title')
                    .addClass('changed')
                    .removeClass('selected abteilung-' + idAbteilung)
                    .find('*')
                    .not('.plan-mark')
                    .remove();
            });
            SetPopupContent('');
            return;
        }

        tdItems.forEach((item) => {
            $(item)
                .removeAttr('class title')
                .attr('data-id-abteilung', idAbteilung)
                .addClass('plan-phase changed abteilung-' + idAbteilung)
                .find('*')
                .not('.plan-mark')
                .remove();
        });

        tdItems.sort(SortTdItems);
        var lastTd = tdItems[tdItems.length - 1].next();

        if (lastTd.attr('data-id-abteilung') != idAbteilung) {
            Ansprechpartner.some((ansprechpartner) => {
                if (ansprechpartner.ID == lastTd.attr('data-id-ansprechpartner')) {
                    lastTd.append($('<span></span>').addClass('ansprechpartner-name').text(ansprechpartner.Name));
                    return;
                }
            });
        }

        CreateAnsprechpartnerPopup(idAbteilung);
    });

    /**
     * Der angeklickte Ansprechpartner wird bei der aktuellen Phase im
     * data-Attribut 'id-ansprechpartner' gesetzt.
     */
    $('#Popup').on('click', '.set-ansprechpartner-popup li', function () {
        var idAnsprechpartner = $(this).data('id');

        tdItems.forEach((item) => {
            let jItem = $(item);
            jItem.attr('data-id-ansprechpartner', idAnsprechpartner).addClass('changed');
            jItem.find('.ansprechpartner-name').remove();
        });

        tdItems.sort(SortTdItems);

        Ansprechpartner.some((ansprechpartner) => {
            if (ansprechpartner.ID == idAnsprechpartner) {
                let prevTd;

                if ((prevTd = tdItems[0].prev())) {
                    if (prevTd.attr('data-id-ansprechpartner') == idAnsprechpartner) {
                        return;
                    }

                    $(tdItems[0]).append(
                        $('<span></span>').addClass('ansprechpartner-name').text(ansprechpartner.Name)
                    );
                } else {
                    $(tdItems[0]).append(
                        $('<span></span>').addClass('ansprechpartner-name').text(ansprechpartner.Name)
                    );
                }

                return;
            }
        });

        SetPopupContent('');
        RemoveSelectedStatus();
    });

    /**
     * Alle Daten der Planung werden gesammelt und für die API zum Speichern
     * der Planung aufbereitet. Die aufbereiteten Daten werden mittels
     * AJAX-Anfrage des Typs POST gespeichert.
     * Wenn die Speicherung der Planung erfolgreich war, wird die Planung
     * von einer vom Server zurückgegeben Planung ersetzt. (Sync)
     * Falls es zu Fehlern beim Speichern der Planung kam, wird eine
     * Fehlernachricht angezeigt.
     */
    $('#SavePlan').on('click', function () {
        $('#LoadingSpinner').show();

        var azubiRows = $('#Plan tr.azubi');
        var azubis = [];

        azubiRows.each((index) => {
            var row = $(azubiRows[index]);
            var phaseDivs = row.find('.plan-phase.changed');
            var phases = [];

            phaseDivs.each((index) => {
                // .attr() anstatt .data(), da jQuery nicht direkt im Element sucht und bei gelöschten Phasen
                // dennoch eine ID für die Abteilung und für den Ansprechpartner zurückgibt
                let phase = $(phaseDivs[index]);
                let id_abteilung = phase.attr('data-id-abteilung');
                let termin = phase.find('.plan-mark');

                if (id_abteilung) {
                    let terminTitle = termin.attr('title');

                    phases.push({
                        date: phase.data('date'),
                        id_abteilung: id_abteilung,
                        id_ansprechpartner: phase.attr('data-id-ansprechpartner'),
                        termin: termin.length && termin.hasClass('icon-plan-mark') ? terminTitle : null,
                        termin_separat: termin.length && termin.hasClass('icon-plan-mark-separat') ? terminTitle : null,
                    });
                } else {
                    phases.push({
                        date: phase.data('date'),
                    });
                }
            });

            if (phases.length > 0) {
                azubis.push({
                    id: row.data('id'),
                    phasen: phases,
                });
            }
        });

        $.ajax({
            type: 'POST',
            url: API + 'Plan/Save',
            data: {
                csrfToken: $('#CsrfToken').val(),
                azubis: azubis,
            },
            success: function (response) {
                $('#Plan').html(response);
                $('#LoadingSpinner').hide();
                $('#LastTimeUpdated').text(`Stand: ${new Date().toLocaleDateString()}`);
            },
            error: function () {
                HandleError('Es traten Fehler beim Anlegen der Daten auf.');
            },
        });
    });

    /**
     * Führt eine AJAX-Anfrage des Typs POST aus, durch die alle Azubis und
     * Ansprechpartner darüber informiert werden, das sich die Planung
     * geändert hat.
     * Sofern es zu einem Fehler beim Versenden der Emails kommt, wird eine
     * Fehlernachricht angezeigt.
     */
    $('#SendMail').on('click', function () {
        if (
            confirm(
                'Mit Bestätigung dieser Meldung, werden Benachrichtigungs-Emails an alle Ansprechpartner und Auszubildende gesendet.'
            )
        ) {
            $('#LoadingSpinner').show();

            $.ajax({
                type: 'POST',
                url: API + 'Plan/SendMail',
                data: { csrfToken: $('#CsrfToken').val() },
                success: function () {
                    $('#LoadingSpinner').hide();
                },
                error: function () {
                    HandleError('Es traten Fehler beim Versenden der Benachrichtigungen auf.');
                },
            });
        }
    });

    /**
     * Führt eine AJAX-Anfrage des Typs POST aus, durch welche die Planung
     * auf Verstöße gegen Richtlinien geprüft wird.
     * Sofern als Antwort nicht 'true' zurückkommt, wird die Antwort wie ein
     * HTML-Element behandelt und in das Element mit der ID 'PlanErrors'
     * eingefügt. Dieses Element wird dann eingeblendet.
     * Sofern bei der Anfrage ein Fehler geworfen wird, wird eine
     * Fehlernachricht angezeigt.
     */
    $('#TestPlan').on('click', function () {
        if ($('#Plan tr.azubi .plan-phase.changed').length > 0) {
            $('#SavePlan').click();
        }

        $('#LoadingSpinner').show();
        $('#PlanErrors').hide();

        $.ajax({
            type: 'POST',
            url: API + 'Plan/Test',
            data: { csrfToken: $('#CsrfToken').val() },
            success: function (response) {
                if (response == true) {
                    $('#PlanErrors')
                        .html(
                            $('<div></div>')
                                .css({ color: 'limegreen' })
                                .text('In der Planung konnten keine Fehler gefunden werden.')
                        )
                        .fadeIn();
                    setTimeout((_) => {
                        $('#PlanErrors > div').fadeOut();
                    }, 5000);
                } else {
                    $('#PlanErrors').html(response).fadeIn();
                    $('html, body').animate(
                        {
                            scrollTop: $('#PlanErrors').offset().top,
                        },
                        TIME
                    );
                }
                $('#LoadingSpinner').hide();
            },
            error: function () {
                HandleError('Es traten Fehler bei der Authentifizierung auf.');
            },
        });
    });

    $('#PlanErrors').on('click', 'input[type="checkbox"]', function () {
        let checkbox = $(this);

        $.ajax({
            type: 'POST',
            url: API + 'Plan/MarkError',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id_error: checkbox.data('id-error'),
                bool: checkbox.prop('checked'),
            },
        });
    });

    // Drag and Drop -----------------------------------------------------------------------------------------------
    var draggedTds = [];

    /**
     * Untersucht die nebenstehenden Phasen nach den Abteilungen solange bis
     * die Abteilungs-ID nicht mit der Abteilungs-ID des Event-Elements
     * übereinstimmt. Anhand der gefunden Phasen wird die Länge des
     * Ghost-Elements gesetzt und mit den für den Drop benötigten Daten
     * versehen Die Daten sind die Farbe, die ID der Abteilung und die ID
     * des Ansprechpartners.
     *
     * @param {Event} e Das ausgelöste dragstart-Event.
     */
    $('#Plan').on('dragstart', '.plan-phase', function (e) {
        if (!e.ctrlKey) return;

        draggedTds.length = 0;
        clicking = false;

        var el = $(this);
        draggedTds = draggedTds.concat(GetFullPhase(el));

        if (draggedTds.length > 1) {
            el.css({ minWidth: (el.innerWidth() + 0.5) * draggedTds.length + 'px' });

            setTimeout(
                (function (el) {
                    return function () {
                        el.css({ minWidth: '' });
                    };
                })(el),
                1
            );

            e.originalEvent.dataTransfer.effectAllowed = 'move';
            e.originalEvent.dataTransfer.dropEffect = 'move';
            e.originalEvent.dataTransfer.setData('id-abteilung', el.attr('data-id-abteilung'));
            e.originalEvent.dataTransfer.setData('id-ansprechpartner', el.attr('data-id-ansprechpartner'));
        }
    });

    /**
     * Setzt dem Dropeffekt 'move', sofern das aktuelle Event nicht das
     * originale Event ist.
     */
    $('#Plan').on('dragover', '.plan-phase', function (e) {
        e.preventDefault();
    });

    /**
     * Setzt die beim Ghost-Element hinterlegten Daten bei der gehoverten
     * Phase; Genauer: Die Hintergrund- und Border-Color wird mit gefärbt
     * und die data-Attribute 'id-abteilung' und 'id-ansprechpartner' werden
     * gesetzt.
     */
    $('#Plan').on('drop', '.plan-phase', function (e) {
        if (e.originalEvent.stopPropagation) {
            e.originalEvent.stopPropagation();
        }

        var target = $(this);
        var id_abteilung = e.originalEvent.dataTransfer.getData('id-abteilung');
        var id_ansprechpartner = e.originalEvent.dataTransfer.getData('id-ansprechpartner');

        draggedTds.forEach((td) => {
            td.removeAttr('data-id-abteilung data-id-ansprechpartner title draggable')
                .addClass('changed')
                .removeClass('selected abteilung-' + id_abteilung)
                .find('*')
                .not('.plan-mark')
                .remove();
        });

        var tempTarget = target;
        var draggedTdsLength = draggedTds.length;
        for (let i = 0; i < draggedTdsLength; i++) {
            tempTarget
                .removeAttr('class title')
                .attr('data-id-abteilung', id_abteilung)
                .attr('data-id-ansprechpartner', id_ansprechpartner)
                .addClass('plan-phase changed abteilung-' + id_abteilung)
                .find('*')
                .not('.plan-mark')
                .remove();

            let nextTempTarget = tempTarget.next();
            if (nextTempTarget !== null) {
                tempTarget = nextTempTarget;
            } else {
                break;
            }
        }

        Ansprechpartner.some((ansprechpartner) => {
            if (ansprechpartner.ID == id_ansprechpartner) {
                $(target).append($('<span></span>').addClass('ansprechpartner-name').text(ansprechpartner.Name));
                return;
            }
        });

        return false;
    });
});
