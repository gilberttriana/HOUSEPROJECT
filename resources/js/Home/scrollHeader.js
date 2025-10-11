window.addEventListener("scroll", function() {
    const header = document.querySelector("header");
    const heroHeight = document.querySelector(".hero")?.offsetHeight || 0;

    if (window.scrollY > 0 && window.scrollY < heroHeight - 60) {
        header?.classList.add("hide");
        header?.classList.remove("scrolled");
    } else if (window.scrollY >= heroHeight - 60) {
        header?.classList.remove("hide");
        header?.classList.add("scrolled");
    } else {
        header?.classList.remove("hide");
        header?.classList.remove("scrolled");
    }
});
