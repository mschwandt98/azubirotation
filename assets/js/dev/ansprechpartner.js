jQuery(function($) {
    $(document).ready(function() {

        // Bezeichnungen für Ansprechpartnerproperties
        const ID = "id";
        const NAME = "name";
        const EMAIL = "email";
        const ID_ABTEILUNG = "id_abteilung";

        function GetAbteilungen() {
            return $.get(APIABTEILUNG + "Get");
        }

        function HandleError(errorMessage = "Es trat ein unbekannter Fehler auf.") {

            $("#LoadingSpinner").hide();
            var emb = $("#ErrorMessageBox");
            emb.find(".message").text(errorMessage);
            emb.show();
            setTimeout(() => { emb.fadeOut().text(); }, 10000);
        }

        function HideViews() {
            $("#Ansprechpartner").hide(TIME);
            $("#AddAnsprechpartnerForm").hide(TIME);
            $("#EditAnsprechpartner").hide(TIME);
        }

        function ShowAnsprechpartner() {
            $("#ShowAnsprechpartnerButton").click();
        }

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
                ansprechpartnerDiv.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

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
                form.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

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
                form.show(TIME);
                $("#LoadingSpinner").hide();
            });
        });

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
