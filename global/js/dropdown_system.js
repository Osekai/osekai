var currently_open = null;
var actively_open = false;

function dropdown(hiddenclass, id, blur = 0) {
    var blur_overlay = document.getElementById("blur_overlay");
    if(!document.getElementById(id).classList.contains(hiddenclass)) {
        hide_dropdowns();
        return;
    }
    hide_dropdowns();
    document.getElementById(id).classList.remove(hiddenclass);
    currently_open = {
        "element": document.getElementById(id),
        "classname": hiddenclass
    }
    blur_overlay.classList.add("osekai__blur-overlay__active");
    actively_open = true;
}

function apps_dropdown(hide = false) {
    var blur_overlay = document.getElementById("blur_overlay");
    var chevron = document.getElementById("nav_chevron");

    if(!document.getElementById("dropdown__apps").classList.contains("osekai__apps-dropdown-hidden") || hide == true) {
        // hide;
        document.getElementById("dropdown__apps").classList.add("osekai__apps-dropdown-hidden");
        document.getElementById("dropdown__apps_mobile").classList.add("osekai__apps-dropdown-mobile-hidden");
        chevron.classList.remove("nav_chevron_flipped");
        blur_overlay.classList.remove("osekai__blur-overlay__active");
        return;
    }
    hide_dropdowns(false);
    blur_overlay.classList.add("osekai__blur-overlay__active");
    document.getElementById("dropdown__apps").classList.remove("osekai__apps-dropdown-hidden");
    document.getElementById("dropdown__apps_mobile").classList.remove("osekai__apps-dropdown-mobile-hidden");
    chevron.classList.add("nav_chevron_flipped");
}

function hide_dropdowns(hideapps = true) {
    actively_open = false;
    if(hideapps) apps_dropdown(true);
    if(currently_open == null) return;
    var blur_overlay = document.getElementById("blur_overlay");
    blur_overlay.classList.remove("osekai__blur-overlay__active");
    currently_open.element.classList.add(currently_open.classname);
}