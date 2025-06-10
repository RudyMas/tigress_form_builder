document.addEventListener('DOMContentLoaded', function () {

    // Helpers
    function $(selector, context) {
        return (context || document).querySelector(selector);
    }
    function $all(selector, context) {
        return Array.from((context || document).querySelectorAll(selector));
    }

    // -------- Formulier validatie en submit
    $all('.submitFrm').forEach(function(btn) {
        btn.addEventListener('click', function () {
            let isValid = true;

            // Inputs & textareas
            $all('input[required][form="form-all"], textarea[required][form="form-all"]').forEach(function(el) {
                let value = el.value;
                if (value === null || value.trim().length === 0) {
                    isValid = false;
                    el.style.border = "1px solid red";
                    el.style.background = "#FFCECE";
                } else {
                    el.style.border = "";
                    el.style.background = "";
                }
            });

            // Selects (ook styling van select2 containers)
            $all('select[required][form="form-all"]').forEach(function(el) {
                let value = el.value;
                let s2 = el.nextElementSibling?.classList?.contains('select2-container') ? el.nextElementSibling : null;
                if (value === null || value.trim().length === 0) {
                    isValid = false;
                    if (s2) {
                        let s2sel = s2.querySelector('.select2-selection');
                        if (s2sel) {
                            s2sel.style.border = "1px solid red";
                            s2sel.style.background = "#FFCECE";
                        }
                    }
                } else {
                    if (s2) {
                        let s2sel = s2.querySelector('.select2-selection');
                        if (s2sel) {
                            s2sel.style.border = "";
                            s2sel.style.background = "";
                        }
                    }
                }
            });

            let errorMsg = $(".error-message");
            if (!isValid) {
                if (errorMsg) {
                    errorMsg.style.display = "block";
                    // errorMsg.innerHTML = "Gelieve alle velden in te vullen.";
                    errorMsg.innerHTML = "Please fill in all required fields.";
                }
                return false;
            } else {
                if (errorMsg) errorMsg.style.display = "none";
                let confirmModal = new bootstrap.Modal($("#confirm-submit"));
                confirmModal.show();
            }
        });
    });

    // Form-all submit
    let submitAll = $('#submit-all');
    if (submitAll) submitAll.addEventListener('click', function () {
        $('#form-all').submit();
    });

    // ----------- Dynamisch: validatie voor toevoegen modals
    function addModalValidation(triggerSel, formSel, modalSel, confirmBtnSel, deleteBtnSel, deleteModalSel, confirmDeleteBtnSel) {
        // Toevoegen
        $all(triggerSel).forEach(function(trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                let count = trigger.dataset.count; // undefined als niet gebruikt
                let form = count ? $(`#${formSel}-${count}`) : $(`#${formSel}`);
                let valid = true;
                // Verwijder foutklassen
                $all('.is-invalid', form).forEach(x => x.classList.remove('is-invalid'));
                // Required check
                $all('[required]', form).forEach(function (el) {
                    if (!el.value || !el.value.trim()) {
                        el.classList.add('is-invalid');
                        valid = false;
                    }
                });
                // Toon modal indien geldig
                if (valid) {
                    if (confirmBtnSel && count !== undefined) {
                        // Bewaar count op de bevestigingsknop
                        let confirmBtn = $(confirmBtnSel);
                        if (confirmBtn) confirmBtn.dataset.count = count;
                    }
                    let addModal = new bootstrap.Modal($(modalSel));
                    addModal.show();
                }
            });
        });
        // Bevestig toevoegen
        let confirmBtn = $(confirmBtnSel);
        if (confirmBtn) confirmBtn.addEventListener('click', function () {
            let count = confirmBtn.dataset.count;
            let form = count ? $(`#${formSel}-${count}`) : $(`#${formSel}`);
            if (form) form.submit();
        });

        // Verwijder links
        $all(deleteBtnSel).forEach(function(btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                let url = btn.dataset.url;
                let confBtn = $(confirmDeleteBtnSel);
                if (confBtn) confBtn.setAttribute('href', url);
                let delModal = new bootstrap.Modal($(deleteModalSel));
                delModal.show();
            });
        });
    }

    // Vraag Toevoegen / Verwijderen
    addModalValidation(
        '.trigger-add-question-modal', 'form-table1', '#confirm-add-question-modal',
        '#confirm-add-question-btn', '.btn-delete-question', '#confirm-delete-question-modal', '#confirm-delete-question-btn'
    );

    // Extra sectie verwijderen (los, niet via addModalValidation)
    $all('.btn-delete-section').forEach(function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            let url = btn.dataset.url;
            let confBtn = $('#confirm-delete-section-btn');
            if (confBtn) confBtn.setAttribute('href', url);
            let modal = new bootstrap.Modal($('#confirm-delete-section-modal'));
            modal.show();
        });
    });

    // ----------- Bij wijzigen form="form-all": disable + buttons
    $all('[form="form-all"]').forEach(function(input) {
        input.addEventListener('change', function () {
            let selectors = [
                '[form^="form-table1"], .trigger-add-question-modal', '.btn-delete-question',
            ];
            for (let i = 0; i < selectors.length; i += 2) {
                $all(selectors[i]).forEach(x => x.disabled = true);
                $all(selectors[i+1]).forEach(x => x.classList.add('disabled'));
            }
        });
    });
});
