function switch3col() {
    // just adds/removes a shit ton of classes from stuff

    const threemain = document.getElementsByClassName("osekai__3col_col1")[0];
    const spacer = document.getElementsByClassName("osekai__3col_col1_spacer")[0];
    const fucking = document.getElementsByClassName("osekai__3col_right");
    const sidebar = document.getElementsByClassName("osekai__ct3-arrow_area")[0];

    if (threemain.classList.contains("osekai__3col_col1_hide")) {
        threemain.classList.remove("osekai__3col_col1_hide");
    } else {
        threemain.classList.add("osekai__3col_col1_hide");
    }

    if (spacer.classList.contains("osekai__3col_col1_spacer_hide")) {
        spacer.classList.remove("osekai__3col_col1_spacer_hide");
    } else {
        spacer.classList.add("osekai__3col_col1_spacer_hide");
    }

    if (fucking[0].classList.contains("osekai__3col_right_hide")) {
        Object.keys(fucking).forEach(function(key){
            fucking[key].classList.remove("osekai__3col_right_hide");
        });
    } else {
        Object.keys(fucking).forEach(function(key){
            fucking[key].classList.add("osekai__3col_right_hide");
        });
    }

    if (sidebar.classList.contains("ct3open")) {
        sidebar.classList.remove("ct3open");

        document.body.classList.add("sidebar_closed");
    } else {
        sidebar.classList.add("ct3open");

        document.body.classList.remove("sidebar_closed");
    }
}
