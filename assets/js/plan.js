jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";

        var Abteilungen;
        var Ansprechpartner;

        $.get(API + "Abteilung/Get", function(data) {
            Abteilungen = JSON.parse(data);
        });

        $.get(API + "Ansprechpartner/Get", function(data) {
            Ansprechpartner = JSON.parse(data);
        });

        $(document).on("click", function(e){

            if(!$(e.target).closest("#Plan .plan-phase").length){
                $("#Plan .set-abteilung-popup").remove();
                $("#Plan .set-ansprechpartner-popup").remove();
            }
        });

        $("#Plan").on("click", ".plan-phase", function(e) {

            if ($(e.target).is("li")) {
                return;
            }

            $("#Plan .set-abteilung-popup").remove();
            $("#Plan .set-ansprechpartner-popup").remove();

            var el = $(this);
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

        $("#Plan").on("click", ".set-abteilung-popup li", function() {

            var el = $(this);
            var container = el.closest(".plan-phase");

            container.attr("data-id-abteilung", el.data("id"))
                .attr(
                    "style",
                    "background-color: " + GetAbteilungsFarbe(el.data("id")) + "; border-color: " + GetAbteilungsFarbe(el.data("id"))
                );

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
            container.append(popupAnsprechpartner);

            $(this).closest(".set-abteilung-popup").remove();
        });

        $("#Plan").on("click", ".set-ansprechpartner-popup li", function() {

            var el = $(this);
            el.closest(".plan-phase").attr("data-id-ansprechpartner", el.data("id"));
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
    });
});
