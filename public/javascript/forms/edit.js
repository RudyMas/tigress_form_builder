document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("external_access");
    const target = document.getElementById("show_hide_external");

    function toggleExternal() {
        if (checkbox.checked) {
            target.style.display = "block";
        } else {
            target.style.display = "none";
        }
    }

    // Run on load (in case checkbox is pre-checked)
    toggleExternal();

    // Run when checkbox changes
    checkbox.addEventListener("change", toggleExternal);
});
