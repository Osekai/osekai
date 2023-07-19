function switch3col() {
    // just adds/removes a shit ton of classes from stuff

    var threemain = document.getElementsByClassName("osekai__3col_col1")[0];
    var spacer = document.getElementsByClassName("osekai__3col_col1_spacer")[0];
    var fucking = document.getElementsByClassName("osekai__3col_right");
    var sidebar = document.getElementsByClassName("osekai__ct3-arrow_area")[0];

    threemain.classList.toggle("osekai__3col_col1_hide");
    spacer.classList.toggle("osekai__3col_col1_spacer_hide");

    if (fucking[0].classList.contains("osekai__3col_right_hide")) {
        Object.keys(fucking).forEach(function (key, index) {
            fucking[key].classList.remove("osekai__3col_right_hide");
        });
    } else {
        Object.keys(fucking).forEach(function (key, index) {
            fucking[key].classList.add("osekai__3col_right_hide");
        });
    }

    if (mobile) {
        if (threemain.classList.contains("osekai__3col_col1_hide")) {
            for (var x of document.getElementsByClassName("osekai__ct3-backdrop")) {
                x.classList.add("hidden");
            }
        } else {
            for (var x of document.getElementsByClassName("osekai__ct3-backdrop")) {
                x.classList.remove("hidden");
            }
        }
    }


    if (sidebar.classList.contains("ct3open")) {
        sidebar.classList.remove("ct3open");
        document.body.classList.add("sidebar_closed");
    } else {
        sidebar.classList.add("ct3open");
        document.body.classList.remove("sidebar_closed");
    }
}
