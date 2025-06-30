const headerToggle = document.getElementById("header-toggle");
const nav = document.getElementById("nav");
const navClose = document.getElementById("nav-close");
const main = document.getElementById("main");

if (headerToggle) {
  headerToggle.addEventListener("click", () => {
    nav.classList.toggle("show-menu");
    main.classList.toggle("shift-content");
  });
}

if (navClose) {
  navClose.addEventListener("click", () => {
    nav.classList.remove("show-menu");
    main.classList.remove("shift-content");
  });
}

const navLink = document.querySelectorAll(".nav-link");

function linkAction() {
  const nav = document.getElementById("nav");
  const main = document.getElementById("main");
  nav.classList.remove("show-menu");
  main.classList.remove("shift-content");
}
navLink.forEach((n) => n.addEventListener("click", linkAction));