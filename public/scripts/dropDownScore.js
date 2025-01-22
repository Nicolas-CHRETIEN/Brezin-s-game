let scoreTrigger = document.querySelector(".scoreNormal");

const onClickDeclarations = () => {
    const scoreDeclarations = document.querySelector(".scoreNormalDeclarations");

    if (scoreDeclarations.classList.contains("score-normal_declarations-hidden")) {
        scoreDeclarations.classList.remove("score-normal_declarations-hidden");
    } else {
        scoreDeclarations.classList.add("score-normal_declarations-hidden")
    }
}

scoreTrigger.addEventListener("click", onClickDeclarations);