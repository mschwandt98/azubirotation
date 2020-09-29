jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";
        const APIABTEILUNG = API + "Abteilung/";

        // Bezeichnungen für Abteilungsproperties
        const ID = "id";
        const BEZEICHNUNG = "bezeichnung";
        const MAXAZUBIS = "maxazubis";
        const FARBE = "farbe";

        function HideViews() {
            $("#Abteilungen").hide();
            $("#AddAbteilungForm").hide();
            $("#EditAbteilungForm").hide();
        }

        function ShowAbteilungen() {
            $("#ShowAbteilungenButton").click();
        }

        $("#ShowAbteilungenButton").on("click", function(e) {

            $.get(APIABTEILUNG + "Get", function(data) {
                data = JSON.parse(data);

                var abteilungen = $("#Abteilungen");
                abteilungen.empty();

                data.forEach(abteilung => {
                    var item = $("<div></div>").addClass("item-child");
                    var outputDiv = $("<div></div>").text(abteilung.Bezeichnung);
                    var buttonContainer = $("<div></div>");

                    var editButton = $('<input type="button" />')
                        .addClass("edit-item-child")
                        .addClass("secondary-button")
                        .data(ID, abteilung.ID)
                        .data(BEZEICHNUNG, abteilung.Bezeichnung)
                        .data(MAXAZUBIS, abteilung.MaxAzubis)
                        .data(FARBE, abteilung.Farbe)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child")
                        .addClass("secondary-button")
                        .data(ID, abteilung.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    abteilungen.append(item);
                });

                HideViews();
                abteilungen.show();
            });
        });

        $("#ShowAddAbteilungForm").on("click", function(e) {
            HideViews();
            $("#AddAbteilungForm").show();
        });

        $("#AddAbteilungForm").on("submit", function(e) {

            e.preventDefault();

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
                success: function(response) {
                    bezeichnungInput.val("");
                    maxAzubisInput.val("");
                    farbeInput.val("#ffffff");

                    ShowAbteilungen();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Abteilungen").on("click", ".edit-item-child", function(e) {

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
            form.show();
        });

        $("#EditAbteilungForm").on("submit", function(e) {

            e.preventDefault();
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
                success: function(response) {
                    idInput.val("");
                    bezeichnungInput.val("");
                    maxAzubisInput.val("");
                    farbeInput.val("");

                    ShowAbteilungen();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });

        $("#Abteilungen").on("click", ".delete-item-child", function(e) {

            var id = $(this).data(ID);
            var abteilung = $(this).closest(".item-child");

            $.ajax({
                type: "POST",
                url: APIABTEILUNG + "Delete",
                data: {
                    csrfToken: $("#CsrfToken").val(),
                    id: id
                },
                success: function(response) {
                    abteilung.remove();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                }
            })
        });
    })
});
