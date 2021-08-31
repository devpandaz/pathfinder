const hamburger = document.querySelector(".header-container .hamburger");
const menu = document.querySelector(".header-nav .btn-icon");
const menucontent = document.querySelector(".header-nav .menu-content");
const sidebar = document.querySelector(".app-sidebar");
menu.addEventListener("click", function(event) {
    event.preventDefault();
    menucontent.classList.toggle("show");
});

hamburger.addEventListener("click", function() {
    this.classList.toggle("is-active");
    sidebar.classList.toggle("open");
});

const signup_dropdown_menu = document.querySelector("header .menu-content .dropdown-menu");
if (signup_dropdown_menu) {
    signup_dropdown_menu.addEventListener("click", function() {
        this.classList.toggle("active");
    });
}
    