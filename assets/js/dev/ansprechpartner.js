$(document).ready(function() {

    // Bezeichnungen für Ansprechpartnerproperties
    const ANSPRECHPARTNER_ID = 'id';
    const ANSPRECHPARTNER_NAME = 'name';
    const ANSPRECHPARTNER_EMAIL = 'email';
    const ANSPRECHPARTNER_ID_ABTEILUNG = 'id_abteilung';

    /**
     * Holt alle Abteilungen mittels einer AJAX-Anfrage des Typs GET.
     *
     * @return {string} Alle Abteilungen im JSON-Format.
     */
    function GetAbteilungen() {
        return $.get(APIABTEILUNG + 'Get');
    }

    /**
     * Führt das Click-Event des Buttons zum Anzeigen der Ansprechpartner
     * aus.
     */
    function ShowAnsprechpartner() {
        $('.data-item.ansprechpartner-item .show-data').click();
    }

    /**
     * Holt alle Ansprechpartner mittels einer GET-Anfrage und zeigt diese
     * an. Für jeden Ansprechpartner wird ein Button zum Bearbeiten und
     * Löschen des jeweiligen Ansprechpartners erstellt.
     */
    $('.data-item.ansprechpartner-item').on('click', '.show-data', function() {

        $('#LoadingSpinner').show();
        var el = $(this);

        $.get(APIANSPRECHPARTNER + 'Get', function(data) {
            data = JSON.parse(data);

            var ansprechpartnerDiv = $('#Ansprechpartner');
            ansprechpartnerDiv.empty();

            data.forEach(ansprechpartner => {
                let item = $('<div></div>').addClass('item-child');
                let outputDiv = $('<div></div>').text(ansprechpartner.Name);
                let buttonContainer = $('<div></div>');

                let editButton = $('<input type="button" />')
                    .addClass('edit-item-child secondary-button')
                    .data(ANSPRECHPARTNER_ID, ansprechpartner.ID)
                    .data(ANSPRECHPARTNER_NAME, ansprechpartner.Name)
                    .data(ANSPRECHPARTNER_EMAIL, ansprechpartner.Email)
                    .data(ANSPRECHPARTNER_ID_ABTEILUNG, ansprechpartner.ID_Abteilung)
                    .val('Bearbeiten');

                let deleteButton = $('<input type="button" />')
                    .addClass('delete-item-child secondary-button')
                    .data(ANSPRECHPARTNER_ID, ansprechpartner.ID)
                    .val('Löschen');

                buttonContainer.append(editButton).append(deleteButton);

                item.append(outputDiv);
                item.append(buttonContainer);
                ansprechpartnerDiv.append(item);
            });

            HideViews(el.closest('.data-item'));
            ansprechpartnerDiv.stop().show(TIME);
            $('#LoadingSpinner').hide();
        });
    });

    /**
     * Zeigt das Formular zum Hinzufügen eines Ansprechpartners an. Da für
     * dieses Formular alle Abteilungen benötigt werden, wird eine
     * AJAX-Anfrage des Typs GET gestellt.
     */
    $('.data-item.ansprechpartner-item').on('click', '.add-data', function() {

        $('#LoadingSpinner').show();

        var form = $('#AddAnsprechpartnerForm');
        var abteilungSelect = form.find(`select[name="${ ANSPRECHPARTNER_ID_ABTEILUNG }"]`);

        GetAbteilungen().then(abteilungen => {
            abteilungen = JSON.parse(abteilungen);

            abteilungen.forEach(abteilung => {

                let item = $(`<option>${ abteilung.Bezeichnung }</option>`)
                    .val(abteilung.ID);

                abteilungSelect.append(item);
            });

            HideViews(form.closest('.data-item'));
            form.stop().show(TIME);
            $('#LoadingSpinner').hide();
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
    $('#AddAnsprechpartnerForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var nameInput = form.find(`input[name="${ ANSPRECHPARTNER_NAME }"]`).eq(0);
        var emailInput = form.find(`input[name="${ ANSPRECHPARTNER_EMAIL }"]`).eq(0);
        var abteilungSelect = form.find(`select[name="${ ANSPRECHPARTNER_ID_ABTEILUNG }"]`).eq(0);

        $.ajax({
            type: 'POST',
            url: APIANSPRECHPARTNER + 'Add',
            data: {
                csrfToken: $('#CsrfToken').val(),
                name: nameInput.val(),
                email: emailInput.val(),
                id_abteilung: abteilungSelect.val()
            },
            success: function() {
                nameInput.val('');
                emailInput.val('');
                abteilungSelect.find('option').remove();

                HideViews(form.closest('.data-item'));
                ShowAnsprechpartner();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Anlegen des Ansprechpartners auf.');
            }
        })
    });

    /**
     * Fügt die Daten des zu bearbeitenden Ansprechpartners in die Felder
     * des Formulars zum Bearbeiten eines Ansprechpartners ein und blendet
     * dieses ein.
     */
    $('#Ansprechpartner').on('click', '.edit-item-child', function() {

        $('#LoadingSpinner').show();

        var el = $(this);
        var id = el.data(ANSPRECHPARTNER_ID);
        var name = el.data(ANSPRECHPARTNER_NAME);
        var email = el.data(ANSPRECHPARTNER_EMAIL);
        var id_abteilung = el.data(ANSPRECHPARTNER_ID_ABTEILUNG);

        GetAbteilungen().then(abteilungen => {
            abteilungen = JSON.parse(abteilungen);

            let form = $('#EditAnsprechpartner');
            var abteilungSelect = form.find(`select[name="${ ANSPRECHPARTNER_ID_ABTEILUNG }"]`);

            abteilungen.forEach(abteilung => {
                let item = $(`<option>${ abteilung.Bezeichnung }</option>`)
                    .val(abteilung.ID);

                abteilungSelect.append(item);
            });

            form.find(`input[name="${ ANSPRECHPARTNER_ID }"]`).val(id);
            form.find(`input[name="${ ANSPRECHPARTNER_NAME }"]`).val(name);
            form.find(`input[name="${ ANSPRECHPARTNER_EMAIL }"]`).val(email);
            abteilungSelect.val(id_abteilung);

            HideViews(el.closest('.data-item'));
            form.stop().show(TIME);
            $('#LoadingSpinner').hide();
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
    $('#EditAnsprechpartner').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var idInput = form.find(`input[name="${ ANSPRECHPARTNER_ID }"]`);
        var nameInput = form.find(`input[name="${ ANSPRECHPARTNER_NAME }"]`);
        var emailInput = form.find(`input[name="${ ANSPRECHPARTNER_EMAIL }"]`);
        var abteilungSelect = form.find(`select[name="${ ANSPRECHPARTNER_ID_ABTEILUNG }"]`);

        $.ajax({
            type: 'POST',
            url: APIANSPRECHPARTNER + 'Edit',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id: idInput.val(),
                name: nameInput.val(),
                email: emailInput.val(),
                id_abteilung: abteilungSelect.val()
            },
            success: function() {
                idInput.val('');
                nameInput.val('');
                emailInput.val('');
                abteilungSelect.find('option').remove();

                HideViews(form.closest('.data-item'));
                $('.data-item.ansprechpartner-item .show-data').click();
            },
            error: function() {
                HandleError('Es traten Fehler beim Aktualisieren des Ansprechpartners auf.');
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
    $('#Ansprechpartner').on('click', '.delete-item-child', function() {

        var el = $(this);
        var ansprechpartner = el.closest('.item-child');

        if (confirm('Soll der Ansprechpartner ' + ansprechpartner.find('div').first().text() + ' wirklich gelöscht werden?')) {

            $('#LoadingSpinner').show();

            var id = el.data(ANSPRECHPARTNER_ID);

            $.ajax({
                type: 'POST',
                url: APIANSPRECHPARTNER + 'Delete',
                data: {
                    csrfToken: $('#CsrfToken').val(),
                    id: id
                },
                success: function() {
                    ansprechpartner.remove();
                    $('#LoadingSpinner').hide();
                },
                error: function() {
                    HandleError('Es traten Fehler beim Löschen des Ansprechpartners auf.');
                }
            });
        }
    });
});
