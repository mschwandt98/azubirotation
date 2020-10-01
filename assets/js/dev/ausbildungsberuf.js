jQuery(function($) {
    $(document).ready(function() {

        const API = "rest/";
        const APIAUSBILDUNGSBERUF = API + "Ausbildungsberuf/";

        // Bezeichnungen für Ausbildungsberufsproperties
        const ID = "id";
        const BEZEICHNUNG = "bezeichnung";

        function HideViews() {
            $("#Ausbildungsberufe").hide();
            $("#AddAusbildungsberufForm").hide();
            $("#EditAusbildungsberufForm").hide();
        }

        function ShowAusbildungsberufe() {
            $("#ShowAusbildungsberufeButton").click();
        }

        $("#ShowAusbildungsberufeButton").on("click", function() {

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
                        .addClass("edit-item-child")
                        .addClass("secondary-button")
                        .data(ID, ausbildungsberuf.ID)
                        .data(BEZEICHNUNG, ausbildungsberuf.Bezeichnung)
                        .val("Bearbeiten");

                    var deleteButton = $('<input type="button" />')
                        .addClass("delete-item-child")
                        .addClass("secondary-button")
                        .data(ID, ausbildungsberuf.ID)
                        .val("Löschen");

                    buttonContainer.append(editButton).append(deleteButton);
                    item.append(outputDiv);
                    item.append(buttonContainer);
                    ausbildungsberufe.append(item);
                });

                HideViews();
                ausbildungsberufe.show();
                $("#LoadingSpinner").hide();
            });
        });

        $("#ShowAddAusbildungsberufForm").on("click", function() {
            HideViews();
            $("#AddAusbildungsberufForm").show();
        });

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
                }
            })
        });

        $("#Ausbildungsberufe").on("click", ".edit-item-child", function() {

            var id = $(this).data(ID);
            var bezeichnung = $(this).data(BEZEICHNUNG);

            var form = $("#EditAusbildungsberufForm");
            form.find(`input[name="${ ID }"]`).val(id);
            form.find(`input[name="${ BEZEICHNUNG }"]`).val(bezeichnung);

            HideViews();
            form.show();
        });

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
                }
            })
        });

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
                    error: function(jqXHR, textStatus, errorThrown ) {
                        $("#LoadingSpinner").hide();
                        var emb = $("#ErrorMessageBox");
                        emb.find(".message").text("Es traten Fehler beim Löschen des Ausbildungsberufes auf.");
                        emb.show();
                        setTimeout(() => { emb.fadeOut().text(); }, 10000);
                    }
                });
            }
        });
    })
});
