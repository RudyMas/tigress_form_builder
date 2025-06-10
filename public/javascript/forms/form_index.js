document.addEventListener('DOMContentLoaded', function () {
    // Initialiseer de eerste stap
    const setStep = step => {
        document.querySelectorAll(".step-content").forEach(element => element.style.display = "none");
        document.querySelector("[data-step='" + step + "']").style.display = "block";
        document.querySelectorAll(".steps .step").forEach((element, index) => {
            index < step - 1 ? element.classList.add("complete") : element.classList.remove("complete");
            index === step - 1 ? element.classList.add("current") : element.classList.remove("current");
        });
    };

    // Start met stap 1
    document.querySelectorAll("[data-set-step]").forEach(element => {
        element.onclick = event => {
            event.preventDefault();
            setStep(parseInt(element.dataset.setStep));
        };
    });

    // Controleer of alle verplichte velden zijn ingevuld
    document.getElementById("submitFrm").addEventListener("click", function (e) {
        let isValid = true;

        // Controleer input & textarea
        document.querySelectorAll("input[required], textarea[required]").forEach(function (el) {
            let value = el.value;
            if (value === null || value.trim().length === 0 || !el.checkValidity()) {
                isValid = false;
                el.style.border = "1px solid red";
                el.style.background = "#FFCECE";
            } else {
                el.style.border = "";
                el.style.background = "";
            }
        });

        // Controleer select
        document.querySelectorAll("select[required]").forEach(function (el) {
            let value = el.value;
            if (value === null || value.trim().length === 0 || value === "-1") {
                isValid = false;
                let select2 = el.nextElementSibling;
                if (select2 && select2.classList.contains('select2-container')) {
                    let selection = select2.querySelector('.select2-selection');
                    if (selection) {
                        selection.style.border = "1px solid red";
                        selection.style.background = "#FFCECE";
                    }
                }
            } else {
                let select2 = el.nextElementSibling;
                if (select2 && select2.classList.contains('select2-container')) {
                    let selection = select2.querySelector('.select2-selection');
                    if (selection) {
                        selection.style.border = "";
                        selection.style.background = "";
                    }
                }
            }
        });

        // Controleer of er een foutmelding is
        if (!isValid) {
            let errorMsg = document.getElementById("error-message");
            errorMsg.style.display = "block";
            // errorMsg.innerHTML = "Gelieve alle velden in te vullen. Bekijk ook de vorige stappen, indien van toepassing.";
            errorMsg.innerHTML = "Please fill in all fields. Also check previous steps, if applicable.";
            e.preventDefault();
            return false;
        } else {
            let modal = new bootstrap.Modal(document.getElementById('confirm-submit'));
            modal.show();
        }
    });


    // Verwijder foutmeldingen bij invoer
    document.querySelectorAll("input[required], textarea[required]").forEach(function (el) {
        el.addEventListener("input", function () {
            el.style.border = "";
            el.style.background = "";
            let errorMsg = document.getElementById("error-message");
            if (errorMsg) {
                errorMsg.style.display = "none";
            }
        });
    });

    // Submit de form na bevestiging
    document.getElementById("submit").addEventListener("click", function () {
        document.getElementById("form").submit();
    });
});