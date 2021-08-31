// hasParent param used for cases when element doesn't have a container/wrapper
const accordion = (target, hasParent=true) => {
    const panelEl = target.nextElementSibling;

    // prevent click during collapsing phase
    if (panelEl.classList.contains("collapsing")) {
        return false;
    }

    const parentEl = hasParent === true? target.parentElement : target;

    parentEl.classList.toggle("active");
    // remove class 'show' only when an open panel is collapsed
    if (!parentEl.classList.contains("active")) {
        panelEl.classList.remove("show");
    }
    panelEl.classList.remove("collapse");
    panelEl.classList.add("collapsing");

    // can't be combined with the previous if statement due to sequece of calling
    if (!parentEl.classList.contains("active")) {
        panelEl.style.height = null;
    } else {
        panelEl.style.height = panelEl.scrollHeight + "px";
    }

    // not compatible with IE 9 or older
    setTimeout((panel, parent) => {
        panel.classList.remove("collapsing");
        panel.classList.add("collapse");

        if (parent.classList.contains("active")) {
            panel.classList.add("show");
        }
    }, 225, panelEl, parentEl);
}