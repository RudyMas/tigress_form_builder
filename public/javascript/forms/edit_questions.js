document.addEventListener('DOMContentLoaded', function () {
    window.tigress = window.tigress || {};

    window.tigress.loadTranslations(language.translations).then(function () {

        // Helpers
        function $(selector, context) {
            return (context || document).querySelector(selector);
        }

        function $all(selector, context) {
            return Array.from((context || document).querySelectorAll(selector));
        }

        // One editor is shared by all question fields. Cancel leaves the source untouched.
        const editorModalElement = $('#edit-question-modal');
        const editorModal = new bootstrap.Modal(editorModalElement, {focus: false});
        const applyQuestion = $('#apply-question-text');
        const editorError = $('#question-editor-error');
        let questionSource = null;
        let questionEditor = null;
        let editorReady = null;
        let applyingQuestion = false;

        function showEditorError(message) {
            editorError.textContent = message;
            editorError.hidden = false;
        }

        $all('.edit-question-text').forEach(button => {
            button.addEventListener('click', () => {
                const source = button.closest('.question-editor-field').querySelector('textarea');
                if (source.disabled || source.readOnly) return;
                questionSource = source;
                editorModal.show();
            });
        });

        editorModalElement.addEventListener('shown.bs.modal', async () => {
            editorError.hidden = true;
            applyQuestion.disabled = true;
            try {
                if (!window.tinymce) throw new Error(__('The text editor could not be loaded.'));
                if (!editorReady) {
                    const locale = window.tigress.languageTinymce;
                    editorReady = tinymce.init({
                        target: $('#question-rich-text'),
                        license_key: 'gpl',
                        height: 400,
                        menubar: false,
                        plugins: 'lists image code',
                        toolbar: 'undo redo | bullist numlist bold italic underline | image | removeformat code',
                        // Match the image-safe HTML profile used to render question labels.
                        valid_elements: 'div,ul,ol,li,b,strong,i,em,u,br,img[src|alt|width|height]',
                        formats: {bold: {inline: 'b'}, italic: {inline: 'i'}},
                        forced_root_block: 'div',
                        content_style: 'img { max-width: 100%; height: auto; }',
                        // Store same-site image URLs from the site root, independent of the editor route.
                        relative_urls: false,
                        remove_script_host: true,
                        images_upload_url: variables.baseUrl + '/public/scripts/tinymce_upload_image.php?folder=forms',
                        // Open the image dialog without depending on the optional image-list endpoint.
                        // That endpoint may fail before the first upload creates the forms folder.
                        image_list: false,
                        images_file_types: 'jpg,jpeg,png',
                        automatic_uploads: true,
                        paste_data_images: true,
                        ...(locale ? {language: locale.lang, language_url: variables.baseUrl + locale.url} : {}),
                    }).then(editors => {
                        if (!editors.length) throw new Error(__('The text editor could not be loaded.'));
                        questionEditor = editors[0];
                        return questionEditor;
                    }).catch(error => {
                        editorReady = null;
                        throw error;
                    });
                }
                const editor = await editorReady;
                if (!questionSource || !editorModalElement.classList.contains('show')) return;
                // Preserve line breaks in older questions that contain plain text.
                const content = questionSource.value.replace(/\r?\n/g, '<br>');
                editor.setContent(content);
                applyQuestion.disabled = false;
                editor.focus();
            } catch (error) {
                showEditorError(__('The text editor could not be loaded.'));
                console.error(error);
            }
        });

        applyQuestion.addEventListener('click', async () => {
            if (!questionEditor || !questionSource || applyingQuestion) return;
            applyingQuestion = true;
            applyQuestion.disabled = true;
            editorError.hidden = true;
            try {
                const uploads = await questionEditor.uploadImages();
                if (uploads.some(upload => upload.status === false)) {
                    throw new Error('Image upload failed');
                }
                questionSource.value = questionEditor.getContent()
                    .replace(/<div[^>]*>/g, '').replace(/<\/div>\s*(?=.)/g, '<br>').replace(/<\/div>\s*$/g, '');
                questionSource.dispatchEvent(new Event('input', {bubbles: true}));
                questionSource.dispatchEvent(new Event('change', {bubbles: true}));
                applyingQuestion = false;
                editorModal.hide();
            } catch (error) {
                showEditorError(__('The images could not be uploaded. Please try again.'));
                console.error(error);
            } finally {
                applyingQuestion = false;
                applyQuestion.disabled = false;
            }
        });

        editorModalElement.addEventListener('hide.bs.modal', event => {
            if (applyingQuestion) event.preventDefault();
        });
        editorModalElement.addEventListener('hidden.bs.modal', () => {
            questionSource = null;
        });

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
                        errorMsg.innerHTML = __('Please fill in all fields.');
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
                $all('[form^="form-table1"]').forEach(x => x.disabled = true);
                $all('.trigger-add-question-modal, .btn-delete-question').forEach(x => x.classList.add('disabled'));
                $all('form[id^="form-table1"] .edit-question-text').forEach(x => x.disabled = true);
            });
        });
    });
});
