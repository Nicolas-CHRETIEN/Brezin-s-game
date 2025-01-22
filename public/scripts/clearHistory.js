// Classes stopHistoryRegister have been added to all links toward a game's render.
// This function stop writting in the browser's history.
// Thank's to this, if the user click on previous button, he will be re directed to the last page he was before starting playing.
// As game's information are registered in DB, he can go back to the game easily by clicking on "next" or "game". He will return to the game'stage he was.
// This is done to prevent any bug in the game.

const links = document.querySelectorAll(".stopHistoryRegister");

links.forEach(link => {
    const path = link.href;

    function location () {
        document.location.replace(path); // location.replace() do not write the location in the history.
    }

    link.addEventListener("click", location);
    link.href = "javascript: void(0)"; // "javascript: void(0)" disable the link effect.
});