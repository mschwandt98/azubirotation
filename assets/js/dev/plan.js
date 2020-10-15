jQuery(function($) {
    $(document).ready(function() {

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

            $("#Popup").empty().append(content);

            if (position !== 0) {
                $("#Popup").css({ top: position.top, left: position.left + 64 });
            }
        }

        /**
         * Holt die Farbe einer Abteilung anhand dessen ID.
         *
         * @param {number} id Die ID der Abteilung.
         *
         * @return {string} Der HEX-Code zur Farbe der Abteilung.
         */
        function GetAbteilungsFarbe(id) {

            var farbe;

            Abteilungen.forEach(abteilung => {
                if (abteilung.ID == id) {
                    farbe = abteilung.Farbe;
                    return;
                }
            });

            return farbe;
        }

        /**
         * Entfernt die Klasse "selected" von allen ausgewählten Plan-Phasen.
         */
        function RemoveSelectedStatus() {
            tdItems.forEach(item => {
                $(item).removeClass("selected");
            });
            tdItems.length = 0;
        }

        /**
         * Handhabung der Anwendung bei Fehlern.
         * Versteckt den Loading-Spinner und zeigt die Fehlernachricht für 10
         * Sekunden an.
         *
         * @param {string} errorMessage Die Fehlernachricht, die angezeigt
         *                              werden soll.
         */
        function HandleError(errorMessage = "Es trat ein unbekannter Fehler auf.") {

            $("#LoadingSpinner").hide();
            var emb = $("#ErrorMessageBox");
            emb.find(".message").text(errorMessage);
            emb.show();
            setTimeout(() => { emb.fadeOut().text(); }, 10000);
        }

        /**
         * Holt initial alle Abteilungen per AJAX-Anfrage des Typs GET.
         */
        $.get(APIABTEILUNG + "Get", function(data) {
            Abteilungen = JSON.parse(data);
        });

        /**
         * Holt initial alle Ansprechpartner per AJAX-Anfrage des Typs GET.
         */
        $.get(APIANSPRECHPARTNER + "Get", function(data) {
            Ansprechpartner = JSON.parse(data);
        });

        /**
         * Bei Klick aufs Dokument werden alle ausgewählten Plan-Einheiten
         * (Phasen), die Informationen des Info-Buttons und das Popup versteckt,
         * sofern der Klick außerhalb der Planung ist.
         */
        $(document).on("click", function(e) {
            if(!$(e.target).closest("#Plan table").length && !$(e.target).closest("#Popup").length) {

                if ($(e.target).parents(".set-abteilung-popup").length === 0 &&
                    $(e.target).parents(".set-ansprechpartner-popup").length === 0 &&
                    $(e.target).parents(".context-popup").length === 0 &&
                    $(e.target).parents(".set-mark-popup").length === 0) {
                    $("#Popup > div").remove();
                    RemoveSelectedStatus();
                }
            }

            if(!$(e.target).closest("#InfoButton").length) {
                $("#InfoButton > div").hide(TIME);
            }
        });

        /**
         * Blendet die Informationen des Info-Buttons ein.
         */
        $("#InfoButton").on("click", function() {
            $(this).find("> div").show(TIME);
        });

        /**
         * Beim Klick auf eine Phase wird ein Popup zur Auswahl der Abteilung
         * erzeugt.
         *
         * @param {Event} e Das ausgelöste click-Event.
         */
        $("#Plan").on("click", ".plan-phase", function(e) {

            if ($(e.target).parents(".plan-phase").length > 0) return;
            if ($(e.target).parents(".set-abteilung-popup").length > 0) return;
            if ($(e.target).parents(".set-ansprechpartner-popup").length > 0) return;
            if ($(e.target).parents(".context-popup").length > 0) return;
            if ($(e.target).parents(".set-mark-popup").length > 0) return;

            // 1 = Linksklick und Prüfung auf undefined, falls diese Funktion im Code per .click() ausgelöst wurde
            if (e.which === 1 || typeof e.which === "undefined") {

                $("#Popup > div").remove();

                var el = $(this);
                el.addClass("selected");
                tdItems.push(el);

                var popup = $("<div></div>").addClass("set-abteilung-popup vertical-scroll");
                var abteilungenList = $("<ul></ul>");

                Abteilungen.forEach(abteilung => {
                    abteilungenList.append(
                        $("<li></li>")
                            .attr("data-id", abteilung.ID)
                            .text(abteilung.Bezeichnung)
                    ).append(
                        $("<hr>")
                    );
                });

                abteilungenList.append($("<li></li>").text("Löschen").css({ color: "red" }));
                popup.append(abteilungenList);
                SetPopupContent(popup, el.position());
            }
        });

        /**
         * Sofern die Steuerungs-Taste (STRG/CTRL) gedrückt wird, wird bei der
         * aktuellen Phase das Attribut draggable auf true gesetzt.
         * Ansonsten wird das Attribut draggable entfernt und eine globale
         * Hilfvariable für andere Funktionen auf true gesetzt.
         *
         * @param {Event} e Das ausgelöste mousedown-Event.
         */
        $("#Plan").on("mousedown", ".plan-phase", function(e) {
            if ($(e.target).parents(".plan-phase").length > 0) return;
            RemoveSelectedStatus();

            var el = $(this);

            if (e.ctrlKey && el.attr("data-id-abteilung")) {
                el.attr("draggable", "true");
            } else {
                el.removeAttr("draggable");
                clicking = true;
            }
        });

        /**
         * Die aktuelle Phase wird als ausgewählt markiert und in ein Array mit
         * allen ausgewählten Phasen aufgenommen.
         *
         * @param {Event} e Das ausgelöste mousemove-Event.
         */
        $("#Plan").on("mousemove", ".plan-phase", function(e) {

            if (!clicking) return;
            var currentTd = $(e.target);

            if (tdItems.length < 1) {
                currentTd.addClass("selected");
                tdItems.push(currentTd);
            } else {

                if ($(tdItems[0]).closest("tr").data("id") == currentTd.closest("tr").data("id")) {

                    var exists = false;

                    if (currentTd.parents(".plan-phase").length > 0) {
                        currentTd = currentTd.parents(".plan-phase");
                    }

                    tdItems.forEach(function(item) {
                        if ($(item).data("date") === currentTd.data("date")) {
                            exists = true;
                        }
                    });

                    if (!exists) {
                        currentTd.addClass("selected");
                        tdItems.push(currentTd);
                    }
                }
            }
        });

        /**
         * Bei der zuletzt ausgewählten Phase wird das click-Event ausgelöst und
         * die globale Hilfsvariable "clicking" auf false gesetzt.
         *
         * @param {Event} e Das ausgelöste mouseup-Event.
         */
        $("#Plan").on("mouseup", ".plan-phase", function(e) {
            if ($(e.target).parents(".plan-phase").length > 0) return;

            $(tdItems[tdItems.length - 1]).click();
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
        $("#Plan").on("contextmenu", ".plan-phase", function(e) {

            e.preventDefault();
            if ($(e.target).parents(".plan-phase").length > 0) return;
            if ($(e.target).parents(".set-abteilung-popup").length > 0) return;
            if ($(e.target).parents(".set-ansprechpartner-popup").length > 0) return;
            if ($(e.target).parents(".set-mark-popup").length > 0) return;
            if ($(e.target).parents(".context-popup").length > 0) return;

            var el = $(this);
            el.addClass("selected");
            tdItems.push(el);

            var popup = $("<div></div>").addClass("context-popup");
            var contextList = $("<ul></ul>");

            if (el.find(".plan-mark").length > 0) {
                contextList.append($("<li></li>").text("Termin umbenennen").data("update", "true"));
                contextList.append($("<hr>"));
                contextList.append($("<li></li>").text("Termin löschen").data("delete", "true"));
            } else {
                contextList.append($("<li></li>").text("Termin setzen").data("add", "true"));
            }

            popup.append(contextList);

            SetPopupContent(popup, el.position());
        });

        /**
         * Führt die angeklickte Option des Kontext-Menüs aus.
         */
        $("#Popup").on("click", ".context-popup li", function() {

            var el = $(this);
            var text = "";

            if (el.data("add")) {
                text = "Terminbezeichnung";
            } else if (el.data("update")) {
                text = "Neue Terminbezeichnung";
            } else if (el.data("delete")) {
                $(tdItems[0]).empty();
                $("#Popup").empty();
                RemoveSelectedStatus();
                return;
            }

            var wrapper = $("<div></div>").addClass("set-mark-popup")
                .append(
                    $("<form></form>").append(
                        $("<label></label>").append(
                                $("<div></div>").text(text)
                            ).append(
                                $('<input type="text" />').css({ minWidth: "100%" }).attr("required", "true")
                            )
                        ).append(
                        "<br>"
                        ).append(
                            $('<input type="button" value="Termin setzen" />')
                        )
                );

            SetPopupContent(wrapper);
        });

        /**
         * Die Bezeichnung für den Termin wird in der Phase eingetragen.
         */
        $("#Popup").on("click", '.set-mark-popup input[type="button"]', function(e) {

            e.preventDefault();
            var markerBezeichnung = $("#Popup .set-mark-popup").find('input[type="text"]').val();
            var td = $(tdItems[0]);

            td.find(".plan-mark").remove();
            td.append(
                $("<div></div>").attr("title", markerBezeichnung).addClass("plan-mark")
            );
            td.addClass("changed");

            $("#Popup").empty();
            RemoveSelectedStatus();
        });

        /**
         * Die angeklickte Abteilung wird in der Phase gesetzt, indem das
         * data-Attribute "id-abteilung" gesetzt wird und der Hintergrund sowie
         * die Border in der Abteilungsfarbe markiert wird.
         * Zudem wird ein Popup-Formular erstellt, in der ein Ansprechpartner
         * für die Phase ausgewählt werden kann. Es werden nur Ansprechpartner
         * angezeigt, die für die angeklickte Abteilung als Ansprechpartner
         * gesetzt sind.
         */
        $("#Popup").on("click", ".set-abteilung-popup li", function() {

            var el = $(this);

            if (!el.data("id")) {

                tdItems.forEach(item => {
                    $(item).removeAttr("style data-id-abteilung data-id-ansprechpartner draggable")
                        .addClass("changed")
                        .removeClass("selected")
                        .empty();
                });
                return;
            }

            tdItems.forEach(item => {

                $(item).attr("data-id-abteilung", el.data("id"))
                .attr(
                    "style",
                    "background-color: " + GetAbteilungsFarbe(el.data("id")) +"; " +
                    "border-left-color: " + GetAbteilungsFarbe(el.data("id")) + "; " +
                    "border-right-color: " + GetAbteilungsFarbe(el.data("id")) + ";"
                )
                .addClass("changed");
            });

            var popupAnsprechpartner = $("<div></div>").addClass("set-ansprechpartner-popup vertical-scroll");
            var ansprechpartnerList = $("<ul></ul>");
            var ansprechpartnerExist = false;

            Ansprechpartner.forEach(ansprechpartner => {

                if (ansprechpartner.ID_Abteilung == el.data("id")) {

                    ansprechpartnerExist = true;
                    ansprechpartnerList.append(
                        $("<li></li>")
                            .attr("data-id", ansprechpartner.ID)
                            .text(ansprechpartner.Name)
                    ).append(
                        $("<hr>")
                    );
                }
            });

            if (ansprechpartnerExist) {
                ansprechpartnerList.find(":last-child").remove();
                popupAnsprechpartner.append(ansprechpartnerList);
                SetPopupContent(popupAnsprechpartner);
            } else {
                SetPopupContent("");
            }
        });

        /**
         * Der angeklickte Ansprechpartner wird bei der aktuellen Phase im
         * data-Attribut "id-ansprechpartner" gesetzt.
         */
        $("#Popup").on("click", ".set-ansprechpartner-popup li", function() {

            tdItems.forEach(item => {
                $(item).attr("data-id-ansprechpartner", $(this).data("id"))
                    .addClass("changed")
                    .empty();
            })

            RemoveSelectedStatus();
            $(this).closest(".set-ansprechpartner-popup").remove();
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
        $("#SavePlan").on("click", function() {

            $("#LoadingSpinner").show();

            var azubiRows = $("#Plan tr.azubi");
            var azubis = [];

            azubiRows.each(index => {

                var row = $(azubiRows[index]);
                var phaseDivs = row.find(".plan-phase.changed");
                var phases = [];

                phaseDivs.each(index => {

                    // .attr() anstatt .data(), da jQuery nicht direkt im Element sucht und bei gelöschten Phasen
                    // dennoch eine ID für die Abteilung und für den Ansprechpartner zurückgibt
                    let phase = $(phaseDivs[index]);
                    let id_abteilung = phase.attr("data-id-abteilung");

                    let markierung = phase.find(".plan-mark");
                    if (markierung.length > 0) {
                        var markierungBezeichnung = markierung.attr("title");
                    }

                    if (id_abteilung) {
                        phases.push({
                            date: phase.data("date"),
                            id_abteilung: id_abteilung,
                            id_ansprechpartner: phase.attr("data-id-ansprechpartner"),
                            markierung: markierungBezeichnung ?? null
                        });
                    } else {
                        phases.push({
                            date: phase.data("date")
                        });
                    }
                });

                if (phases.length > 0) {
                    azubis.push({
                        id: row.data("id"),
                        phasen: phases
                    });
                }
            });

            $.ajax({
                type: "POST",
                url: API + "Plan/Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    azubis: azubis
                },
                success: function(response) {
                    $("#Plan").html(response);
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen der Daten auf.");
                }
            });
        });

        /**
         * Führt eine AJAX-Anfrage des Typs POST aus, durch die alle Azubis und
         * Ansprechpartner darüber informiert werden, das sich die Planung
         * geändert hat.
         * Sofern es zu einem Fehler beim Versenden der Emails kommt, wird eine
         * Fehlernachricht angezeigt.
         */
        $("#SendMail").on("click", function() {

            var button = $(this);
            $("#LoadingSpinner").show();

            $.ajax({
                type: "POST",
                url: API + "Plan/SendMail",
                data: { csrfToken: $("#CsrfToken").val() },
                success: function() {
                    var messageSpan = button.siblings("span");
                    messageSpan.fadeIn();
                    setTimeout(_ => { messageSpan.fadeOut(); }, 5000);
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Versenden der Benachrichtigungen auf.");
                }
            });
        });

        /**
         * Führt eine AJAX-Anfrage des Typs POST aus, durch welche die Planung
         * auf Verstöße gegen Richtlinien geprüft wird.
         * Sofern als Antwort nicht "true" zurückkommt, wird die Antwort wie ein
         * HTML-Element behandelt und in das Element mit der ID "PlanErrors"
         * eingefügt. Dieses Element wird dann eingeblendet.
         * Sofern bei der Anfrage ein Fehler geworfen wird, wird eine
         * Fehlernachricht angezeigt.
         */
        $("#TestPlan").on("click", function() {

            $("#LoadingSpinner").show();
            $("#PlanErrors").hide();

            $.ajax({
                type: "POST",
                url: API + "Plan/Test",
                data: { csrfToken: $("#CsrfToken").val() },
                success: function(response) {
                    if (response != true) {
                        $("#PlanErrors").html(response).fadeIn();
                    }
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler bei der Authentifizierung auf.");
                }
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
        $("#Plan").on("dragstart", ".plan-phase", function(e) {

            if (!e.ctrlKey) return;

            draggedTds.length = 0;
            clicking = false;

            var el = $(this);
            var tempEl = el;
            draggedTds.push(tempEl);

            while (true) {
                if (tempEl.prev() !== null && tempEl.prev().attr("data-id-abteilung") === el.attr("data-id-abteilung")) {
                    tempEl = tempEl.prev();
                    draggedTds.push(tempEl);
                } else {
                    break;
                }
            };

            tempEl = el;
            while (true) {
                if (tempEl.next() !== null && tempEl.next().attr("data-id-abteilung") === el.attr("data-id-abteilung")) {
                    tempEl = tempEl.next();
                    draggedTds.push(tempEl);
                } else {
                    break;
                }
            };

            if (draggedTds.length > 1) {
                el.css({ minWidth: el.width() * draggedTds.length + "px" });
            }

            setTimeout((function(el) {
                return function() {
                    el.css({ minWidth: "" });
                }
            })(el), 1);

            e.originalEvent.dataTransfer.effectAllowed = "move";
            e.originalEvent.dataTransfer.setData("farbe", el.css("backgroundColor"));
            e.originalEvent.dataTransfer.setData("id-abteilung", el.attr("data-id-abteilung"));
            e.originalEvent.dataTransfer.setData("id-ansprechpartner", el.attr("data-id-ansprechpartner"));
        });

        /**
         * Setzt dem Dropeffekt "move", sofern das aktuelle Event nicht das
         * originale Event ist.
         */
        $("#Plan").on("dragover", ".plan-phase", function(e) {

            if(e.originalEvent.preventDefault) {
                e.preventDefault();
            }

            e.originalEvent.dataTransfer.dropEffect = "move";
            return false;
        });

        /**
         * Die gehoverte Phase wird Betreten mit der Klasse "selected" versehen.
         */
        $("#Plan").on("dragenter", ".plan-phase", function() {
            $(this).addClass("selected");
        });

        /**
         * Bei der gehoverten Phase wird beim Verlassen die Klasse "selected"
         * entfernt.
         */
        $("#Plan").on("dragleave", ".plan-phase", function() {
            $(this).removeClass("selected");
        });

        /**
         * Setzt die beim Ghost-Element hinterlegten Daten bei der gehoverten
         * Phase; Genauer: Die Hintergrund- und Border-Color wird mit gefärbt
         * und die data-Attribute "id-abteilung" und "id-ansprechpartner" werden
         * gesetzt.
         */
        $("#Plan").on("drop", ".plan-phase", function (e) {

            if (e.originalEvent.stopPropagation) {
                e.originalEvent.stopPropagation();
            }

            var target = $(this);

            var farbe = e.originalEvent.dataTransfer.getData("farbe");
            var id_abteilung = e.originalEvent.dataTransfer.getData("id-abteilung");
            var id_ansprechpartner = e.originalEvent.dataTransfer.getData("id-ansprechpartner");

            draggedTds.forEach(td => {
                td.removeAttr("style data-id-abteilung data-id-ansprechpartner draggable")
                    .addClass("changed")
                    .removeClass("selected")
                    .empty();
            })

            var tempTarget = target;
            for (var i = 0; i < draggedTds.length; i++) {

                tempTarget.removeClass("selected")
                    .attr("data-id-abteilung", id_abteilung)
                    .attr("data-id-ansprechpartner", id_ansprechpartner)
                    .addClass("changed")
                    .css({
                        backgroundColor: farbe,
                        borderColor: farbe
                    })
                    .empty();

                if (tempTarget.next() !== null) {
                    tempTarget = tempTarget.next();
                } else {
                    break;
                }
            }

            return false;
        });
    });
});
