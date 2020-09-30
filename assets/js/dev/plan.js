jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";

        var Abteilungen;
        var Ansprechpartner;

        var clicking = false;
        var tdItems = [];

        $.get(API + "Abteilung/Get", function(data) {
            Abteilungen = JSON.parse(data);
        });

        $.get(API + "Ansprechpartner/Get", function(data) {
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

            var popup = $("<div></div>").addClass("set-abteilung-popup").addClass("vertical-scroll");
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
            clicking = true;
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
                    $(item).removeAttr("style")
                        .removeAttr("data-id-abteilung")
                        .removeAttr("data-id-ansprechpartner")
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

            var popupAnsprechpartner = $("<div></div>").addClass("set-ansprechpartner-popup").addClass("vertical-scroll");
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

                    var phase = $(phaseDivs[index]);
                    var id_abteilung = phase.data("id-abteilung");

                    if (id_abteilung) {
                        phases.push({
                            date: phase.data("date"),
                            id_abteilung: id_abteilung,
                            id_ansprechpartner: phase.data("id-ansprechpartner")
                        });
                    } else if (phase.hasClass("deleted-abteilung")) {
                        phases.push({
                            date: phase.data("date"),
                            id_abteilung: null,
                            id_ansprechpartner: null
                        });
                    }
                })

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
                }
            });
        });

        $("#SendMail").on("click", function() {

            $("#LoadingSpinner").show();

            $.ajax({
                type: "POST",
                url: API + "Plan/SendMail",
                data: { csrfToken: $("#CsrfToken").val() },
                success: function(response) {
                    // TODO
                    $("#LoadingSpinner").hide();
                }
            });
        });

        $("#TestPlan").on("click", function() {

            $("#LoadingSpinner").show();

            $.ajax({
                type: "POST",
                url: API + "Plan/Test",
                data: { csrfToken: $("#CsrfToken").val() },
                success: function(response) {
                    if (response != true) {
                        $("#PlanErrors").html(response);
                    }
                    $("#LoadingSpinner").hide();
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
    });
});
