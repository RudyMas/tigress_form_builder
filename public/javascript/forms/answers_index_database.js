document.addEventListener('DOMContentLoaded', function () {
    window.tigress = window.tigress || {};

    const allTranslations = {
        nl: {
            id: 'Id',
            created: 'Datum',
            name: 'Ingediend door',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Acties',
            view: 'Bekijk',
        },
        fr: {
            id: 'Id',
            created: 'Date',
            name: 'Soumis par',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Actions',
            view: 'Voir',
        },
        de: {
            id: 'Id',
            created: 'Datum',
            name: 'Eingereicht von',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Aktionen',
            view: 'Ansehen',
        },
        es: {
            id: 'Id',
            created: 'Fecha',
            name: 'Enviado por',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Acciones',
            view: 'Ver',
        },
        it: {
            id: 'Id',
            created: 'Data',
            name: 'Inviato da',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Azioni',
            view: 'Visualizza',
        },
        en: {
            id: 'Id',
            created: 'Date',
            name: 'Submitted by',
            unknown: '<i class="fa fa-question"></i>',
            actions: 'Actions',
            view: 'View',
        }
    }

    const translations = allTranslations[tigress.shortLang] || allTranslations['en'];

    const tableAnswers = new DataTable('#dataTableAnswersDatabase', {
        processing: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'Alle']
        ],
        responsive: true,
        stateSave: true,
        order: [[1, 'desc']],
        language: tigress.languageOption,
    });

    // Tooltip initialiseren bij elke redraw
    tableAnswers.on('draw', function () {
        initTooltips();
    });
});
