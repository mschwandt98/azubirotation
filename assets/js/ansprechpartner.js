jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";
        const APIANSPRECHPARTNER = API + "Ansprechpartner/";

        // Bezeichnungen für Ansprechpartnerproperties
        const ID = "id";
        const NAME = "name";
        const EMAIL = "email";
        const ID_ABTEILUNG = "id_abteilung";

        function GetAbteilungen() {
            return $.get(API + "Abteilung/Get");
        }

        function HideViews() {
            $("#Ansprechpartner").hide();
            $("#AddAnsprechpartnerForm").hide();
            $("#EditAnsprechpartner").hide();
        }

        function ShowAnsprechpartner() {
            $("#ShowAnsprechpartnerButton").click();
        }

        $("#ShowAnsprechpartnerButton").on("click", function(e) {

            $.get(APIANSPRECHPARTNER + "Get", function(data) {
                data = JSON.parse(data);

                var ansprechpartnerDiv = $("#Ansprechpartner");
                ansprechpartnerDiv.empty();

                data.forEach(ansprechpartner => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(ansprechpartner.Name);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child")
                        .addClass("secondary-button")
                        .data(ID, ansprechpartner.ID)
                        .data(NAME, ansprechpartner.Name)
                        .data(EMAIL, ansprechpartner.Email)
                        .data(ID_ABTEILUNG, ansprechpartner.ID_Abteilung)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child")
                        .addClass("secondary-button")
                        .data(ID, ansprechpartner.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);

                    item.append(outputDiv);
                    item.append(buttonContainer);
                    ansprechpartnerDiv.append(item);
                });

                HideViews();
                ansprechpartnerDiv.show();
            });
        });

        $("#ShowAddAnsprechpartnerForm").on("click", function(e) {

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
                form.show();
            });
        });

        $("#AddAnsprechpartnerForm").on("submit", function(e) {

            e.preventDefault();
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
                success: function(response) {
                    nameInput.val("");
                    emailInput.val("");
                    abteilungSelect.find("option").remove();

                    HideViews();
                    ShowAnsprechpartner();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Ansprechpartner").on("click", ".edit-item-child", function(e) {

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
                form.show();
            });
        });

        $("#EditAnsprechpartner").on("submit", function(e) {

            e.preventDefault();
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
                success: function(response) {
                    idInput.val("");
                    nameInput.val("");
                    emailInput.val("");
                    abteilungSelect.find("option").remove();

                    HideViews();
                    $("#ShowAnsprechpartnerButton").click();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Ansprechpartner").on("click", ".delete-item-child", function(e) {

            var id = $(this).data(ID);
            var ansprechpartner = $(this).closest(".item-child");

            $.ajax({
                type: "POST",
                url: APIANSPRECHPARTNER + "Delete",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: id
                },
                success: function(response) {
                    ansprechpartner.remove();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });
    })
});
