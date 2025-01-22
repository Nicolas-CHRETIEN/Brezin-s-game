const showDeclarationsButtonPlayer = document.querySelector(".endRoundDeclarationsPlayer");
const stackDeclarationListPlayer = document.querySelector(".endRoundDeclarationsListPlayer");
const showDeclarationsButtonComputer = document.querySelector(".endRoundDeclarationsComputer");
const stackDeclarationListComputer = document.querySelector(".endRoundDeclarationsListComputer");

showDeclarationsButtonPlayer.addEventListener("click", () => {
    if (stackDeclarationListPlayer.classList.contains("end-round_declarations-list_hidden")) {
        stackDeclarationListPlayer.classList.remove("end-round_declarations-list_hidden");
        stackDeclarationListPlayer.classList.add("end-round_declarations-list_visible");
    } else {
        stackDeclarationListPlayer.classList.remove("end-round_declarations-list_visible");
        stackDeclarationListPlayer.classList.add("end-round_declarations-list_hidden");
    }
});

showDeclarationsButtonComputer.addEventListener("click", () => {
    if (stackDeclarationListComputer.classList.contains("end-round_declarations-list_hidden")) {
        stackDeclarationListComputer.classList.remove("end-round_declarations-list_hidden");
        stackDeclarationListComputer.classList.add("end-round_declarations-list_visible");
    } else {
        stackDeclarationListComputer.classList.remove("end-round_declarations-list_visible");
        stackDeclarationListComputer.classList.add("end-round_declarations-list_hidden");
    }
});