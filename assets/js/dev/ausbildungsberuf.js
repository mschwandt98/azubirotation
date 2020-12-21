jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Ausbildungsberufsproperties
        const ID = "id";
        const BEZEICHNUNG = "bezeichnung";

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
         * Versteckt die Ansichten zu den Ausbildungsberufen.
         */
        function HideViews() {
            $("#Ausbildungsberufe").stop().hide(TIME);
            $("#AddAusbildungsberufForm").stop().hide(TIME);
            $("#EditAusbildungsberufForm").stop().hide(TIME);
        }

        /**
         * Führt das Click-Event des Buttons zum Anzeigen der Ausbildungsberufe
         * aus.
         */
        function ShowAusbildungsberufe() {
            $(".data-item.ausbildungsberufe-item .show-data").click();
        }

        /**
         * Holt alle Ausbildungsberufe mittels einer GET-Anfrage und zeigt diese
         * an. Für jeden Ausbildungsberuf wird ein Button zum Bearbeiten und
         * Löschen des jeweiligen Ausbildungsberufes erstellt.
         */
        $(".data-item.ausbildungsberufe-item").on("click", ".show-data", function() {

            $("#LoadingSpinner").show();
            $.get(APIAUSBILDUNGSBERUF + "Get", function(data) {
                data = JSON.parse(data);

                var ausbildungsberufe = $("#Ausbildungsberufe");
                ausbildungsberufe.empty();

                data.forEach(ausbildungsberuf => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(ausbildungsberuf.Bezeichnung);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child secondary-button")
                        .data(ID, ausbildungsberuf.ID)
                        .data(BEZEICHNUNG, ausbildungsberuf.Bezeichnung)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child secondary-button")
                        .data(ID, ausbildungsberuf.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    ausbildungsberufe.append(item);
                });

                HideViews();
                ausbildungsberufe.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Zeigt das Formular zum Hinzufügen eines Ausbildungsberufes an.
         */
        $(".data-item.ausbildungsberufe-item").on("click", ".add-data", function() {
            HideViews();
            $("#AddAusbildungsberufForm").stop().show(TIME);
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Hinzufügen eines Ausbildungsberufes. Bei erfolgreicher
         * Speicherung des Ausbildungsberufes wird das Formular versteckt und
         * die Ansicht aller Ausbildungsberufe wird eingeblendet. Bei einem
         * Fehler wird eine Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#AddAusbildungsberufForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var bezeichnungInput = form.find(`input[name="${ BEZEICHNUNG }"]`).eq(0);

            $.ajax({
                type: "POST",
                url: APIAUSBILDUNGSBERUF + "Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    bezeichnung: bezeichnungInput.val()
                },
                success: function() {
                    bezeichnungInput.val("");

                    HideViews();
                    ShowAusbildungsberufe();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen des Ausbildungsberufes auf.");
                }
            })
        });

        /**
         * Fügt die Daten des zu bearbeitenden Ausbildungsberufes in die Felder
         * des Formulars zum Bearbeiten eines Ausbildungsberuf ein und blendet
         * dieses ein.
         */
        $("#Ausbildungsberufe").on("click", ".edit-item-child", function() {

            var id = $(this).data(ID);
            var bezeichnung = $(this).data(BEZEICHNUNG);

            var form = $("#EditAusbildungsberufForm");
            form.find(`input[name="${ ID }"]`).val(id);
            form.find(`input[name="${ BEZEICHNUNG }"]`).val(bezeichnung);

            HideViews();
            form.stop().show(TIME);
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Bearbeiten eines Ausbildungsberufes. Bei erfolgreicher
         * Aktualisierung des Ausbildungsberufes wird das Formular versteckt und
         * die Ansicht aller Ausbildungsberufe wird eingeblendet. Bei einem
         * Fehler wird eine Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#EditAusbildungsberufForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var idInput = form.find(`input[name="${ ID }"]`);
            var bezeichnungInput = form.find(`input[name="${ BEZEICHNUNG }"]`);

            $.ajax({
                type: "POST",
                url: APIAUSBILDUNGSBERUF + "Edit",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: idInput.val(),
                    bezeichnung: bezeichnungInput.val()
                },
                success: function() {
                    idInput.val("");
                    bezeichnungInput.val("");

                    HideViews();
                    ShowAusbildungsberufe();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Aktualisieren des Ausbildungsberufes auf.");
                }
            })
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
         * Ausbildungsberufes. Nach erfolgreichem Löschen des Ausbildungsberufes
         * wird die Ansicht aller Ausbildungsberufe aktualisiert. Wenn Fehler
         * beim Löschen des Ausbildungsberufes auftreten, wird eine
         * Fehlernachricht angezeigt.
         */
        $("#Ausbildungsberufe").on("click", ".delete-item-child", function() {

            var ausbildungsberuf = $(this).closest(".item-child");

            if (confirm("Soll der Ausbildungsberuf " + ausbildungsberuf.find("div").first().text() + " wirklich gelöscht werden?")) {

                $("#LoadingSpinner").show();

                var id = $(this).data(ID);

                $.ajax({
                    type: "POST",
                    url: APIAUSBILDUNGSBERUF + "Delete",
                    data: {
                        csrfToken: $("#CsrfToken").val(),
                        id: id
                    },
                    success: function() {
                        ausbildungsberuf.remove();
                        $("#LoadingSpinner").hide();
                    },
                    error: function() {
                        HandleError("Es traten Fehler beim Löschen des Ausbildungsberufes auf.");
                    }
                });
            }
        });
    })
});
