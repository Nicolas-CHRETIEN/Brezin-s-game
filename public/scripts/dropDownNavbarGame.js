// Manage the drop down elements which are hidden by default. 
// If screen is small enought, there are three. They can't be opened at the same time.

const cardsDeclaredComputerTrigger = document.querySelectorAll(".ComputerCardsDeclarations");
const trickListTrigger = document.querySelectorAll(".LastTrick");
const burgerTrigger = document.querySelectorAll(".burger");
const cardsDeclaredComputer = document.querySelector(".cardsDeclaredComputerNarrowScreen");
const trickList = document.querySelector(".trickList");
const menuBurger = document.querySelector(".menuBurger");


const activeCardsDeclaredComputer = () => {
    cardsDeclaredComputer.classList.remove("cards-declared-computer-narrow-screen-hidden");
    cardsDeclaredComputer.classList.add("cards-declared-computer-visible");
    cardsDeclaredComputerTrigger.forEach(element => element.classList.add("cards-declared-computer-active"));
}

const disableCardsDeclaredComputer = () => {
    cardsDeclaredComputer.classList.remove("cards-declared-computer-visible");
    cardsDeclaredComputerTrigger.forEach(element => element.classList.remove("cards-declared-computer-active"));
    cardsDeclaredComputer.classList.add("cards-declared-computer-narrow-screen-hidden");
}

const activeLastTrick = () => {
    trickList.classList.remove("trick-list-hidden");
    trickList.classList.add("trick-list-visible");
    trickListTrigger.forEach(element => element.classList.add("trick-list-active"));
}

const disableLastTrick = () => {
    trickList.classList.remove("trick-list-visible");
    trickList.classList.add("trick-list-hidden");
    trickListTrigger.forEach(element => element.classList.remove("trick-list-active"));
}

const activeMenuBurger = () => {
    menuBurger.classList.remove("menu-burger-hidden");
    menuBurger.classList.add("menu-burger-visible");
    burgerTrigger.forEach(element => element.classList.add("burger-active"));
}

const disableMenuBurger = () => {
    menuBurger.classList.remove("menu-burger-visible");
    menuBurger.classList.add("menu-burger-hidden");
    burgerTrigger.forEach(element => element.classList.remove("burger-active"));
}


const onClickCardsDeclaredComputer = () => {
    if (cardsDeclaredComputer.classList.contains("cards-declared-computer-narrow-screen-hidden")) {
        activeCardsDeclaredComputer();
        if (trickList.classList.contains("trick-list-visible")) {
            disableLastTrick();
        }
        if (menuBurger.classList.contains("menu-burger-visible")) {
            disableMenuBurger();
        }
    } else {
        disableCardsDeclaredComputer();
    }
}

const onClickLastTrick = () => {
    if (trickList.classList.contains("trick-list-hidden")) {
        activeLastTrick();
        if (cardsDeclaredComputer.classList.contains("cards-declared-computer-visible")) {
            disableCardsDeclaredComputer();
        }
        if (menuBurger.classList.contains("menu-burger-visible")) {
            disableMenuBurger();
        }
    } else {
        disableLastTrick();
    }
}

const onClickMenuBurger = () => {
    if (menuBurger.classList.contains("menu-burger-hidden")) {
        activeMenuBurger();
        if (cardsDeclaredComputer.classList.contains("cards-declared-computer-visible")) {
            disableCardsDeclaredComputer();
        }
        if (trickList.classList.contains("trick-list-visible")) {
            disableLastTrick();
        }
    } else {
        disableMenuBurger();
    }
}


cardsDeclaredComputerTrigger.forEach(element => element.addEventListener("click", onClickCardsDeclaredComputer));
trickListTrigger.forEach(element => element.addEventListener("click", onClickLastTrick));
burgerTrigger.forEach(element => element.addEventListener("click", onClickMenuBurger));