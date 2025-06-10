document.addEventListener('DOMContentLoaded', function () {
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
                title: 'Id',
                data: 'id',
                className: 'text-middle'
            },
            {
                title: 'Form',
                data: 'name',
                className: 'text-nowrap text-middle',
                width: '99%'
            },
            {
                title: 'Device',
                data: 'type',
                className: 'text-center text-middle',
                render: function (data, type, row) {
                    if (data === 1) {
                        return '<i class="fa fa-mobile-alt"></i>';
                    } else if (data === 2) {
                        return '<i class="fa fa-desktop"></i>';
                    }
                    return '<i class="fa fa-question"></i>';
                }
            },
            {
                title: 'Link',
                data: 'form_reference',
                className: 'text-middle',
                render: function (data, type, row) {
                    if (data) {
                        return `<a href="/form/${data}" target="_blank">https://gunax.go-next.be/form/${data}</a>`;
                    }
                    return '';
                }
            },
            {
                title: 'Actions',
                data: null,
                className: 'text-nowrap text-center text-middle',
                render: function (data, type, row) {
                    let output = '';
                    output += ` <a data-bs-toggle="tooltip" title="QR-code" href="/forms/qr/${row.id}" class="btn btn-sm btn-secondary"><i class="fa fa-qrcode"></i></a>`;
                    if (variables.read) {
                        output += ` <a data-bs-toggle="tooltip" title="View" href="/forms/view/${row.id}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`;
                    }
                    if (variables.write && variables.show !== 'archive') {
                        output += ` <a data-bs-toggle="tooltip" title="Questionlist" href="/forms/questions/${row.id}" class="btn btn-sm btn-warning"><i class="fa-solid fa-clipboard-question"></i></a>`;
                        output += ` <a data-bs-toggle="tooltip" title="Edit" href="/forms/edit/${row.id}" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></a>`;
                    }
                    if (variables.delete) {
                        if (variables.show === 'archive') {
                            output += ` <button title="Repair" type="button" class="btn btn-sm btn-success open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsRepair" data-id="${row.id}"><i class="fa fa-undo" aria-hidden="true"></i></button>`;
                        } else {
                            output += ` <button title="Archive" type="button" class="btn btn-sm btn-danger open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsDelete" data-id="${row.id}"><i class="fa fa-archive" aria-hidden="true"></i></button>`;
                        }
                    }
                    return output;
                }
            }
        ],
        stateSave: true,
        order: [[0, 'desc']],
        language: {
            url: '/node_modules/datatables.net-plugins/i18n/nl-NL.json'
        },
    });

    // Tooltip initialiseren bij elke redraw
    tableForms.on('draw', function () {
        initTooltips();
    });

    // Modal vullen
    const modalDelete = document.getElementById('ModalFormDelete');
    modalDelete.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        modalDelete.querySelector('#DeleteForm').value = button.getAttribute('data-id');
    });
});
