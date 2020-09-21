jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";
        const APISTANDARDPLAN = API + "Standardplan/";

        // Bezeichnungen für Standardplanproperties
        const ID_AUSBILDUNGSBERUF = "id_ausbildungsberuf";
        const ID_ABTEILUNG = "id_abteilung";
        const WOCHEN = "wochen";

        // TODO: besseren Weg finden
        var Abteilungen;

        function AddPhaseHtml(container) {
            var item = $('<div></div>').addClass("phase");
            var abteilungenSelect = $("<select></select>").attr("name", ID_ABTEILUNG);

            AddSelectOption(abteilungenSelect, Abteilungen);

            var abteilungLabel = $("<label></label>")
                .append($("<div></div>").text("Abteilung auswählen"))
                .append(abteilungenSelect);

            var wochenDiv = $("<div></div>")
                .append($("<span></span>").text("Wochen: "))
                .append($(`<input type="number" name="${ WOCHEN }" />`));

            var deletePhaseButton = $('<input type="button" />')
                .addClass("delete-phase")
                .val("Phase löschen");

            container.append(
                item.append(abteilungLabel).append(wochenDiv).append(deletePhaseButton)
            );
        }

        function GetAbteilungen() {
            return $.get(API + "Abteilung/Get");
        }

        function GetAusbildungsberufe() {
            return $.get(API + "Ausbildungsberuf/Get");
        }

        function HideViews() {
            $("#Standardplaene").hide();
            $("#AddStandardplanForm").hide();
            $("#EditStandardplanForm").hide();
        }

        function ShowStandardPlaene() {
            $("#ShowStandardplaeneButton").click();
        }

        $("#ShowStandardplaeneButton").on("click", function(e) {

            GetAbteilungen().then(abteilungen => {
                Abteilungen = JSON.parse(abteilungen);
            });

            $.get(APISTANDARDPLAN + "Get", function(data) {
                data = JSON.parse(data);

                var standardplaene = $("#Standardplaene");
                standardplaene.empty();

                $.each(data, function(index, standardplan) {
                    var item = $("<div></div>").addClass("standardplan-item");

                    var outputDiv = $("<div></div>").text(standardplan.Ausbildungsberuf);

                    var editButton = $('<input type="button" />')
                        .addClass("edit-standardplan")
                        .data(ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-standardplan")
                        .data(ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                        .val("Löschen");

                    item.append(outputDiv);
                    item.append(editButton);
                    item.append(deleteButton);
                    standardplaene.append(item);
                });

                HideViews();
                standardplaene.show();
            });
        });

        function AddSelectOption(selectItem, optionData) {
            optionData.forEach(data => {
                selectItem.append(
                    $(`<option>${ data.Bezeichnung }</option>`).val(data.ID)
                );
            });
        }

        $("#ShowAddStandardplanForm").on("click", function(e) {

            var form = $("#AddStandardplanForm");

            GetAusbildungsberufe().then(ausbildungsberufe => {

                GetAbteilungen().then(abteilungen => {

                    abteilungen = JSON.parse(abteilungen);
                    Abteilungen = abteilungen;

                    AddSelectOption(
                        form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"`),
                        JSON.parse(ausbildungsberufe)
                    );

                    AddSelectOption(
                        form.find(`select[name="${ ID_ABTEILUNG }"`),
                        abteilungen
                    );

                    HideViews();
                    form.show();
                });
            })
        });

        $("#AddStandardplanForm").on("click", "input.delete-phase", function(e) {
            $(this).closest(".phase").remove();
        });

        $("#AddStandardplanForm").on("click", "input.add-phase", function(e) {
            AddPhaseHtml($("#AddStandardplanForm .plan-phasen").eq(0));
        });

        $("#AddStandardplanForm").on("submit", function(e) {

            e.preventDefault();
            var form = $(this);
            var ausbildungsberufeSelect = form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"`);
            var phaseDivs = form.find(".phase");
            var phasen = [];

            phaseDivs.each(index => {

                let phase = phaseDivs.eq(index);
                let abteilungenSelect = phase.find(`select[name="${ ID_ABTEILUNG }"]`);
                let wochenInput = phase.find(`input[name="${ WOCHEN }"]`);

                phasen.push({
                    id_abteilung: abteilungenSelect.val(),
                    wochen: wochenInput.val()
                });
            });

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Add",
                data: {
                    id_ausbildungsberuf: ausbildungsberufeSelect.val(),
                    phasen: phasen
                },
                success: function(response) {

                    ausbildungsberufeSelect.find("option").remove();
                    phaseDivs.not(":first").remove();

                    phaseDivs.eq(0).find('input[type="number"]').val("");
                    phaseDivs.eq(0).find(`select[name="${ ID_ABTEILUNG }"]`).find("option").remove();

                    ShowStandardPlaene();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Standardplaene").on("click", ".edit-standardplan", function(e) {

            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);

            var form = $("#EditStandardplanForm");
            form.find(`input[name="${ ID_AUSBILDUNGSBERUF }"`).val(id_ausbildungsberuf);

            $.get(APISTANDARDPLAN + "GetTemplate", { id_ausbildungsberuf: id_ausbildungsberuf}, function(data) {
                form.find(".plan").empty().append(data);

                HideViews();
                form.show();
            });
        });

        $("#EditStandardplanForm").on("click", "input.delete-phase", function(e) {
            $(this).closest(".phase").remove();
        });

        $("#EditStandardplanForm").on("click", "input.add-phase", function(e) {
            AddPhaseHtml($("#EditStandardplanForm .plan-phasen").eq(0));
        });

        $("#EditStandardplanForm").on("submit", function(e) {

            e.preventDefault();
            var form = $(this);
            var idAusbildungsberufInput = form.find(`input[name="${ ID_AUSBILDUNGSBERUF }"`);
            var phaseDivs = form.find(".phase");
            var phasen = [];

            phaseDivs.each(index => {

                let phase = phaseDivs.eq(index);
                let abteilungenSelect = phase.find(`select[name="${ ID_ABTEILUNG }"]`);
                let wochenInput = phase.find(`input[name="${ WOCHEN }"]`);

                phasen.push({
                    id_abteilung: abteilungenSelect.val(),
                    wochen: wochenInput.val()
                });
            });

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Edit",
                data: {
                    id_ausbildungsberuf: idAusbildungsberufInput.val(),
                    phasen: phasen
                },
                success: function(response) {

                    phaseDivs.not(":first").remove();
                    phaseDivs.eq(0).find(`input[name="${ WOCHEN }"]`).val("");
                    phaseDivs.eq(0).find(`select[name="${ ID_ABTEILUNG }"]`).find("option").remove();

                    HideViews();
                    ShowStandardPlaene();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Standardplaene").on("click", ".delete-standardplan", function(e) {

            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);
            var standardplan = $(this).closest(".standardplan-item");

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Delete",
                data: { id_ausbildungsberuf: id_ausbildungsberuf },
                success: function(response) {
                    standardplan.remove();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });
    })
});
