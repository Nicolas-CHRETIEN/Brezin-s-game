const stackDeclareButton = document.querySelectorAll(".stackDeclareButton");
const stackDeclarationList = document.querySelectorAll(".stackDeclarationList");
stackDeclareButton.forEach((button) => {
    button.addEventListener("click", () => {
        stackDeclarationList.forEach(list => {
            list.classList.remove("stack_declaration-list_hidden");
            list.classList.add("stack_declaration-list_visible");
        });
        button.classList.remove("stack_declare-button_visible");
        button.classList.add("stack_declare-button_hidden");
    }); 
});