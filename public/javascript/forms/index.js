document.addEventListener('DOMContentLoaded', function () {
    const htmlLang = document.documentElement.lang.toLowerCase();
    const shortLang = htmlLang.substring(0, 2);

    const languageFiles = {
        nl: '/node_modules/datatables.net-plugins/i18n/nl-NL.json',
        fr: '/node_modules/datatables.net-plugins/i18n/fr-FR.json',
        // no entry for en-US → use default
        // en: '/node_modules/datatables.net-plugins/i18n/en-GB.json',
    };

    const languageOption = languageFiles[shortLang] ? {url: languageFiles[shortLang]} : {};

    const translations = {
        nl: {
            id: 'Id',
            form: 'Formulier',
            device: 'Toestel',
            link: 'Link',
            actions: 'Acties',
            qr: 'QR-code',
            view: 'Bekijk',
            questionlist: 'Vragenlijst',
            edit: 'Bewerk',
            archive: 'Archiveren',
            restore: 'Herstellen',
            desktop: '<i class="fa fa-desktop"></i>',
            mobile: '<i class="fa fa-mobile-alt"></i>',
            unknown: '<i class="fa fa-question"></i>',
            makeChoice: 'Maak je keuze',
        },
        fr: {
            id: 'Id',
            form: 'Formulaire',
            device: 'Appareil',
            link: 'Lien',
            actions: 'Actions',
            qr: 'Code QR',
            view: 'Voir',
            questionlist: 'Liste de questions',
            edit: 'Éditer',
            archive: 'Archiver',
            restore: 'Restaurer',
            desktop: '<i class="fa fa-desktop"></i>',
            mobile: '<i class="fa fa-mobile-alt"></i>',
            unknown: '<i class="fa fa-question"></i>',
            makeChoice: 'Faites votre choix',
        },
        en: {
            id: 'Id',
            form: 'Form',
            device: 'Device',
            link: 'Link',
            actions: 'Actions',
            qr: 'QR-code',
            view: 'View',
            questionlist: 'Questionlist',
            edit: 'Edit',
            archive: 'Archive',
            restore: 'Restore',
            desktop: '<i class="fa fa-desktop"></i>',
            mobile: '<i class="fa fa-mobile-alt"></i>',
            unknown: '<i class="fa fa-question"></i>',
            makeChoice: 'Make your choice',
        }
    }[shortLang] || translations['en']; // fallback naar Engels

    let url = '/forms/get/active';
    if (variables.show === 'archive') {
        url = '/forms/get/inactive';
    }

    const tableForms = new DataTable('#dataTableForms', {
        processing: true,
        ajax: {
            url: url,
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
                data: 'id',
                className: 'text-middle'
            },
            {
                title: translations.form,
                data: 'name',
                className: 'text-nowrap text-middle',
                width: '99%'
            },
            {
                title: translations.device,
                data: 'type_id',
                className: 'text-center text-middle',
                render: function (data) {
                    return data === 1 ? translations.mobile : data === 2 ? translations.desktop : translations.unknown;
                }
            },
            {
                title: translations.link,
                data: 'form_reference',
                className: 'text-middle',
                render: function (data) {
                    return data ? `<a href="/form/${data}" target="_blank">https://gunax.go-next.be/form/${data}</a>` : '';
                }
            },
            {
                title: translations.actions,
                data: null,
                className: 'text-nowrap text-center text-middle',
                render: function (data, type, row) {
                    let output = '';
                    output += ` <a data-bs-toggle="tooltip" title="${translations.qr}" href="/forms/qr/${row.id}" class="btn btn-sm btn-secondary"><i class="fa fa-qrcode"></i></a>`;
                    if (variables.read) {
                        output += ` <a data-bs-toggle="tooltip" title="${translations.view}" href="/forms/view/${row.id}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`;
                    }
                    if (variables.write && variables.show !== 'archive') {
                        output += ` <a data-bs-toggle="tooltip" title="${translations.questionlist}" href="/forms/questions/${row.id}" class="btn btn-sm btn-warning"><i class="fa-solid fa-clipboard-question"></i></a>`;
                        output += ` <a data-bs-toggle="tooltip" title="${translations.edit}" href="/forms/edit/${row.id}" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></a>`;
                    }
                    if (variables.delete) {
                        if (variables.show === 'archive') {
                            output += ` <button title="${translations.restore}" type="button" class="btn btn-sm btn-success open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsRestore" data-id="${row.id}"><i class="fa fa-undo" aria-hidden="true"></i></button>`;
                        } else {
                            output += ` <button title="${translations.archive}" type="button" class="btn btn-sm btn-danger open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsDelete" data-id="${row.id}"><i class="fa fa-archive" aria-hidden="true"></i></button>`;
                        }
                    }
                    return output;
                }
            }
        ],
        stateSave: true,
        order: [[0, 'desc']],
        language: languageOption,
    });

    // Tooltip initialiseren bij elke redraw
    tableForms.on('draw', function () {
        initTooltips();
    });

    const modalDelete = document.getElementById('ModalFormsDelete');
    modalDelete.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        modalDelete.querySelector('#DeleteForm').value = button.getAttribute('data-id');
    });

    const modalRestore = document.getElementById('ModalFormsRestore');
    modalRestore.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        modalRestore.querySelector('#RestoreForm').value = button.getAttribute('data-id');
    });
});
