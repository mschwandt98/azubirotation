jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Ansprechpartnerproperties
        const ID = "id";
        const NAME = "name";
        const EMAIL = "email";
        const ID_ABTEILUNG = "id_abteilung";

        /**
         * Holt alle Abteilungen mittels einer AJAX-Anfrage des Typs GET.
         *
         * @return {string} Alle Abteilungen im JSON-Format.
         */
        function GetAbteilungen() {
            return $.get(APIABTEILUNG + "Get");
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
         * Versteckt die Ansichten zu den Ansprechpartnern.
         */
        function HideViews() {
            $("#Ansprechpartner").stop().hide(TIME);
            $("#AddAnsprechpartnerForm").stop().hide(TIME);
            $("#EditAnsprechpartner").stop().hide(TIME);
        }

        /**
         * Führt ein Click-Event auf dem Element mit der ID
         * "ShowAnsprechpartnerButton" aus.
         */
        function ShowAnsprechpartner() {
            $("#ShowAnsprechpartnerButton").click();
        }

        /**
         * Holt alle Ansprechpartner mittels einer GET-Anfrage und zeigt diese
         * an. Für jeden Ansprechpartner wird ein Button zum Bearbeiten und
         * Löschen des jeweiligen Ansprechpartners erstellt.
         */
        $("#ShowAnsprechpartnerButton").on("click", function() {

            $("#LoadingSpinner").show();
            $.get(APIANSPRECHPARTNER + "Get", function(data) {
                data = JSON.parse(data);

                var ansprechpartnerDiv = $("#Ansprechpartner");
                ansprechpartnerDiv.empty();

                data.forEach(ansprechpartner => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(ansprechpartner.Name);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child secondary-button")
                        .data(ID, ansprechpartner.ID)
                        .data(NAME, ansprechpartner.Name)
                        .data(EMAIL, ansprechpartner.Email)
                        .data(ID_ABTEILUNG, ansprechpartner.ID_Abteilung)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child secondary-button")
                        .data(ID, ansprechpartner.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);

                    item.append(outputDiv);
                    item.append(buttonContainer);
                    ansprechpartnerDiv.append(item);
                });

                HideViews();
                ansprechpartnerDiv.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Zeigt das Formular zum Hinzufügen eines Ansprechpartners an. Da für
         * dieses Formular alle Abteilungen benötigt werden, wird eine
         * AJAX-Anfrage des Typs GET gestellt.
         */
        $("#ShowAddAnsprechpartnerForm").on("click", function() {

            $("#LoadingSpinner").show();

            var form = $("#AddAnsprechpartnerForm");
            var abteilungSelect = form.find(`select[name="${ ID_ABTEILUNG }"]`);

            GetAbteilungen().then(abteilungen => {
                abteilungen = JSON.parse(abteilungen);

                abteilungen.forEach(abteilung => {

                    var item = $(`<option>${ abteilung.Bezeichnung }</option>`)
                        .val(abteilung.ID);

                    abteilungSelect.append(item);
                });

                HideViews();
                form.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Hinzufügen eines Ansprechpartners. Bei erfolgreicher Speicherung
         * der Ansprechpartners wird das Formular versteckt und die Ansicht
         * aller Ansprechpartner wird eingeblendet. Bei einem Fehler wird eine
         * Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#AddAnsprechpartnerForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var nameInput = form.find(`input[name="${ NAME }"]`).eq(0);
            var emailInput = form.find(`input[name="${ EMAIL }"]`).eq(0);
            var abteilungSelect = form.find(`select[name="${ ID_ABTEILUNG }"]`).eq(0);

            $.ajax({
                type: "POST",
                url: APIANSPRECHPARTNER + "Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    name: nameInput.val(),
                    email: emailInput.val(),
                    id_abteilung: abteilungSelect.val()
                },
                success: function() {
                    nameInput.val("");
                    emailInput.val("");
                    abteilungSelect.find("option").remove();

                    HideViews();
                    ShowAnsprechpartner();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen des Ansprechpartners auf.");
                }
            })
        });

        /**
         * Fügt die Daten des zu bearbeitenden Ansprechpartners in die Felder
         * des Formulars zum Bearbeiten eines Ansprechpartners ein und blendet
         * dieses ein.
         */
        $("#Ansprechpartner").on("click", ".edit-item-child", function() {

            $("#LoadingSpinner").show();

            var id = $(this).data(ID);
            var name = $(this).data(NAME);
            var email = $(this).data(EMAIL);
            var id_abteilung = $(this).data(ID_ABTEILUNG);

            GetAbteilungen().then(abteilungen => {
                abteilungen = JSON.parse(abteilungen);

                var form = $("#EditAnsprechpartner");
                var abteilungSelect = form.find(`select[name="${ ID_ABTEILUNG }"]`);

                abteilungen.forEach(abteilung => {
                    var item = $(`<option>${ abteilung.Bezeichnung }</option>`)
                        .val(abteilung.ID);

                    abteilungSelect.append(item);
                });

                form.find(`input[name="${ ID }"]`).val(id);
                form.find(`input[name="${ NAME }"]`).val(name);
                form.find(`input[name="${ EMAIL }"]`).val(email);
                abteilungSelect.val(id_abteilung);

                HideViews();
                form.stop().show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Bearbeiten eines Ansprechpartners. Bei erfolgreicher
         * Aktualisierung des Ansprechpartners wird das Formular versteckt un
         * die Ansicht aller Ansprechpartner wird eingeblendet. Bei einem Fehler
         * wird eine Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#EditAnsprechpartner").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var idInput = form.find(`input[name="${ ID }"]`);
            var nameInput = form.find(`input[name="${ NAME }"]`);
            var emailInput = form.find(`input[name="${ EMAIL }"]`);
            var abteilungSelect = form.find(`select[name="${ ID_ABTEILUNG }"]`);

            $.ajax({
                type: "POST",
                url: APIANSPRECHPARTNER + "Edit",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: idInput.val(),
                    name: nameInput.val(),
                    email: emailInput.val(),
                    id_abteilung: abteilungSelect.val()
                },
                success: function() {
                    idInput.val("");
                    nameInput.val("");
                    emailInput.val("");
                    abteilungSelect.find("option").remove();

                    HideViews();
                    $("#ShowAnsprechpartnerButton").click();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Aktualisieren des Ansprechpartners auf.");
                }
            });
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
         * Ansprechpartners. Nach erfolgreichem Löschen des Ansprechpartners
         * wird die Ansicht aller Ansprechpartner aktualisiert. Wenn Fehler
         * beim Löschen des Ansprechpartners auftreten, wird eine Fehlernachricht
         * angezeigt.
         */
        $("#Ansprechpartner").on("click", ".delete-item-child", function() {

            var ansprechpartner = $(this).closest(".item-child");

            if (confirm("Soll der Ansprechpartner " + ansprechpartner.find("div").first().text() + " wirklich gelöscht werden?")) {

                $("#LoadingSpinner").show();

                var id = $(this).data(ID);

                $.ajax({
                    type: "POST",
                    url: APIANSPRECHPARTNER + "Delete",
                    data: {
                        csrfToken: $("#CsrfToken").val(),
                        id: id
                    },
                    success: function() {
                        ansprechpartner.remove();
                        $("#LoadingSpinner").hide();
                    },
                    error: function() {
                        HandleError("Es traten Fehler beim Löschen des Ansprechpartners auf.");
                    }
                });
            }
        });
    })
});
