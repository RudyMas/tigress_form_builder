document.addEventListener('DOMContentLoaded', function () {
    window.tigress = window.tigress || {};

    window.tigress.loadTranslations(language.translations)
        .then(function () {

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
                        title: __('ID'),
                        data: 'id',
                        className: 'text-middle'
                    },
                    {
                        title: __('Form'),
                        data: 'name',
                        className: 'text-nowrap text-middle',
                        width: '99%',
                        render: function (data, type, row) {
                            if (row.db_table) {
                                return '<i class="fa-solid fa-database"></i> ' + data;
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        title: __('Device'),
                        data: 'type_id',
                        className: 'text-center text-middle',
                        render: function (data) {
                            return data === 1 ? '<i class="fa fa-mobile-alt"></i>' : data === 2 ? '<i class="fa fa-desktop"></i>' : '<i class="fa fa-question"></i>';
                        }
                    },
                    {
                        title: __('Link'),
                        data: 'form_reference',
                        className: 'text-middle',
                        render: function (data) {
                            return data ? `<a href="/form/${data}" target="_blank">https://gunax.go-next.be/form/${data}</a>` : '';
                        }
                    },
                    {
                        title: __('Actions'),
                        data: null,
                        className: 'text-nowrap text-center text-middle',
                        render: function (data, type, row) {
                            let output = '';
                            output += ` <a data-bs-toggle="tooltip" title="${__('QR code')}" href="/forms/qr/${row.id}" class="btn btn-sm btn-secondary"><i class="fa fa-qrcode"></i></a>`;
                            if (variables.read) {
                                output += ` <a data-bs-toggle="tooltip" title="${__('View')}" href="/forms/${row.id}/answers/" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`;
                            }
                            if (variables.write && variables.show !== 'archive') {
                                output += ` <a data-bs-toggle="tooltip" title="${__('Question List')}" href="/forms/questions/${row.id}" class="btn btn-sm btn-warning"><i class="fa-solid fa-clipboard-question"></i></a>`;
                                output += ` <a data-bs-toggle="tooltip" title="${__('Edit')}" href="/forms/edit/${row.id}" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></a>`;
                                output += ` <button title="${__('Duplicate')}" type="button" class="btn btn-sm btn-primary open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsDuplicate" data-id="${row.id}"><i class="fa-solid fa-clone" aria-hidden="true"></i></button>`;
                            }
                            if (variables.delete) {
                                if (variables.show === 'archive') {
                                    output += ` <button title="${__('Restore')}" type="button" class="btn btn-sm btn-success open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsRestore" data-id="${row.id}"><i class="fa fa-undo" aria-hidden="true"></i></button>`;
                                } else {
                                    output += ` <button title="${__('Archiving')}" type="button" class="btn btn-sm btn-danger open-modal" data-bs-toggle="modal" data-bs-target="#ModalFormsDelete" data-id="${row.id}"><i class="fa fa-archive" aria-hidden="true"></i></button>`;
                                }
                            }
                            return output;
                        }
                    }
                ],
                stateSave: true,
                order: [[0, 'desc']],
                language: tigress.languageOption,
                drawCallback: function () {
                    initTooltips();
                }
            });
        })

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

    const modalDuplicate = document.getElementById('ModalFormsDuplicate');
    modalDuplicate.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        modalDuplicate.querySelector('[name="form_id"]').value = button.getAttribute('data-id');
    });
});
