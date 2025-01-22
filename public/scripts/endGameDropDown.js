const showDeclarationsButtonPlayer = document.querySelector(".scorePartDeclarationsPlayer");
const stackDeclarationListPlayer = document.querySelector(".scorePartDeclarationsListPlayer");
const showDeclarationsButtonComputer = document.querySelector(".scorePartDeclarationsComputer");
const stackDeclarationListComputer = document.querySelector(".scorePartDeclarationsListComputer");

showDeclarationsButtonPlayer.addEventListener("click", () => {
    if (stackDeclarationListPlayer.classList.contains("score-part__declarations-list_hidden")) {
        stackDeclarationListPlayer.classList.remove("score-part__declarations-list_hidden");
        stackDeclarationListPlayer.classList.add("score-part__declarations-list_visible");
    } else {
        stackDeclarationListPlayer.classList.remove("score-part__declarations-list_visible");
        stackDeclarationListPlayer.classList.add("score-part__declarations-list_hidden");
    }
});

showDeclarationsButtonComputer.addEventListener("click", () => {
    if (stackDeclarationListComputer.classList.contains("score-part__declarations-list_hidden")) {
        stackDeclarationListComputer.classList.remove("score-part__declarations-list_hidden");
        stackDeclarationListComputer.classList.add("score-part__declarations-list_visible");
    } else {
        stackDeclarationListComputer.classList.remove("score-part__declarations-list_visible");
        stackDeclarationListComputer.classList.add("score-part__declarations-list_hidden");
    }
});