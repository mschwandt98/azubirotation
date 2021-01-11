$(document).ready(function() {

    // Bezeichnungen für Standardplanproperties
    const STANDARDPLAN_ID_AUSBILDUNGSBERUF = 'id_ausbildungsberuf';
    const STANDARDPLAN_ID_ABTEILUNG = 'id_abteilung';
    const STANDARDPLAN_WOCHEN = 'wochen';
    const STANDARDPLAN_PRAEFERIEREN = 'praeferieren';
    const STANDARDPLAN_OPTIONAL = 'optional';

    // TODO: besseren Weg finden
    var Abteilungen;

    /**
     * Erzeugt HTML zum Hinzufügen einer Phase im Standardplan.
     *
     * @param {HTMLElement} container Der Container, in dem die HTML
     *                                eingefügt werden soll.
     */
    function AddPhaseHtml(container) {
        var item = $('<div></div>').addClass('phase');
        var abteilungenSelect = $('<select></select>').attr('name', STANDARDPLAN_ID_ABTEILUNG);

        AddSelectOption(abteilungenSelect, Abteilungen);

        var abteilungLabel = $('<label></label>')
            .append($('<span></span>').text('Abteilung auswählen'))
            .append(abteilungenSelect);

        var wochenDiv = $('<label></label>')
            .append($('<span></span>').text('Wochen'))
            .append($(`<input type="number" name="${ STANDARDPLAN_WOCHEN }" />`));

        var praeferieren = $('<label></label>')
                .append($('<span></span>').text('Präferieren'))
                .append($(`<input type="checkbox" name="${ STANDARDPLAN_PRAEFERIEREN }" />`));

        var optional = $('<label></label>')
                .append($('<span></span>').text('Optional'))
                .append($(`<input type="checkbox" name="${ STANDARDPLAN_OPTIONAL }" />`));

        var deletePhaseButton = $('<input type="button" />')
            .addClass('delete-phase')
            .val('Phase löschen');

        container.append(
            item.append(abteilungLabel)
                .append(wochenDiv)
                .append(praeferieren)
                .append(optional)
                .append(deletePhaseButton)
        );
    }

    /**
     * Fügt option-Elemente zu einem select-Element hinzu.
     *
     * @param {HTMLSelectElement} selectItem Das select-Element, zu dem die
     *                                       option-Elemente hinzugefügt
     *                                       werden sollen.
     * @param {array} optionData Die Daten für die einzelenen
     *                           option-Elemente.
     */
    function AddSelectOption(selectItem, optionData) {

        selectItem.empty();

        optionData.forEach(data => {
            selectItem.append(
                $(`<option>${ data.Bezeichnung }</option>`).val(data.ID)
            );
        });
    }

    /**
     * Holt alle Abteilungen mittels einer AJAX-Anfrage des Typs GET.
     *
     * @return {string} Alle Abteilungen im JSON-Format.
     */
    function GetAbteilungen() {
        return $.get(APIABTEILUNG + 'Get');
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
     * Führt das Click-Event des Buttons zum Anzeigen der Standardpläne aus.
     */
    function ShowStandardPlaene() {
        $('.data-item.standardplaene-item .show-data').click();
    }

    /**
     * Holt alle Standardpläne mittels einer GET-Anfrage und zeigt diese an.
     * Für jeden Standardplan wird ein Button zum Bearbeiten und Löschen des
     * jeweiligen Standardplans erstellt.
     */
    $('.data-item.standardplaene-item').on('click', '.show-data', function() {

        $('#LoadingSpinner').show();
        var el = $(this);

        GetAbteilungen().then(abteilungen => {
            Abteilungen = JSON.parse(abteilungen);
        });

        $.get(APISTANDARDPLAN + 'Get', function(data) {
            data = JSON.parse(data);

            var standardplaene = $('#Standardplaene');
            standardplaene.empty();

            $.each(data, function(index, standardplan) {
                let item = $('<div></div>').addClass('item-child');
                let outputDiv = $('<div></div>').text(standardplan.Ausbildungsberuf);
                let buttonContainer = $('<div></div>');

                let editButton = $('<input type="button" />')
                    .addClass('edit-item-child secondary-button')
                    .data(STANDARDPLAN_ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                    .val('Bearbeiten');

                let deleteButton = $('<input type="button" />')
                    .addClass('delete-item-child secondary-button')
                    .data(STANDARDPLAN_ID_AUSBILDUNGSBERUF, standardplan.ID_Ausbildungsberuf)
                    .val('Löschen');

                buttonContainer.append(editButton).append(deleteButton);
                item.append(outputDiv);
                item.append(buttonContainer);
                standardplaene.append(item);
            });

            HideViews(el.closest('.data-item'));
            standardplaene.stop().show(TIME);
            $('#LoadingSpinner').hide();
        });
    });

    /**
     * Zeigt das Formular zum Hinzufügen eines Standardplans an. Da im
     * Formular die Ausbildungsberufe und Abteilungen benötigt werden,
     * werden AJAX-Anfragen des Typs GET gestellt, um diese zu holen.
     */
    $('.data-item.standardplaene-item').on('click', '.add-data', function() {

        $('#LoadingSpinner').show();
        var form = $('#AddStandardplanForm');

        GetAusbildungsberufe().then(ausbildungsberufe => {

            GetAbteilungen().then(abteilungen => {

                abteilungen = JSON.parse(abteilungen);
                Abteilungen = abteilungen;

                AddSelectOption(
                    form.find(`select[name="${ STANDARDPLAN_ID_AUSBILDUNGSBERUF }"`),
                    JSON.parse(ausbildungsberufe)
                );

                AddSelectOption(
                    form.find(`select[name="${ STANDARDPLAN_ID_ABTEILUNG }"`),
                    abteilungen
                );

                HideViews(form.closest('.data-item'));
                form.stop().show(TIME);
                $('#LoadingSpinner').hide();
            });
        })
    });

    /**
     * Die aktuelle Phase des jeweiligen Standardplans wird gelöscht.
     */
    $('#AddStandardplanForm').on('click', 'input.delete-phase', function() {
        $(this).closest('.phase').remove();
    });

    /**
     * Eine Phase wird zum aktuellen Standardplan hinzugefügt.
     */
    $('#AddStandardplanForm').on('click', 'input.add-phase', function() {
        AddPhaseHtml($('#AddStandardplanForm .plan-phasen').eq(0));
    });

    /**
     * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
     * zum Hinzufügen eines Standardplans. Bei erfolgreicher Speicherung des
     * Standardplans wird das Formular versteckt und die Ansicht aller
     * Standardpläne wird eingeblendet. Bei einem Fehler wird eine
     * Fehlernachricht ausgegeben.
     *
     * @param {Event} e Das ausgelöste Submit-Event.
     */
    $('#AddStandardplanForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var ausbildungsberufeSelect = form.find(`select[name="${ STANDARDPLAN_ID_AUSBILDUNGSBERUF }"`);
        var phaseDivs = form.find('.phase');
        var phasen = [];

        phaseDivs.each(index => {

            let phase = phaseDivs.eq(index);
            let abteilungenSelect = phase.find(`select[name="${ STANDARDPLAN_ID_ABTEILUNG }"]`);
            let wochenInput = phase.find(`input[name="${ STANDARDPLAN_WOCHEN }"]`);
            let praeferierenCheckbox = phase.find(`input[name="${ STANDARDPLAN_PRAEFERIEREN }"]`);
            let optionalCheckbox = phase.find(`input[name="${ STANDARDPLAN_OPTIONAL }"]`);

            phasen.push({
                id_abteilung: abteilungenSelect.val(),
                wochen: wochenInput.val(),
                praeferieren: praeferierenCheckbox.val(),
                optional: optionalCheckbox.val()
            });
        });

        $.ajax({
            type: 'POST',
            url: APISTANDARDPLAN + 'Add',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id_ausbildungsberuf: ausbildungsberufeSelect.val(),
                phasen: phasen
            },
            success: function() {

                ausbildungsberufeSelect.find('option').remove();
                phaseDivs.not(':first').remove();

                phaseDivs.eq(0).find(`select[name="${ STANDARDPLAN_ID_ABTEILUNG }"]`).find('option').remove();
                phaseDivs.eq(0).find(`input[name="${ STANDARDPLAN_WOCHEN }"]`).val('');
                phaseDivs.eq(0).find(`input[name="${ STANDARDPLAN_PRAEFERIEREN }"]`).val('');
                phaseDivs.eq(0).find(`input[name="${ STANDARDPLAN_OPTIONAL }"]`).val('');

                ShowStandardPlaene();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Anlegen des Standardplans auf.');
            }
        })
    });

    /**
     * Fügt die Daten des zu bearbeitenden Standardplans in die Felder des
     * Formulars zum Bearbeiten einer Standardplans ein und blendet dieses
     * ein.
     */
    $('#Standardplaene').on('click', '.edit-item-child', function() {

        $('#LoadingSpinner').show();

        var id_ausbildungsberuf = $(this).data(STANDARDPLAN_ID_AUSBILDUNGSBERUF);
        var form = $('#EditStandardplanForm');
        form.find(`input[name="${ STANDARDPLAN_ID_AUSBILDUNGSBERUF }"`).val(id_ausbildungsberuf);

        $.get(APISTANDARDPLAN + 'GetTemplate', { id_ausbildungsberuf: id_ausbildungsberuf}, function(data) {
            form.find('.plan').empty().append(data);

            HideViews(form.closest('.data-item'));
            form.stop().show(TIME);
            $('#LoadingSpinner').hide();
        });
    });

    /**
     * Die aktuelle Phase des jeweiligen Standardplans wird gelöscht.
     */
    $('#EditStandardplanForm').on('click', 'input.delete-phase', function() {
        $(this).closest('.phase').remove();
    });

    /**
     * Eine Phase wird zum aktuellen Standardplan hinzugefügt.
     */
    $('#EditStandardplanForm').on('click', 'input.add-phase', function() {
        AddPhaseHtml($('#EditStandardplanForm .plan-phasen').eq(0));
    });

    /**
     * Stellt eine AJAX-Anfrage vom Typ POST beim Submitten des Formulars
     * zum Bearbeiten eines Standardplans. Bei erfolgreicher Aktualisierung
     * des Standardplans wird das Formular versteckt und die Ansicht aller
     * Standardpläne wird eingeblendet. Bei einem Fehler wird eine
     * Fehlernachricht ausgegeben.
     *
     * @param {Event} e Das ausgelöste Submit-Event.
     */
    $('#EditStandardplanForm').on('submit', function(e) {

        e.preventDefault();
        $('#LoadingSpinner').show();

        var form = $(this);
        var idAusbildungsberufInput = form.find(`input[name="${ STANDARDPLAN_ID_AUSBILDUNGSBERUF }"`);
        var phaseDivs = form.find('.phase');
        var phasen = [];

        phaseDivs.each(index => {

            let phase = phaseDivs.eq(index);
            let abteilungenSelect = phase.find(`select[name="${ STANDARDPLAN_ID_ABTEILUNG }"]`);
            let wochenInput = phase.find(`input[name="${ STANDARDPLAN_WOCHEN }"]`);
            let praeferierenCheckbox = phase.find(`input[name="${ STANDARDPLAN_PRAEFERIEREN }"]`).eq(0);
            let optionalCheckbox = phase.find(`input[name="${ STANDARDPLAN_OPTIONAL }"]`).eq(0);

            phasen.push({
                id_abteilung: abteilungenSelect.val(),
                wochen: wochenInput.val(),
                praeferieren: praeferierenCheckbox.prop('checked'),
                optional: optionalCheckbox.prop('checked')
            });
        });

        $.ajax({
            type: 'POST',
            url: APISTANDARDPLAN + 'Edit',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id_ausbildungsberuf: idAusbildungsberufInput.val(),
                phasen: phasen
            },
            success: function() {

                phaseDivs.not(':first').remove();
                phaseDivs.eq(0).find(`select[name="${ STANDARDPLAN_ID_ABTEILUNG }"]`).find('option').remove();
                phaseDivs.eq(0).find(`input[name="${ STANDARDPLAN_PRAEFERIEREN }"]`).prop('checked', false);
                phaseDivs.eq(0).find(`input[name="${ STANDARDPLAN_OPTIONAL }"]`).prop('checked', false);

                HideViews(form.closest('.data-item'));
                ShowStandardPlaene();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Aktualisieren des Standardplans auf.');
            }
        })
    });

    /**
     * Stellt eine AJAX-Anfrage vom Typ POST zum Löschen des jeweiligen
     * Standardplans. Nach erfolgreichem Löschen des Standardplans wird die
     * Ansicht aller Standardpläne aktualisiert. Wenn Fehler beim Löschen
     * des Standardplans auftreten, wird eine Fehlernachricht angezeigt.
     */
    $('#Standardplaene').on('click', '.delete-item-child', function() {

        $('#LoadingSpinner').show();

        var el = $(this);

        $.ajax({
            type: 'POST',
            url: APISTANDARDPLAN + 'Delete',
            data: {
                csrfToken: $('#CsrfToken').val(),
                id_ausbildungsberuf: el.data(STANDARDPLAN_ID_AUSBILDUNGSBERUF)
            },
            success: function() {
                el.closest('.item-child').remove();
                $('#LoadingSpinner').hide();
            },
            error: function() {
                HandleError('Es traten Fehler beim Löschen des Standardplans auf.');
            }
        })
    });
});
