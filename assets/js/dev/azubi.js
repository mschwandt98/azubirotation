$(document).ready(function() {

    // Bezeichnungen für Azubiproperties
    const AZUBI_ID = 'id';
    const AZUBI_VORNAME = 'vorname';
    const AZUBI_NACHNAME = 'nachname';
    const AZUBI_KUERZEL = 'kuerzel';
    const AZUBI_EMAIL = 'email';
    const AZUBI_ID_AUSBILDUNGSBERUF = 'id_ausbildungsberuf';
    const AZUBI_AUSBILDUNGSSTART = 'ausbildungsstart';
    const AZUBI_AUSBILDUNGSENDE = 'ausbildungsende';
    const AZUBI_PLANUNG_ERSTELLEN = 'planung_erstellen';

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

        let a = new Date(dateA).getTime();
        let b = new Date(dateB).getTime();

        return a > b;
    }

    /**
     * Holt alle Ausbildungsberufe mittels einer AJAX-Anfrage des Typs GET.
     *
     * @return {string} Alle Ausbildungsberufe im JSON-Format.
     */
    function GetAusbildungsberufe() {
        return $.get(APIAUSBILDUNGSBERUF + 'Get');
    }

    /**
     * Aktualisiert die Planung mittels einer AJAX-Anfrage des Typs GET.
     */
    function RefreshPlan() {
        $('#Plan').load(API + 'Refresh/Plan');
    }

    /**
     * Führt das Click-Event des Buttons zum Anzeigen der Azubis aus.
     */
    function ShowAzubis() {
        var container = $('.data-item.azubis-item');
        HideViews(container);
        container.find('.show-data').click();
    }

    /**
     * Holt alle Azubis mittels einer GET-Anfrage und zeigt diese an. Für
     * jeden Azubi wird ein Button zum Bearbeiten und Löschen des jeweiligen
     * Azubis erstellt.
     */
    $('.data-item.azubis-item').on('click', '.show-data', function() {

        $('#LoadingSpinner').show();
        var el = $(this);

        $.get(APIAZUBI + 'Get', function(data) {
            data = JSON.parse(data);

            var azubis = $('#Azubis');
            azubis.empty();

            data.forEach(auszubildender => {
                let item = $('<div></div>').addClass('item-child');
                let outputDiv = $('<div></div>').text(auszubildender.Nachname + ', ' + auszubildender.Vorname);
                let buttonContainer = $('<div></div>');

                let editButton = $('<input type="button" />')
                    .addClass('edit-item-child secondary-button')
                    .data(AZUBI_ID, auszubildender.ID)
                    .data(AZUBI_VORNAME, auszubildender.Vorname)
                    .data(AZUBI_NACHNAME, auszubildender.Nachname)
                    .data(AZUBI_KUERZEL, auszubildender.Kuerzel)
                    .data(AZUBI_EMAIL, auszubildender.Email)
                    .data(AZUBI_ID_AUSBILDUNGSBERUF, auszubildender.ID_Ausbildungsberuf)
                    .data(AZUBI_AUSBILDUNGSSTART, auszubildender.Ausbildungsstart)
                    .data(AZUBI_AUSBILDUNGSENDE, auszubildender.Ausbildungsende)
                    .val('Bearbeiten');

                let deleteButton = $('<input type="button" />')
                    .addClass('delete-item-child secondary-button')
                    .data(AZUBI_ID, auszubildender.ID)
                    .val('Löschen');

                buttonContainer.append(editButton).append(deleteButton);
                item.append(outputDiv);
                item.append(buttonContainer);
                azubis.append(item);
            });

            HideViews(el.closest('.data-item'));
            azubis.stop().show(TIME);
            $('#LoadingSpinner').hide();
        });
    });

    /**
     * Zeigt das Formular zum Hinzufügen eines Azubis an. Da im Formular
     * alle Ausbildungsberufe benötigt werden, wird eine AJAX-Anfrage des
     * Typs GET gestellt, um diese zu holen.
     */
    $('.data-item.azubis-item').on('click', '.add-data', function() {

        $('#LoadingSpinner').show();
        var ausbildungsberufSelect = $('#AddAzubiForm').find(`select[name="${ AZUBI_ID_AUSBILDUNGSBERUF }"]`);
        var el = $(this);

        GetAusbildungsberufe().then(ausbildungsberufe => {
            ausbildungsberufe = JSON.parse(ausbildungsberufe);

            ausbildungsberufe.forEach(ausbildungsberuf => {

                let item = $(`<option>${ ausbildungsberuf.Bezeichnung }</option>`)
                    .val(ausbildungsberuf.ID);

                    ausbildungsberufSelect.append(item);
            });

            HideViews(el.closest('.data-item'));
            $('#AddAzubiForm').stop().show(TIME);
            $('#LoadingSpinner').hide();
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
    $('#AddAzubiForm').on('change', `input[name="${ AZUBI_AUSBILDUNGSSTART }"]`, function(e) {

        var value = e.target.value;
        var inputAusbildungsende = $(`#AddAzubiForm input[name="${ AZUBI_AUSBILDUNGSENDE }"]`);
        inputAusbildungsende.attr('min', value);

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
    $('#AddAzubiForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var vornameInput = form.find(`input[name="${ AZUBI_VORNAME }"]`).eq(0);
        var nachnameInput = form.find(`input[name="${ AZUBI_NACHNAME }"]`).eq(0);
        var kuerzelInput = form.find(`input[name="${ AZUBI_KUERZEL }"]`).eq(0);
        var emailInput = form.find(`input[name="${ AZUBI_EMAIL }"]`).eq(0);
        var ausbildungsberufSelect = form.find(`select[name="${ AZUBI_ID_AUSBILDUNGSBERUF }"]`).eq(0);
        var ausbildungsstartInput = form.find(`input[name="${ AZUBI_AUSBILDUNGSSTART }"]`).eq(0);
        var ausbildungsendeInput = form.find(`input[name="${ AZUBI_AUSBILDUNGSENDE }"]`).eq(0);
        var planung_erstellen = form.find(`input[name="${ AZUBI_PLANUNG_ERSTELLEN }"]`).eq(0);

        $.ajax({
            type: 'POST',
            url: APIAZUBI + 'Add',
            data: {
                csrfToken: $('#CsrfToken').val(),
                vorname: vornameInput.val(),
                nachname: nachnameInput.val(),
                kuerzel: kuerzelInput.val(),
                email: emailInput.val(),
                id_ausbildungsberuf: ausbildungsberufSelect.val(),
                ausbildungsstart: ausbildungsstartInput.val(),
                ausbildungsende: ausbildungsendeInput.val(),
                planung_erstellen: planung_erstellen.prop('checked')
            },
            success: function() {
                vornameInput.val('');
                nachnameInput.val('');
                kuerzelInput.val('');
                emailInput.val('');
                ausbildungsberufSelect.empty();
                planung_erstellen.prop('checked', true)

                RefreshPlan();
                HideViews(form.closest('.data-item'));
                ShowAzubis();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Anlegen des Auszubildenden auf.');
            }
        })
    });

    /**
     * Fügt die Daten des zu bearbeitenden Azubis in die Felder des
     * Formulars zum Bearbeiten eines Azubis ein und blendet dieses ein.
     */
    $('#Azubis').on('click', '.edit-item-child', function() {

        $('#LoadingSpinner').show();

        var el = $(this);
        var id = el.data(AZUBI_ID);
        var vorname = el.data(AZUBI_VORNAME);
        var nachname = el.data(AZUBI_NACHNAME);
        var kuerzel = el.data(AZUBI_KUERZEL);
        var email = el.data(AZUBI_EMAIL);
        var id_ausbildungsberuf = el.data(AZUBI_ID_AUSBILDUNGSBERUF);
        var ausbildungsstart = el.data(AZUBI_AUSBILDUNGSSTART);
        var ausbildungsende = el.data(AZUBI_AUSBILDUNGSENDE);

        GetAusbildungsberufe().then(ausbildungsberufe => {
            ausbildungsberufe = JSON.parse(ausbildungsberufe);

            let form = $('#EditAzubiForm');
            var ausbildungsberufSelect = form.find(`select[name="${ AZUBI_ID_AUSBILDUNGSBERUF }"]`);
            ausbildungsberufSelect.empty();

            ausbildungsberufe.forEach(ausbildungsberuf => {
                let item = $(`<option>${ ausbildungsberuf.Bezeichnung }</option>`)
                    .val(ausbildungsberuf.ID);

                ausbildungsberufSelect.append(item);
            });

            form.find(`input[name="${ AZUBI_ID }"]`).val(id);
            form.find(`input[name="${ AZUBI_VORNAME }"]`).val(vorname);
            form.find(`input[name="${ AZUBI_NACHNAME }"]`).val(nachname);
            form.find(`input[name="${ AZUBI_KUERZEL }"]`).val(kuerzel);
            form.find(`input[name="${ AZUBI_EMAIL }"]`).val(email);
            ausbildungsberufSelect.val(id_ausbildungsberuf);
            form.find(`input[name="${ AZUBI_AUSBILDUNGSSTART }"]`).val(ausbildungsstart);
            form.find(`input[name="${ AZUBI_AUSBILDUNGSENDE }"]`).val(ausbildungsende);
            form.find(`input[name="${ AZUBI_AUSBILDUNGSENDE }"]`).attr("min", ausbildungsstart);

            HideViews(el.closest('.data-item'));
            form.stop().show(TIME);
            $('#LoadingSpinner').hide();
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
    $('#EditAzubiForm').on('change', `input[name="${ AZUBI_AUSBILDUNGSSTART }"]`, function(e) {

        var value = e.target.value;
        var inputAusbildungsende = $(`#EditAzubiForm input[name="${ AZUBI_AUSBILDUNGSENDE }"]`);
        inputAusbildungsende.attr('min', value);

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
    $('#EditAzubiForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var idInput = form.find(`input[name="${ AZUBI_ID }"]`);
        var vornameInput = form.find(`input[name="${ AZUBI_VORNAME }"]`);
        var nachnameInput = form.find(`input[name="${ AZUBI_NACHNAME }"]`);
        var kuerzelInput = form.find(`input[name="${ AZUBI_KUERZEL }"]`);
        var emailInput = form.find(`input[name="${ AZUBI_EMAIL }"]`);
        var ausbildungsberufSelect = form.find(`select[name="${ AZUBI_ID_AUSBILDUNGSBERUF }"]`);
        var ausbildungsstartInput = form.find(`input[name="${ AZUBI_AUSBILDUNGSSTART }"]`);
        var ausbildungsendeInput = form.find(`input[name="${ AZUBI_AUSBILDUNGSENDE }"]`);
        var planung_erstellen = form.find(`input[name="${ AZUBI_PLANUNG_ERSTELLEN }"]`);

        $.ajax({
            type: 'POST',
            url: APIAZUBI + 'Edit',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id: idInput.val(),
                vorname: vornameInput.val(),
                nachname: nachnameInput.val(),
                kuerzel: kuerzelInput.val(),
                email: emailInput.val(),
                id_ausbildungsberuf: ausbildungsberufSelect.val(),
                ausbildungsstart: ausbildungsstartInput.val(),
                ausbildungsende: ausbildungsendeInput.val(),
                planung_erstellen: planung_erstellen.prop('checked')
            },
            success: function() {
                idInput.val('');
                vornameInput.val('');
                nachnameInput.val('');
                kuerzelInput.val('');
                emailInput.val('');
                ausbildungsberufSelect.empty();
                ausbildungsstartInput.val('');
                ausbildungsendeInput.val('');

                RefreshPlan();
                HideViews(form.closest('.data-item'));
                ShowAzubis();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Aktualisieren des Auszubildenden auf.');
            }
        })
    });

    /**
     * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
     * Azubis. Nach erfolgreichem Löschen des Azubis wird die Ansicht aller
     * Azubis aktualisiert. Wenn Fehler beim Löschen des Azubis auftreten,
     * wird eine Fehlernachricht angezeigt.
     */
    $('#Azubis').on('click', '.delete-item-child', function() {

        var el = $(this);
        var auszubildender = el.closest('.item-child');

        if (confirm('Soll der Auszubildende ' + auszubildender.find('div').first().text() + ' wirklich gelöscht werden?')) {

            $('#LoadingSpinner').show();

            var id = el.data(AZUBI_ID);

            $.ajax({
                type: 'POST',
                url: APIAZUBI + 'Delete',
                data: {
                    csrfToken: $('#CsrfToken').val(),
                    id: id
                },
                success: function() {
                    RefreshPlan();
                    auszubildender.remove();
                    $('#LoadingSpinner').hide();
                },
                error: function() {
                    HandleError('Es traten Fehler beim Löschen des Auszubildenden auf.');
                }
            });
        }
    });
});
