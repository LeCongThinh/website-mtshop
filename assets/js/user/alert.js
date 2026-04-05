function showAlert(id, message, type = "success", time = 5000) {

    const alertBox = document.getElementById(id);
    if (!alertBox) return;

    const alertText = alertBox.querySelector(".alert-text");

    alertBox.classList.remove("d-none", "alert-success", "alert-danger");
    alertBox.classList.add("alert-" + type, "show");

    if (alertText) {
        alertText.innerText = message;
    }

    autoHideAlert(alertBox, time);
}


function autoHideAlert(alertBox, time = 5000) {

    setTimeout(() => {

        alertBox.style.transition = "opacity 0.5s ease";
        alertBox.style.opacity = "0";

        setTimeout(() => {
            alertBox.classList.add("d-none");
            alertBox.style.opacity = "1";
        }, 500);

    }, time);
}