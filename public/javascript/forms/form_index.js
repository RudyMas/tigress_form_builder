document.addEventListener('DOMContentLoaded', function () {
    // Helpers
    function $(selector, context) {
        return (context || document).querySelector(selector);
    }
    function $all(selector, context) {
        return Array.from((context || document).querySelectorAll(selector));
    }

    // Language handling
    const htmlLang = document.documentElement.lang;
    const lang = htmlLang.substring(0, 2);
    const errorTranslations = {
        nl: 'Gelieve alle velden in te vullen. Bekijk ook de vorige stappen, indien van toepassing.',
        fr: 'Veuillez remplir tous les champs. Vérifiez également les étapes précédentes, le cas échéant.',
        en: 'Please fill in all fields. Also check previous steps, if applicable.'
    };
    const errorMessageText = errorTranslations[lang] || errorTranslations.en;

    // Step navigation
    const setStep = step => {
        $all(".step-content").forEach(el => el.style.display = "none");
        $(`[data-step='${step}']`).style.display = "block";
        $all(".steps .step").forEach((el, index) => {
            el.classList.toggle("complete", index < step - 1);
            el.classList.toggle("current", index === step - 1);
        });
    };

    $all("[data-set-step]").forEach(el => {
        el.addEventListener("click", e => {
            e.preventDefault();
            setStep(parseInt(el.dataset.setStep));
        });
    });

    // Form validation on submit
    $("#submitFrm")?.addEventListener("click", function (e) {
        let isValid = true;

        // Validate input & textarea
        $all("input[required], textarea[required]").forEach(el => {
            if (!el.value || !el.value.trim() || !el.checkValidity()) {
                isValid = false;
                el.style.border = "1px solid red";
                el.style.background = "#FFCECE";
            } else {
                el.style.border = "";
                el.style.background = "";
            }
        });

        // Validate select
        $all("select[required]").forEach(el => {
            let value = el.value;
            let s2 = el.nextElementSibling?.classList?.contains('select2-container') ? el.nextElementSibling : null;

            if (!value || value.trim() === '' || value === "-1") {
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

        // Show or hide error message
        const errorMsg = $("#error-message");
        if (!isValid) {
            if (errorMsg) {
                errorMsg.style.display = "block";
                errorMsg.innerHTML = errorMessageText;
            }
            e.preventDefault();
            return false;
        } else {
            if (errorMsg) errorMsg.style.display = "none";
            const modal = new bootstrap.Modal($("#confirm-submit"));
            modal.show();
        }
    });

    // Reset style on input/textarea change
    $all("input[required], textarea[required]").forEach(el => {
        el.addEventListener("input", () => {
            el.style.border = "";
            el.style.background = "";
            const errorMsg = $("#error-message");
            if (errorMsg) errorMsg.style.display = "none";
        });
    });

    // Submit form after confirmation
    $("#submit")?.addEventListener("click", () => {
        $("#form")?.submit();
    });
});
