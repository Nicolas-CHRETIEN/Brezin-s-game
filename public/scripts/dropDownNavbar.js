// Manage the drop down elements which are hidden by default. 
// Anywhere else than in game's pages, there is only the menu.

const burgerTrigger = document.querySelectorAll(".burger");
const menuBurger = document.querySelector(".menuBurger");

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

const onClickMenuBurger = () => {
    if (menuBurger.classList.contains("menu-burger-hidden")) {
        activeMenuBurger();
    } else {
        disableMenuBurger();
    }
}

burgerTrigger.forEach(element => element.addEventListener("click", onClickMenuBurger));