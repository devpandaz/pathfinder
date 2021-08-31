const scrollbar = document.querySelector('.scrollbar-sidebar');
const ps = new PerfectScrollbar(scrollbar);

const menuExpand = document.getElementsByClassName("menu-expand");

Array.from(menuExpand).forEach((element) => {
    element.querySelector("a").addEventListener("click", function(event) {
        // handler prevents the use of return false 
        // so, .preventDefault is implemented instead
        event.preventDefault();
        const target = event.currentTarget;
        accordion(target, true);
    });
});