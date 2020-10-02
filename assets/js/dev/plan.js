jQuery(function($) {
    $(document).ready(function() {

        // TODO: Abteilungen und Ansprechpartner nicht beim Laden der Seite holen -> dann wenn sie gebraucht werden!
        var Abteilungen;
        var Ansprechpartner;

        var clicking = false;
        var tdItems = [];

        function HandleError(errorMessage = "Es trat ein unbekannter Fehler auf.") {

            $("#LoadingSpinner").hide();
            var emb = $("#ErrorMessageBox");
            emb.find(".message").text(errorMessage);
            emb.show();
            setTimeout(() => { emb.fadeOut().text(); }, 10000);
        }

        $.get(APIABTEILUNG + "Get", function(data) {
            Abteilungen = JSON.parse(data);
        });

        $.get(APIANSPRECHPARTNER + "Get", function(data) {
            Ansprechpartner = JSON.parse(data);
        });

        $(document).on("click", function(e) {
            if(!$(e.target).closest("#Plan table").length && !$(e.target).closest("#Popup").length) {

                if ($(e.target).parents(".set-abteilung-popup").length === 0 &&
                    $(e.target).parents(".set-ansprechpartner-popup").length === 0) {
                    $("#Popup .set-abteilung-popup").remove();
                    $("#Popup .set-ansprechpartner-popup").remove();
                    RemoveSelectedStatus();
                }
            }
        });

        $("#Plan").on("click", ".plan-phase", function(e) {

            if ($(e.target).parents(".plan-phase").length > 0) return;
            if ($(e.target).parents(".set-abteilung-popup").length > 0) return;
            if ($(e.target).parents(".set-ansprechpartner-popup").length > 0) return;

            $("#Popup .set-abteilung-popup").remove();
            $("#Popup .set-ansprechpartner-popup").remove();

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
            var positionTd = el.position();
            $("#Popup").append(popup).css({ top: positionTd.top, left: positionTd.left + el.width() + 16 });
        });

        $("#Plan").on("mousedown", ".plan-phase", function(e) {
            if ($(e.target).parents(".plan-phase").length > 0) return;
            RemoveSelectedStatus();

            if (e.ctrlKey) {
                $(this).attr("draggable", "true");
            } else {
                $(this).removeAttr("draggable");
                clicking = true;
            }
        });

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

        $("#Plan").on("mouseup", ".plan-phase", function(e) {
            if ($(e.target).parents(".plan-phase").length > 0) return;

            $(tdItems[tdItems.length - 1]).click();
            clicking = false;
        });

        $("#Popup").on("click", ".set-abteilung-popup li", function() {

            var el = $(this);

            if (!el.data("id")) {

                tdItems.forEach(item => {
                    $(item).removeAttr("style data-id-abteilung data-id-ansprechpartner draggable")
                        .addClass("deleted-abteilung")
                        .removeClass("selected")
                        .empty();
                });
                return;
            }

            tdItems.forEach(item => {

                $(item).attr("data-id-abteilung", el.data("id"))
                .attr(
                    "style",
                    "background-color: " + GetAbteilungsFarbe(el.data("id")) + "; border-color: " + GetAbteilungsFarbe(el.data("id"))
                )
                .removeClass("deleted-abteilung");
            });

            var popupAnsprechpartner = $("<div></div>").addClass("set-ansprechpartner-popup vertical-scroll");
            var ansprechpartnerList = $("<ul></ul>");

            Ansprechpartner.forEach(ansprechpartner => {

                if (ansprechpartner.ID_Abteilung == el.data("id")) {

                    ansprechpartnerList.append(
                        $("<li></li>")
                            .attr("data-id", ansprechpartner.ID)
                            .text(ansprechpartner.Name)
                    ).append(
                        $("<hr>")
                    );
                }
            });

            ansprechpartnerList.find(":last-child").remove();

            popupAnsprechpartner.append(ansprechpartnerList);
            $("#Popup").append(popupAnsprechpartner);

            $(".set-abteilung-popup").remove();
        });

        $("#Popup").on("click", ".set-ansprechpartner-popup li", function() {

            tdItems.forEach(item => {
                $(item).attr("data-id-ansprechpartner", $(this).data("id")).empty();
            })

            RemoveSelectedStatus();
            $(this).closest(".set-ansprechpartner-popup").remove();
        });

        $("#SavePlan").on("click", function() {

            $("#LoadingSpinner").show();

            var azubiRows = $("#Plan tr.azubi");
            var azubis = [];

            azubiRows.each(index => {

                var row = $(azubiRows[index]);
                var phaseDivs = row.find(".plan-phase");
                var phases = [];

                phaseDivs.each(index => {

                    // .attr() anstatt .data(), da jQuery nicht direkt im Element sucht und bei gelöschten Phasen
                    // dennoch eine ID für die Abteilung und für den Ansprechpartner zurückgibt
                    let phase = $(phaseDivs[index]);
                    let id_abteilung = phase.attr("data-id-abteilung");

                    if (id_abteilung) {
                        phases.push({
                            date: phase.data("date"),
                            id_abteilung: id_abteilung,
                            id_ansprechpartner: phase.attr("data-id-ansprechpartner")
                        });
                    } else if (phase.hasClass("deleted-abteilung")) {
                        phases.push({
                            date: phase.data("date"),
                            id_abteilung: null,
                            id_ansprechpartner: null
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

        function RemoveSelectedStatus() {
            tdItems.forEach(item => {
                $(item).removeClass("selected");
            });
            tdItems.length = 0;
        }

        // Drag and Drop -----------------------------------------------------------------------------------------------
        var draggedTds;
        $("#Plan").on("dragstart", ".plan-phase", function(e) {

            if (!e.ctrlKey) {
                return;
            }

            draggedTds = [];
            clicking = false;

            var el = $(this);
            var tempEl = el;
            draggedTds.push(tempEl);

            while (true) {
                if (tempEl.prev() !== null && tempEl.prev().data("id-abteilung") === el.data("id-abteilung")) {
                    tempEl = tempEl.prev();
                    draggedTds.push(tempEl);
                } else {
                    break;
                }
            };

            tempEl = el;
            while (true) {
                if (tempEl.next() !== null && tempEl.next().data("id-abteilung") === el.data("id-abteilung")) {
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
            e.originalEvent.dataTransfer.setData("id-abteilung", el.data("id-abteilung"));
            e.originalEvent.dataTransfer.setData("id-ansprechpartner", el.data("id-ansprechpartner"));
        });

        $("#Plan").on("dragover", ".plan-phase", function(e) {

            if(e.originalEvent.preventDefault) {
                e.preventDefault();
            }

            e.originalEvent.dataTransfer.dropEffect = "move";
            return false;
        });

        $("#Plan").on("dragenter", ".plan-phase", function() {
            $(this).addClass("selected");
        });

        $("#Plan").on("dragleave", ".plan-phase", function() {
            $(this).removeClass("selected");
        });

        $("#Plan").on("drop", ".plan-phase", function (e) {

            if (e.originalEvent.stopPropagation) {
                e.originalEvent.stopPropagation();
            }

            var target = $(this);

            var farbe = e.originalEvent.dataTransfer.getData("farbe");
            var id_abteilung = e.originalEvent.dataTransfer.getData("id-abteilung");
            var id_ansprechpartner = e.originalEvent.dataTransfer.getData("id-ansprechpartner");

            var tempTarget = target;

            draggedTds.forEach(td => {
                td.removeAttr("style data-id-abteilung data-id-ansprechpartner draggable")
                    .addClass("deleted-abteilung")
                    .removeClass("selected")
                    .empty();
            })

            for (var i = 0; i < draggedTds.length; i++) {

                tempTarget.removeClass("selected")
                    .attr("data-id-abteilung", id_abteilung)
                    .attr("data-id-ansprechpartner", id_ansprechpartner)
                    .removeClass("deleted-abteilung")
                    .css({
                        backgroundColor: farbe,
                        borderColor: farbe
                    });

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
