jQuery(function($) {
    $(document).ready(function() {

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

        function RefreshFooter() {
            $.get(API + "Refresh/Footer", function(data) {
                $("#Footer").html(data);
            });
        }

        function ShowAbteilungen() {
            $("#ShowAbteilungenButton").click();
        }

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
                $("#LoadingSpinner").hide();
            });
        });

        $("#ShowAddAbteilungForm").on("click", function() {
            HideViews();
            $("#AddAbteilungForm").show();
        });

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
                }
            })
        });

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
            form.show();
        });

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
                    farbeInput.val("");

                    RefreshFooter();
                    ShowAbteilungen();
                    $("#LoadingSpinner").hide();
                }
            })
        });

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
                    error: function(jqXHR, textStatus, errorThrown ) {
                        $("#LoadingSpinner").hide();
                        var emb = $("#ErrorMessageBox");
                        emb.find(".message").text("Es traten Fehler beim Löschen der Abteilung auf.");
                        emb.show();
                        setTimeout(() => { emb.fadeOut().text(); }, 10000);
                    }
                });
            }
        });
    })
});
