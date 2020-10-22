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

        /**
         * Erzeugt HTML zum Hinzufügen einer Phase im Standardplan.
         *
         * @param {HTMLElement} container Der Container, in dem die HTML
         *                                eingefügt werden soll.
         */
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

        /**
         * Fügt option-Elemente zu einem select-Element hinzu.
         *
         * @param {HTMLSelectElement} selectItem Das select-Element, zu dem die
         *                                       option-Elemente hinzugefügt
         *                                       werden sollen.
         * @param {array} optionData Die Daten für die einzelenen
         *                           option-Elemente.
         */
        function AddSelectOption(selectItem, optionData) {

            selectItem.empty();

            optionData.forEach(data => {
                selectItem.append(
                    $(`<option>${ data.Bezeichnung }</option>`).val(data.ID)
                );
            });
        }

        /**
         * Holt alle Abteilungen mittels einer AJAX-Anfrage des Typs GET.
         *
         * @return {string} Alle Abteilungen im JSON-Format.
         */
        function GetAbteilungen() {
            return $.get(APIABTEILUNG + "Get");
        }

        /**
         * Holt alle Ausbildungsberufe mittels einer AJAX-Anfrage des Typs GET.
         *
         * @return {string} Alle Ausbildungsberufe im JSON-Format.
         */
        function GetAusbildungsberufe() {
            return $.get(APIAUSBILDUNGSBERUF + "Get");
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
         * Versteckt die Ansichten zu den Standardplänen.
         */
        function HideViews() {
            $("#Standardplaene").stop().hide(TIME);
            $("#AddStandardplanForm").stop().hide(TIME);
            $("#EditStandardplanForm").stop().hide(TIME);
        }

        /**
         * Führt ein Click-Event auf dem Element mit der ID
         * "ShowStandardplaeneButton" aus.
         */
        function ShowStandardPlaene() {
            $("#ShowStandardplaeneButton").click();
        }

        /**
         * Holt alle Standardpläne mittels einer GET-Anfrage und zeigt diese an.
         * Für jeden Standardplan wird ein Button zum Bearbeiten und Löschen des
         * jeweiligen Standardplans erstellt.
         */
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
                standardplaene.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Zeigt das Formular zum Hinzufügen eines Standardplans an. Da im
         * Formular die Ausbildungsberufe und Abteilungen benötigt werden,
         * werden AJAX-Anfragen des Typs GET gestellt, um diese zu holen.
         */
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
                    form.stop().show(TIME);
                    $("#LoadingSpinner").hide();
                });
            })
        });

        /**
         * Die aktuelle Phase des jeweiligen Standardplans wird gelöscht.
         */
        $("#AddStandardplanForm").on("click", "input.delete-phase", function() {
            $(this).closest(".phase").remove();
        });

        /**
         * Eine Phase wird zum aktuellen Standardplan hinzugefügt.
         */
        $("#AddStandardplanForm").on("click", "input.add-phase", function() {
            AddPhaseHtml($("#AddStandardplanForm .plan-phasen").eq(0));
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Hinzufügen eines Standardplans. Bei erfolgreicher Speicherung des
         * Standardplans wird das Formular versteckt und die Ansicht aller
         * Standardpläne wird eingeblendet. Bei einem Fehler wird eine
         * Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
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

        /**
         * Fügt die Daten des zu bearbeitenden Standardplans in die Felder des
         * Formulars zum Bearbeiten einer Standardplans ein und blendet dieses
         * ein.
         */
        $("#Standardplaene").on("click", ".edit-item-child", function() {

            $("#LoadingSpinner").show();

            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);
            var form = $("#EditStandardplanForm");
            form.find(`input[name="${ ID_AUSBILDUNGSBERUF }"`).val(id_ausbildungsberuf);

            $.get(APISTANDARDPLAN + "GetTemplate", { id_ausbildungsberuf: id_ausbildungsberuf}, function(data) {
                form.find(".plan").empty().append(data);

                HideViews();
                form.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Die aktuelle Phase des jeweiligen Standardplans wird gelöscht.
         */
        $("#EditStandardplanForm").on("click", "input.delete-phase", function() {
            $(this).closest(".phase").remove();
        });

        /**
         * Eine Phase wird zum aktuellen Standardplan hinzugefügt.
         */
        $("#EditStandardplanForm").on("click", "input.add-phase", function() {
            AddPhaseHtml($("#EditStandardplanForm .plan-phasen").eq(0));
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Bearbeiten eines Standardplans. Bei erfolgreicher Aktualisierung
         * des Standardplans wird das Formular versteckt und die Ansicht aller
         * Standardpläne wird eingeblendet. Bei einem Fehler wird eine
         * Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
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

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
         * Standardplans. Nach erfolgreichem Löschen des Standardplans wird die
         * Ansicht aller Standardpläne aktualisiert. Wenn Fehler beim Löschen
         * des Standardplans auftreten, wird eine Fehlernachricht angezeigt.
         */
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
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Löschen des Standardplans auf.");
                }
            })
        });
    })
});
