const trickDesktop = document.querySelector(".LastTrickDesktop");
const trickListDesktop = document.querySelector(".trickListDesktop");

const showLastTrick = () => {
    console.log('works');
    if (trickListDesktop.classList.contains("trick-list-hidden")) {
        trickListDesktop.classList.remove("trick-list-hidden");
        trickListDesktop.classList.add("trick-list-visible");
    } else {
        trickListDesktop.classList.remove("trick-list-visible");
        trickListDesktop.classList.add("trick-list-hidden");
    }
}

trickDesktop.addEventListener("click", showLastTrick);