jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Azubiproperties
        const ID = "id";
        const VORNAME = "vorname";
        const NACHNAME = "nachname";
        const EMAIL = "email";
        const ID_AUSBILDUNGSBERUF = "id_ausbildungsberuf";
        const AUSBILDUNGSSTART = "ausbildungsstart";
        const AUSBILDUNGSENDE = "ausbildungsende";
        const MUSTERPLANUNG_ERSTELLEN = "musterplanung_erstellen";

        /**
         * Vergleicht, ob der erste Parameter (dateA) größer als der zweite
         * Parameter (dateB) ist.
         *
         * @param {string} dateA - Datum im Format YYYY-mm-dd
         * @param {string} dateB - Datum im Format YYYY-mm-dd
         *
         * @returns {boolean} Status, ob dateA größer als dateB ist.
         */
        function CompareDates(dateA, dateB) {

            var a = new Date(dateA).getTime();
            var b = new Date(dateB).getTime();

            return a > b;
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
         * Versteckt die Ansichten zu den Azubis.
         */
        function HideViews() {
            $("#Azubis").hide(TIME);
            $("#AddAzubiForm").hide(TIME);
            $("#EditAzubiForm").hide(TIME);
        }

        /**
         * Aktualisiert die Planung mittels einer AJAX-Anfrage des Typs GET.
         */
        function RefreshPlan() {
            $.get(API + "Refresh/Plan", function(data) {
                $("#Plan").html(data);
            });
        }

        /**
         * Führt ein Click-Event auf dem Element mit der ID "ShowAzubisButton"
         * aus.
         */
        function ShowAzubis() {
            HideViews();
            $("#ShowAzubisButton").click();
        }

        /**
         * Holt alle Azubis mittels einer GET-Anfrage und zeigt diese an. Für
         * jeden Azubi wird ein Button zum Bearbeiten und Löschen des jeweiligen
         * Azubis erstellt.
         */
        $("#ShowAzubisButton").on("click", function() {

            $("#LoadingSpinner").show();
            $.get(APIAZUBI + "Get", function(data) {
                data = JSON.parse(data);

                var azubis = $("#Azubis");
                azubis.empty();

                data.forEach(auszubildender => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(auszubildender.Nachname + ", " + auszubildender.Vorname);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child secondary-button")
                        .data(ID, auszubildender.ID)
                        .data(VORNAME, auszubildender.Vorname)
                        .data(NACHNAME, auszubildender.Nachname)
                        .data(EMAIL, auszubildender.Email)
                        .data(ID_AUSBILDUNGSBERUF, auszubildender.ID_Ausbildungsberuf)
                        .data(AUSBILDUNGSSTART, auszubildender.Ausbildungsstart)
                        .data(AUSBILDUNGSENDE, auszubildender.Ausbildungsende)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child secondary-button")
                        .data(ID, auszubildender.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    azubis.append(item);
                });

                HideViews();
                azubis.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Zeigt das Formular zum Hinzufügen eines Azubis an. Da im Formular
         * alle Ausbildungsberufe benötigt werden, wird eine AJAX-Anfrage des
         * Typs GET gestellt, um diese zu holen.
         */
        $("#ShowAddAzubiForm").on("click", function() {

            $("#LoadingSpinner").show();
            var ausbildungsberufSelect = $("#AddAzubiForm").find(`select[name="${ ID_AUSBILDUNGSBERUF }"]`);

            GetAusbildungsberufe().then(ausbildungsberufe => {
                ausbildungsberufe = JSON.parse(ausbildungsberufe);

                ausbildungsberufe.forEach(ausbildungsberuf => {

                    var item = $(`<option>${ ausbildungsberuf.Bezeichnung }</option>`)
                        .val(ausbildungsberuf.ID);

                        ausbildungsberufSelect.append(item);
                });

                HideViews();
                $("#AddAzubiForm").show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Wenn in dem Formular zum Hinzufügen eines Azubis der Wert des
         * Input-Felds für den Ausbildungsstart geändert wird, wird dieser Wert
         * beim Input-Feld für das Ausbildungsende als minimales Datum per
         * Attribut min gesetzt.
         *
         * @param {Event} e Das ausgelöste change-Event.
         */
        $("#AddAzubiForm").on("change", `input[name="${ AUSBILDUNGSSTART }"]`, function(e) {

            var value = e.target.value;
            var inputAusbildungsende = $(`#AddAzubiForm input[name="${ AUSBILDUNGSENDE }"]`);
            inputAusbildungsende.attr("min", value);

            if (inputAusbildungsende.val() && CompareDates(value, inputAusbildungsende.val())) {
                inputAusbildungsende.val(value);
            }
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Hinzufügen eines Azubis. Bei erfolgreicher Speicherung des Azubis
         * wird das Formular versteckt und die Ansicht aller Azubis wird
         * eingeblendet. Bei einem Fehler wird eine Fehlernachricht ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#AddAzubiForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var vornameInput = form.find(`input[name="${ VORNAME }"]`).eq(0);
            var nachnameInput = form.find(`input[name="${ NACHNAME }"]`).eq(0);
            var emailInput = form.find(`input[name="${ EMAIL }"]`).eq(0);
            var ausbildungsberufSelect = form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"]`).eq(0);
            var ausbildungsstartInput = form.find(`input[name="${ AUSBILDUNGSSTART }"]`).eq(0);
            var ausbildungsendeInput = form.find(`input[name="${ AUSBILDUNGSENDE }"]`).eq(0);
            var musterplanung_erstellen = form.find(`input[name="${ MUSTERPLANUNG_ERSTELLEN }"]`).eq(0);

            $.ajax({
                type: "POST",
                url: APIAZUBI + "Add",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    vorname: vornameInput.val(),
                    nachname: nachnameInput.val(),
                    email: emailInput.val(),
                    id_ausbildungsberuf: ausbildungsberufSelect.val(),
                    ausbildungsstart: ausbildungsstartInput.val(),
                    ausbildungsende: ausbildungsendeInput.val(),
                    musterplanung_erstellen: musterplanung_erstellen.prop("checked")
                },
                success: function() {
                    vornameInput.val("");
                    nachnameInput.val("");
                    emailInput.val("");
                    ausbildungsberufSelect.empty();
                    musterplanung_erstellen.prop("checked", true)

                    RefreshPlan();
                    HideViews();
                    ShowAzubis();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Anlegen des Auszubildenden auf.");
                }
            })
        });

        /**
         * Fügt die Daten des zu bearbeitenden Azubis in die Felder des
         * Formulars zum Bearbeiten eines Azubis ein und blendet dieses ein.
         */
        $("#Azubis").on("click", ".edit-item-child", function() {

            $("#LoadingSpinner").show();

            var id = $(this).data(ID);
            var vorname = $(this).data(VORNAME);
            var nachname = $(this).data(NACHNAME);
            var email = $(this).data(EMAIL);
            var id_ausbildungsberuf = $(this).data(ID_AUSBILDUNGSBERUF);
            var ausbildungsstart = $(this).data(AUSBILDUNGSSTART);
            var ausbildungsende = $(this).data(AUSBILDUNGSENDE);

            GetAusbildungsberufe().then(ausbildungsberufe => {
                ausbildungsberufe = JSON.parse(ausbildungsberufe);

                var form = $("#EditAzubiForm");
                var ausbildungsberufSelect = form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"]`);
                ausbildungsberufSelect.empty();

                ausbildungsberufe.forEach(ausbildungsberuf => {
                    var item = $(`<option>${ ausbildungsberuf.Bezeichnung }</option>`)
                        .val(ausbildungsberuf.ID);

                    ausbildungsberufSelect.append(item);
                })

                form.find(`input[name="${ ID }"]`).val(id);
                form.find(`input[name="${ VORNAME }"]`).val(vorname);
                form.find(`input[name="${ NACHNAME }"]`).val(nachname);
                form.find(`input[name="${ EMAIL }"]`).val(email);
                ausbildungsberufSelect.val(id_ausbildungsberuf);
                form.find(`input[name="${ AUSBILDUNGSSTART }"]`).val(ausbildungsstart);
                form.find(`input[name="${ AUSBILDUNGSENDE }"]`).val(ausbildungsende);
                form.find(`input[name="${ AUSBILDUNGSENDE }"]`).attr("min", ausbildungsstart);

                HideViews();
                form.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

        /**
         * Wenn in dem Formular zum Bearbeiten eines Azubis der Wert des
         * Input-Felds für den Ausbildungsstart geändert wird, wird dieser Wert
         * beim Input-Feld für das Ausbildungsende als minimales Datum per
         * Attribut min gesetzt.
         *
         * @param {Event} e Das ausgelöste change-Event.
         */
        $("#EditAzubiForm").on("change", `input[name="${ AUSBILDUNGSSTART }"]`, function(e) {

            var value = e.target.value;
            var inputAusbildungsende = $(`#EditAzubiForm input[name="${ AUSBILDUNGSENDE }"]`);
            inputAusbildungsende.attr("min", value);

            if (inputAusbildungsende.val() && CompareDates(value, inputAusbildungsende.val())) {
                inputAusbildungsende.val(value);
            }
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
         * zum Bearbeiten eines Azubis. Bei erfolgreicher Aktualisierung des
         * Azubis wird das Formular versteckt und die Ansicht aller
         * Azubis wird eingeblendet. Bei einem Fehler wird eine Fehlernachricht
         * ausgegeben.
         *
         * @param {Event} e Das ausgelöste Submit-Event.
         */
        $("#EditAzubiForm").on("submit", function(e) {

            e.preventDefault();
            $("#LoadingSpinner").show();

            var form = $(this);
            var idInput = form.find(`input[name="${ ID }"]`);
            var vornameInput = form.find(`input[name="${ VORNAME }"]`);
            var nachnameInput = form.find(`input[name="${ NACHNAME }"]`);
            var emailInput = form.find(`input[name="${ EMAIL }"]`);
            var ausbildungsberufSelect = form.find(`select[name="${ ID_AUSBILDUNGSBERUF }"]`);
            var ausbildungsstartInput = form.find(`input[name="${ AUSBILDUNGSSTART }"]`);
            var ausbildungsendeInput = form.find(`input[name="${ AUSBILDUNGSENDE }"]`);

            $.ajax({
                type: "POST",
                url: APIAZUBI + "Edit",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: idInput.val(),
                    vorname: vornameInput.val(),
                    nachname: nachnameInput.val(),
                    email: emailInput.val(),
                    id_ausbildungsberuf: ausbildungsberufSelect.val(),
                    ausbildungsstart: ausbildungsstartInput.val(),
                    ausbildungsende: ausbildungsendeInput.val()
                },
                success: function() {
                    idInput.val("");
                    vornameInput.val("");
                    nachnameInput.val("");
                    emailInput.val("");
                    ausbildungsberufSelect.empty();
                    ausbildungsstartInput.val("");
                    ausbildungsendeInput.val("");

                    RefreshPlan();
                    HideViews();
                    ShowAzubis();
                    $("#LoadingSpinner").hide();
                },
                error: function() {
                    HandleError("Es traten Fehler beim Aktualisieren des Auszubildenden auf.");
                }
            })
        });

        /**
         * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
         * Azubis. Nach erfolgreichem Löschen des Azubis wird die Ansicht aller
         * Azubis aktualisiert. Wenn Fehler beim Löschen des Azubis auftreten,
         * wird eine Fehlernachricht angezeigt.
         */
        $("#Azubis").on("click", ".delete-item-child", function() {

            var auszubildender = $(this).closest(".item-child");

            if (confirm("Soll der Auszubildende " + auszubildender.find("div").first().text() + " wirklich gelöscht werden?")) {

                $("#LoadingSpinner").show();

                var id = $(this).data(ID);

                $.ajax({
                    type: "POST",
                    url: APIAZUBI + "Delete",
                    data: {
                        csrfToken: $("#CsrfToken").val(),
                        id: id
                    },
                    success: function() {
                        RefreshPlan();
                        auszubildender.remove();
                        $("#LoadingSpinner").hide();
                    },
                    error: function() {
                        HandleError("Es traten Fehler beim Löschen des Auszubildenden auf.");
                    }
                });
            }
        });
    })
});
