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
            if(!$(e.target).closest("#Plan table").length) {

                if ($(e.target).parents(".set-abteilung-popup").length === 0 &&
                    $(e.target).parents(".set-ansprechpartner-popup").length === 0) {
                    $("#Plan .set-abteilung-popup").remove();
                    $("#Plan .set-ansprechpartner-popup").remove();
                    RemoveSelectedStatus();
                }
            }
        });

        $("#Plan").on("click", ".plan-phase", function(e) {

            if ($(e.target).parents(".plan-phase").length > 0) return;
            if ($(e.target).parents(".set-abteilung-popup").length > 0) return;
            if ($(e.target).parents(".set-ansprechpartner-popup").length > 0) return;

            $("#Plan .set-abteilung-popup").remove();
            $("#Plan .set-ansprechpartner-popup").remove();

            var el = $(this);
            el.addClass("selected");
            tdItems.push(el);

            var popup = $("<div></div>").addClass("set-abteilung-popup");
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

            abteilungenList.find(":last-child").remove();

            popup.append(abteilungenList);
            el.append(popup);
        });

        $('#Plan .plan-phase').on('mousedown', function(e) {

            if ($(e.target).parents(".plan-phase").length > 0) return;
            if ($(e.target).parents(".set-abteilung-popup").length > 0) return;
            if ($(e.target).parents(".set-ansprechpartner-popup").length > 0) return;

            RemoveSelectedStatus();
            clicking = true;
        });

        $("#Plan .plan-phase").mousemove(function(e) {

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

        $('#Plan .plan-phase').on('mouseup', function(e) {
            if ($(e.target).parents(".plan-phase").length > 0) return;

            $(tdItems[tdItems.length - 1]).click();
            clicking = false;
        });

        $("#Plan").on("click", ".set-abteilung-popup li", function() {

            var el = $(this);

            tdItems.forEach(item => {

                $(item).attr("data-id-abteilung", el.data("id"))
                .attr(
                    "style",
                    "background-color: " + GetAbteilungsFarbe(el.data("id")) + "; border-color: " + GetAbteilungsFarbe(el.data("id"))
                );
            });

            var popupAnsprechpartner = $("<div></div>").addClass("set-ansprechpartner-popup");
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
            el.closest(".plan-phase").append(popupAnsprechpartner);

            $(".set-abteilung-popup").remove();
        });

        $("#Plan").on("click", ".set-ansprechpartner-popup li", function() {

            tdItems.forEach(item => {
                $(item).attr("data-id-ansprechpartner", $(this).data("id"));
            })

            RemoveSelectedStatus();
            $(this).closest(".set-ansprechpartner-popup").remove();
        });

        $("#SavePlan").on("click", function() {

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
                    azubis: azubis
                },
                success: function(response) {
                    // nothing to do...
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
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
