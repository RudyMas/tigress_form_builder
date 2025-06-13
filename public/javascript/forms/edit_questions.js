document.addEventListener('DOMContentLoaded', function () {
    // Helpers
    function $(selector, context) {
        return (context || document).querySelector(selector);
    }

    function $all(selector, context) {
        return Array.from((context || document).querySelectorAll(selector));
    }

    // Taaldetectie en foutboodschap
    const htmlLang = document.documentElement.lang;
    const lang = htmlLang.substring(0, 2);
    const errorTranslations = {
        nl: 'Gelieve alle velden in te vullen.',
        fr: 'Veuillez remplir tous les champs.',
        de: 'Bitte füllen Sie alle Felder aus.',
        en: 'Please fill in all fields.'
    };
    const errorMessageText = errorTranslations[lang] || errorTranslations.en;

    // Validatie voor hoofdformulier
    $all('.submitFrm').forEach(btn => {
        btn.addEventListener('click', function () {
            let isValid = true;

            // Input & textarea validatie
            $all('input[required][form="form-all"], textarea[required][form="form-all"]').forEach(el => {
                if (!el.value || el.value.trim() === '') {
                    isValid = false;
                    el.style.border = "1px solid red";
                    el.style.background = "#FFCECE";
                } else {
                    el.style.border = "";
                    el.style.background = "";
                }
            });

            // Select + Select2 validatie
            $all('select[required][form="form-all"]').forEach(el => {
                const s2 = el.nextElementSibling?.classList?.contains('select2-container') ? el.nextElementSibling : null;
                if (!el.value || el.value.trim() === '') {
                    isValid = false;
                    if (s2) {
                        const s2sel = s2.querySelector('.select2-selection');
                        if (s2sel) {
                            s2sel.style.border = "1px solid red";
                            s2sel.style.background = "#FFCECE";
                        }
                    }
                } else if (s2) {
                    const s2sel = s2.querySelector('.select2-selection');
                    if (s2sel) {
                        s2sel.style.border = "";
                        s2sel.style.background = "";
                    }
                }
            });

            const errorMsg = $("#error-message");
            if (!isValid) {
                if (errorMsg) {
                    errorMsg.style.display = "block";
                    errorMsg.innerHTML = errorMessageText;
                }
                return false;
            } else {
                if (errorMsg) errorMsg.style.display = "none";
                const confirmModal = new bootstrap.Modal($("#confirm-submit"));
                confirmModal.show();
            }
        });
    });

    // "Alles verzenden" knop
    $('#submit-all')?.addEventListener('click', () => {
        $('#form-all')?.submit();
    });

    // Dynamische validatie voor vragen/secties
    function addModalValidation(triggerSel, formSel, modalSel, confirmBtnSel, deleteBtnSel, deleteModalSel, confirmDeleteBtnSel) {
        // Toevoegen
        $all(triggerSel).forEach(trigger => {
            trigger.addEventListener('click', e => {
                e.preventDefault();
                const count = trigger.dataset.count;
                const form = count ? $(`#${formSel}-${count}`) : $(`#${formSel}`);
                let valid = true;

                // Reset validatie
                $all('.is-invalid', form).forEach(x => x.classList.remove('is-invalid'));

                // Controleer vereiste velden
                $all('[required]', form).forEach(el => {
                    if (!el.value || !el.value.trim()) {
                        el.classList.add('is-invalid');
                        valid = false;
                    }
                });

                // Toon modal indien geldig
                if (valid) {
                    if (confirmBtnSel && count !== undefined) {
                        const confirmBtn = $(confirmBtnSel);
                        if (confirmBtn) confirmBtn.dataset.count = count;
                    }
                    const modal = new bootstrap.Modal($(modalSel));
                    modal.show();
                }
            });
        });

        // Bevestiging van toevoegen
        const confirmBtn = $(confirmBtnSel);
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                const count = confirmBtn.dataset.count;
                const form = count ? $(`#${formSel}-${count}`) : $(`#${formSel}`);
                if (form) form.submit();
            });
        }

        // Verwijderen
        $all(deleteBtnSel).forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const url = btn.dataset.url;
                const confBtn = $(confirmDeleteBtnSel);
                if (confBtn) confBtn.setAttribute('href', url);
                const delModal = new bootstrap.Modal($(deleteModalSel));
                delModal.show();
            });
        });
    }

    // Gebruik voor vragen
    addModalValidation(
        '.trigger-add-question-modal', 'form-table1', '#confirm-add-question-modal',
        '#confirm-add-question-btn', '.btn-delete-question', '#confirm-delete-question-modal', '#confirm-delete-question-btn'
    );

    // Verwijderen van sectie
    $all('.btn-delete-section').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const url = btn.dataset.url;
            const confBtn = $('#confirm-delete-section-btn');
            if (confBtn) confBtn.setAttribute('href', url);
            const modal = new bootstrap.Modal($('#confirm-delete-section-modal'));
            modal.show();
        });
    });

    // Uitschakelen van bewerkopties zodra iets in 'form-all' wijzigt
    $all('[form="form-all"]').forEach(input => {
        input.addEventListener('change', () => {
            const selectors = [
                '[form^="form-table1"]', '.trigger-add-question-modal',
                '.btn-delete-question'
            ];
            for (let i = 0; i < selectors.length; i += 2) {
                $all(selectors[i]).forEach(x => x.disabled = true);
                $all(selectors[i + 1]).forEach(x => x.classList.add('disabled'));
            }
        });
    });
});
