jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Standardplanproperties
        const ID_AUSBILDUNGSBERUF = "id_ausbildungsberuf";
        const ID_ABTEILUNG = "id_abteilung";
        const WOCHEN = "wochen";
        const PRAEFERIEREN = "praeferieren";
        const OPTIONAL = "optional";

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

            var praeferieren = $("<div></div>").append(
                $("<label></label>")
                    .append($("<span></span>").text("Präferieren: "))
                    .append($(`<input type="checkbox" name="${ PRAEFERIEREN }" />`))
            );

            var optional = $("<div></div>").append(
                $("<label></label>")
                    .append($("<span></span>").text("Optional: "))
                    .append($(`<input type="checkbox" name="${ OPTIONAL }" />`))
            );

            var deletePhaseButton = $('<input type="button" />')
                .addClass("delete-phase")
                .val("Phase löschen");

            container.append(
                item.append(abteilungLabel)
                    .append(wochenDiv)
                    .append(praeferieren)
                    .append(optional)
                    .append(deletePhaseButton)
            );
        }

        function GetAbteilungen() {
            return $.get(APIABTEILUNG + "Get");
        }

        function GetAusbildungsberufe() {
            return $.get(APIAUSBILDUNGSBERUF + "Get");
        }

        function HandleError(errorMessage = "Es trat ein unbekannter Fehler auf.") {

            $("#LoadingSpinner").hide();
            var emb = $("#ErrorMessageBox");
            emb.find(".message").text(errorMessage);
            emb.show();
            setTimeout(() => { emb.fadeOut().text(); }, 10000);
        }

        function HideViews() {
            $("#Standardplaene").hide();
            $("#AddStandardplanForm").hide();
            $("#EditStandardplanForm").hide();
        }

        function ShowStandardPlaene() {
            $("#ShowStandardplaeneButton").click();
        }

        $("#ShowStandardplaeneButton").on("click", function() {

            $("#LoadingSpinner").show();

            GetAbteilungen().then(abteilungen => {
                Abteilungen = JSON.parse(abteilungen);
            });

            $.get(APISTANDARDPLAN + "Get", function(data) {
                data = JSON.parse(data);

                var standardplaene = $("#Standardplaene");
                standardplaene.empty();

                $.each(data, function(index, standardplan) {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(standardplan.Ausbildungsberuf);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child secondary-button")
                        .data(ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child secondary-button")
                        .data(ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    standardplaene.append(item);
                });

                HideViews();
                standardplaene.show();
                $("#LoadingSpinner").hide();
            });
        });

        function AddSelectOption(selectItem, optionData) {

            selectItem.empty();

            optionData.forEach(data => {
                selectItem.append(
                    $(`<option>${ data.Bezeichnung }</option>`).val(data.ID)
                );
            });
        }

        $("#ShowAddStandardplanForm").on("click", function() {

            $("#LoadingSpinner").show();
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
                    $("#LoadingSpinner").hide();
                });
            })
        });

        $("#AddStandardplanForm").on("click", "input.delete-phase", function() {
            $(this).closest(".phase").remove();
        });

        $("#AddStandardplanForm").on("click", "input.add-phase", function() {
            AddPhaseHtml($("#AddStandardplanForm .plan-phasen").eq(0));
        });

        $("#AddStandardplanForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var ausbildungsberufeSelect = form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"`);
            var phaseDivs = form.find(".phase");
            var phasen = [];

            phaseDivs.each(index => {

                let phase = phaseDivs.eq(index);
                let abteilungenSelect = phase.find(`select[name="${ ID_ABTEILUNG }"]`);
                let wochenInput = phase.find(`input[name="${ WOCHEN }"]`);
                let praeferierenCheckbox = phase.find(`input[name="${ PRAEFERIEREN }"]`);
                let optionalCheckbox = phase.find(`input[name="${ OPTIONAL }"]`);

                phasen.push({
                    id_abteilung: abteilungenSelect.val(),
                    wochen: wochenInput.val(),
                    praeferieren: praeferierenCheckbox.val(),
                    optional: optionalCheckbox.val()
                });
            });

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id_ausbildungsberuf: ausbildungsberufeSelect.val(),
                    phasen: phasen
                },
                success: function() {

                    ausbildungsberufeSelect.find("option").remove();
                    phaseDivs.not(":first").remove();

                    phaseDivs.eq(0).find(`select[name="${ ID_ABTEILUNG }"]`).find("option").remove();
                    phaseDivs.eq(0).find(`input[name="${ WOCHEN }"]`).val("");
                    phaseDivs.eq(0).find(`input[name="${ PRAEFERIEREN }"]`).val("");
                    phaseDivs.eq(0).find(`input[name="${ OPTIONAL }"]`).val("");

                    ShowStandardPlaene();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen des Standardplans auf.");
                }
            })
        });

        $("#Standardplaene").on("click", ".edit-item-child", function() {

            $("#LoadingSpinner").show();

            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);
            var form = $("#EditStandardplanForm");
            form.find(`input[name="${ ID_AUSBILDUNGSBERUF }"`).val(id_ausbildungsberuf);

            $.get(APISTANDARDPLAN + "GetTemplate", { id_ausbildungsberuf: id_ausbildungsberuf}, function(data) {
                form.find(".plan").empty().append(data);

                HideViews();
                form.show();
                $("#LoadingSpinner").hide();
            });
        });

        $("#EditStandardplanForm").on("click", "input.delete-phase", function() {
            $(this).closest(".phase").remove();
        });

        $("#EditStandardplanForm").on("click", "input.add-phase", function() {
            AddPhaseHtml($("#EditStandardplanForm .plan-phasen").eq(0));
        });

        $("#EditStandardplanForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var idAusbildungsberufInput = form.find(`input[name="${ ID_AUSBILDUNGSBERUF }"`);
            var phaseDivs = form.find(".phase");
            var phasen = [];

            phaseDivs.each(index => {

                let phase = phaseDivs.eq(index);
                let abteilungenSelect = phase.find(`select[name="${ ID_ABTEILUNG }"]`);
                let wochenInput = phase.find(`input[name="${ WOCHEN }"]`);
                let praeferierenCheckbox = phase.find(`input[name="${ PRAEFERIEREN }"]`).eq(0);
                let optionalCheckbox = phase.find(`input[name="${ OPTIONAL }"]`).eq(0);

                phasen.push({
                    id_abteilung: abteilungenSelect.val(),
                    wochen: wochenInput.val(),
                    praeferieren: praeferierenCheckbox.prop("checked"),
                    optional: optionalCheckbox.prop("checked")
                });
            });

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Edit",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id_ausbildungsberuf: idAusbildungsberufInput.val(),
                    phasen: phasen
                },
                success: function() {

                    phaseDivs.not(":first").remove();
                    phaseDivs.eq(0).find(`select[name="${ ID_ABTEILUNG }"]`).find("option").remove();
                    phaseDivs.eq(0).find(`input[name="${ PRAEFERIEREN }"]`).prop("checked", false);
                    phaseDivs.eq(0).find(`input[name="${ OPTIONAL }"]`).prop("checked", false);

                    HideViews();
                    ShowStandardPlaene();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Aktualisieren des Standardplans auf.");
                }
            })
        });

        $("#Standardplaene").on("click", ".delete-item-child", function() {

            $("#LoadingSpinner").show();

            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);
            var standardplan = $(this).closest(".item-child");

            $.ajax({
                type: "POST",
                url: APISTANDARDPLAN + "Delete",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id_ausbildungsberuf: id_ausbildungsberuf
                },
                success: function() {
                    standardplan.remove();
                    $("#LoadingSpinner").show();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Löschen des Standardplans auf.");
                }
            })
        });
    })
});
