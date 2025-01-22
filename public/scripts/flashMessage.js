// Put the flash message in an alert to be sure player see it.

if (document.querySelector(".flashAlert")) {
    const message = document.querySelector(".flashAlert");
    alert(message.textContent);
}