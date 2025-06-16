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

    const tableAnswers = new DataTable('#dataTableAnswers', {
        processing: true,
        ajax: {
            url: `/forms/${variables.formId}/answers/get`,
            dataType: 'json'
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'Alle']
        ],
        responsive: true,
        columns: [
            {
                title: translations.id,
                data: 'uniq_code',
                width: '99%',
            },
            {
                title: translations.created,
                data: 'created',
                className: 'text-nowrap text-middle',
            },
            {
                title: translations.name,
                data: 'last_name',
                className: 'text-center text-middle text-nowrap',
                render: function (data, type, row) {
                    if (row.created_user_id > 0) {
                        return `${data} ${row.first_name}`;
                    } else {
                        return `<span class="text-muted">${translations.unknown}</span>`;
                    }
                }
            },
            {
                title: translations.actions,
                data: null,
                className: 'text-nowrap text-center text-middle',
                render: function (data, type, row) {
                    let output = '';
                    if (variables.read) {
                        output += ` <a data-bs-toggle="tooltip" title="${translations.view}" href="/forms/${row.id}/answers/view/${row.uniq_code}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`;
                    }
                    return output;
                }
            }
        ],
        columnDefs: [
            {
                targets: [1],
                type: 'datetime-moment',
                render: function (data, type) {
                    if (!data) {
                        return '';
                    }
                    return type === 'display'
                        ? moment(data, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY, HH:mm')
                        : moment(data, 'YYYY-MM-DD HH:mm:ss').valueOf();
                }
            }
        ],
        stateSave: true,
        order: [[1, 'desc']],
        language: tigress.languageOption,
    });

    // Tooltip initialiseren bij elke redraw
    tableAnswers.on('draw', function () {
        initTooltips();
    });
});
