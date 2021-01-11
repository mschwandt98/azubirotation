$(document).ready(function() {

    // Bezeichnungen für Abteilungsproperties
    const ABTEILUNG_ID = 'id';
    const ABTEILUNG_BEZEICHNUNG = 'bezeichnung';
    const ABTEILUNG_MAXAZUBIS = 'maxazubis';
    const ABTEILUNG_FARBE = 'farbe';

    /**
     * Aktualisiert die Legende im Footer mittels einer GET-Anfrage.
     */
    function RefreshFooter() {
        $('#Legende').load(API + 'Refresh/Legende');
    }

    /**
     * Führt das Click-Event des Buttons zum Anzeigen der Abteilungen aus.
     */
    function ShowAbteilungen() {
        $('#SubMenu .data-item.abteilung-item .show-data').click();
    }

    /**
     * Holt alle Abteilungen mittels einer GET-Anfrage und zeigt diese an.
     * Für jede Abteilung wird ein Button zum Bearbeiten und Löschen der
     * jeweiligen Abteilung erstellt.
     */
    $('#SubMenu .data-item.abteilung-item').on('click', '.show-data', function() {

        var el = $(this);

        $('#LoadingSpinner').show();
        $.get(APIABTEILUNG + 'Get', function(data) {
            data = JSON.parse(data);

            var abteilungen = $('#Abteilungen');
            abteilungen.empty();

            data.forEach(abteilung => {
                let item = $('<div></div>').addClass('item-child');
                let outputDiv = $('<div></div>').text(abteilung.Bezeichnung);
                let buttonContainer = $('<div></div>');

                let editButton = $('<input type="button" />')
                    .addClass('edit-item-child secondary-button')
                    .data(ABTEILUNG_ID, abteilung.ID)
                    .data(ABTEILUNG_BEZEICHNUNG, abteilung.Bezeichnung)
                    .data(ABTEILUNG_MAXAZUBIS, abteilung.MaxAzubis)
                    .data(ABTEILUNG_FARBE, abteilung.Farbe)
                    .val('Bearbeiten');

                let deleteButton = $('<input type="button" />')
                    .addClass('delete-item-child secondary-button')
                    .data(ABTEILUNG_ID, abteilung.ID)
                    .val('Löschen');

                buttonContainer.append(editButton).append(deleteButton);
                item.append(outputDiv);
                item.append(buttonContainer);
                abteilungen.append(item);
            });

            HideViews(el.closest('.data-item'));
            abteilungen.stop().show(TIME);
            $('#LoadingSpinner').hide();
        });
    });

    /**
     * Zeigt das Formular zum Hinzufügen einer Abteilung an.
     */
    $('#SubMenu .data-item.abteilung-item').on('click', '.add-data', function() {
        HideViews($(this).closest('.data-item'));
        $('#AddAbteilungForm').stop().show(TIME);
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
    $('#AddAbteilungForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var bezeichnungInput = form.find(`input[name="${ ABTEILUNG_BEZEICHNUNG }"]`).eq(0);
        var maxAzubisInput = form.find(`input[name="${ ABTEILUNG_MAXAZUBIS }"]`).eq(0);
        var farbeInput = form.find(`input[name="${ ABTEILUNG_FARBE }"]`).eq(0);

        $.ajax({
            type: 'POST',
            url: APIABTEILUNG + 'Add',
            data: {
                csrfToken: $('#CsrfToken').val(),
                bezeichnung: bezeichnungInput.val(),
                maxAzubis: maxAzubisInput.val(),
                farbe: farbeInput.val()
            },
            success: function() {
                bezeichnungInput.val('');
                maxAzubisInput.val('');
                farbeInput.val('#ffffff');

                RefreshFooter();
                ShowAbteilungen();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Anlegen der Abteilung auf.');
            }
        })
    });

    /**
     * Fügt die Daten der zu bearbeitenden Abteilung in die Felder des
     * Formulars zum Bearbeiten einer Abteilung ein und blendet dieses ein.
     */
    $('#Abteilungen').on('click', '.edit-item-child', function() {

        var el = $(this);
        var id = el.data(ABTEILUNG_ID);
        var bezeichnung = el.data(ABTEILUNG_BEZEICHNUNG);
        var maxAzubis = el.data(ABTEILUNG_MAXAZUBIS);
        var farbe = el.data(ABTEILUNG_FARBE);

        var form = $('#EditAbteilungForm');
        form.find(`input[name="${ ABTEILUNG_ID }"]`).val(id);
        form.find(`input[name="${ ABTEILUNG_BEZEICHNUNG }"]`).val(bezeichnung);
        form.find(`input[name="${ ABTEILUNG_MAXAZUBIS }"]`).val(maxAzubis);
        form.find(`input[name="${ ABTEILUNG_FARBE }"]`).val(farbe);

        HideViews(el.closest('.data-item'));
        form.stop().show(TIME);
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
    $('#EditAbteilungForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var idInput = form.find(`input[name="${ ABTEILUNG_ID }"]`);
        var bezeichnungInput = form.find(`input[name="${ ABTEILUNG_BEZEICHNUNG }"]`);
        var maxAzubisInput = form.find(`input[name="${ ABTEILUNG_MAXAZUBIS }"]`);
        var farbeInput = form.find(`input[name="${ ABTEILUNG_FARBE }"]`);

        $.ajax({
            type: 'POST',
            url: APIABTEILUNG + 'Edit',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id: idInput.val(),
                bezeichnung: bezeichnungInput.val(),
                maxAzubis: maxAzubisInput.val(),
                farbe: farbeInput.val()
            },
            success: function() {
                HideViews(form.closest('.data-item'));

                idInput.val('');
                bezeichnungInput.val('');
                maxAzubisInput.val('');
                farbeInput.val('#ffffff');

                RefreshFooter();
                ShowAbteilungen();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Aktualisieren der Abteilung auf.');
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
    $('#Abteilungen').on('click', '.delete-item-child', function() {

        var el = $(this);
        var abteilung = el.closest('.item-child');

        if (confirm('Soll die Abteilung ' + abteilung.find('div').first().text() + ' wirklich gelöscht werden?')) {

            $('#LoadingSpinner').show();
            var id = el.data(ABTEILUNG_ID);

            $.ajax({
                type: 'POST',
                url: APIABTEILUNG + 'Delete',
                data: {
                    csrfToken: $('#CsrfToken').val(),
                    id: id
                },
                success: function() {
                    HideViews(el.closest('.data-item'));
                    RefreshFooter();
                    abteilung.remove();
                    $('#LoadingSpinner').hide();
                },
                error: function() {
                    HandleError('Es traten Fehler beim Löschen der Abteilung auf.');
                }
            });
        }
    });
});
