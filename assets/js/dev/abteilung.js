jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Abteilungsproperties
        const ID = "id";
        const BEZEICHNUNG = "bezeichnung";
        const MAXAZUBIS = "maxazubis";
        const FARBE = "farbe";

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
         * Versteckt die Ansichten zu den Abteilungen.
         */
        function HideViews() {
            $("#Abteilungen").hide(TIME);
            $("#AddAbteilungForm").hide(TIME);
            $("#EditAbteilungForm").hide(TIME);
        }

        /**
         * Aktualisiert die Legende im Footer mittels einer GET-Anfrage.
         */
        function RefreshFooter() {
            $.get(API + "Refresh/Footer", function(data) {
                $("#Footer").html(data);
            });
        }

        /**
         * Führt ein Click-Event auf dem Element mit der ID
         * "ShowAbteilungenButton" aus.
         */
        function ShowAbteilungen() {
            $("#ShowAbteilungenButton").click();
        }

        /**
         * Holt alle Abteilungen mittels einer GET-Anfrage und zeigt diese an.
         * Für jede Abteilung wird ein Button zum Bearbeiten und Löschen der
         * jeweiligen Abteilung erstellt.
         */
        $("#ShowAbteilungenButton").on("click", function() {

            $("#LoadingSpinner").show();
            $.get(APIABTEILUNG + "Get", function(data) {
                data = JSON.parse(data);

                var abteilungen = $("#Abteilungen");
                abteilungen.empty();

                data.forEach(abteilung => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(abteilung.Bezeichnung);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child secondary-button")
                        .data(ID, abteilung.ID)
                        .data(BEZEICHNUNG, abteilung.Bezeichnung)
                        .data(MAXAZUBIS, abteilung.MaxAzubis)
                        .data(FARBE, abteilung.Farbe)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child secondary-button")
                        .data(ID, abteilung.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    abteilungen.append(item);
                });

                HideViews();
                abteilungen.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Zeigt das Formular zum Hinzufügen einer Abteilung an.
         */
        $("#ShowAddAbteilungForm").on("click", function() {
            HideViews();
            $("#AddAbteilungForm").show(TIME);
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Hinzufügen einer Abteilung. Bei erfolgreicher Speicherung der
         * Abteilung wird das Formular versteckt und die Ansicht aller
         * Abteilungen wird eingeblendet. Bei einem Fehler wird eine
         * Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#AddAbteilungForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var bezeichnungInput = form.find(`input[name="${ BEZEICHNUNG }"]`).eq(0);
            var maxAzubisInput = form.find(`input[name="${ MAXAZUBIS }"]`).eq(0);
            var farbeInput = form.find(`input[name="${ FARBE }"]`).eq(0);

            $.ajax({
                type: "POST",
                url: APIABTEILUNG + "Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    bezeichnung: bezeichnungInput.val(),
                    maxAzubis: maxAzubisInput.val(),
                    farbe: farbeInput.val()
                },
                success: function() {
                    bezeichnungInput.val("");
                    maxAzubisInput.val("");
                    farbeInput.val("#ffffff");

                    RefreshFooter();
                    ShowAbteilungen();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen der Abteilung auf.");
                }
            })
        });

        /**
         * Fügt die Daten der zu bearbeitenden Abteilung in die Felder des
         * Formulars zum Bearbeiten einer Abteilung ein und blendet dieses ein.
         */
        $("#Abteilungen").on("click", ".edit-item-child", function() {

            var id = $(this).data(ID);
            var bezeichnung = $(this).data(BEZEICHNUNG);
            var maxAzubis = $(this).data(MAXAZUBIS);
            var farbe = $(this).data(FARBE);

            var form = $("#EditAbteilungForm");
            form.find(`input[name="${ ID }"]`).val(id);
            form.find(`input[name="${ BEZEICHNUNG }"]`).val(bezeichnung);
            form.find(`input[name="${ MAXAZUBIS }"]`).val(maxAzubis);
            form.find(`input[name="${ FARBE }"]`).val(farbe);

            HideViews();
            form.show(TIME);
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Bearbeiten einer Abteilung. Bei erfolgreicher Aktualisierung der
         * Abteilung wird das Formular versteckt und die Ansicht aller
         * Abteilungen wird eingeblendet. Bei einem Fehler wird eine
         * Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#EditAbteilungForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var idInput = form.find(`input[name="${ ID }"]`);
            var bezeichnungInput = form.find(`input[name="${ BEZEICHNUNG }"]`);
            var maxAzubisInput = form.find(`input[name="${ MAXAZUBIS }"]`);
            var farbeInput = form.find(`input[name="${ FARBE }"]`);

            $.ajax({
                type: "POST",
                url: APIABTEILUNG + "Edit",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: idInput.val(),
                    bezeichnung: bezeichnungInput.val(),
                    maxAzubis: maxAzubisInput.val(),
                    farbe: farbeInput.val()
                },
                success: function() {
                    HideViews();

                    idInput.val("");
                    bezeichnungInput.val("");
                    maxAzubisInput.val("");
                    farbeInput.val("#ffffff");

                    RefreshFooter();
                    ShowAbteilungen();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Aktualisieren der Abteilung auf.");
                }
            })
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen der jeweiligen
         * Abteilung. Nach erfolgreichem Löschen der Abteilung wird die
         * Ansicht aller Abteilungen und die Legende aktualisiert. Wenn Fehler
         * beim Löschen der Abteilung auftreten, wird eine Fehlernachricht
         * angezeigt.
         */
        $("#Abteilungen").on("click", ".delete-item-child", function() {

            var abteilung = $(this).closest(".item-child");

            if (confirm("Soll die Abteilung " + abteilung.find("div").first().text() + " wirklich gelöscht werden?")) {

                $("#LoadingSpinner").show();
                var id = $(this).data(ID);

                $.ajax({
                    type: "POST",
                    url: APIABTEILUNG + "Delete",
                    data: {
                        csrfToken: $("#CsrfToken").val(),
                        id: id
                    },
                    success: function() {
                        HideViews();
                        RefreshFooter();
                        abteilung.remove();
                        $("#LoadingSpinner").hide();
                    },
                    error: function() {
                        HandleError("Es traten Fehler beim Löschen der Abteilung auf.");
                    }
                });
            }
        });
    })
});
