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
            $("#Azubis").hide(TIME);
            $("#AddAzubiForm").hide(TIME);
            $("#EditAzubiForm").hide(TIME);
        }

        function RefreshPlan() {
            $.get(API + "Refresh/Plan", function(data) {
                $("#Plan").html(data);
            });
        }

        function ShowAzubis() {
            HideViews();
            $("#ShowAzubisButton").click();
        }

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

        $("#AddAzubiForm").on("change", `input[name="${ AUSBILDUNGSSTART }"]`, function(e) {

            var value = e.target.value;
            var inputAusbildungsende = $(`#AddAzubiForm input[name="${ AUSBILDUNGSENDE }"]`);
            inputAusbildungsende.attr("min", value);

            if (inputAusbildungsende.val() && CompareDates(value, inputAusbildungsende.val())) {
                inputAusbildungsende.val(value);
            }
        });

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

        $("#EditAzubiForm").on("change", `input[name="${ AUSBILDUNGSSTART }"]`, function(e) {

            var value = e.target.value;
            var inputAusbildungsende = $(`#EditAzubiForm input[name="${ AUSBILDUNGSENDE }"]`);
            inputAusbildungsende.attr("min", value);

            if (inputAusbildungsende.val() && CompareDates(value, inputAusbildungsende.val())) {
                inputAusbildungsende.val(value);
            }
        });

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
