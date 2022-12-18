let openedDropdowns = [];
let openedDropdownsHC = [];
let openedDropdownsBL = [];

function removeItemAll(arr, value) {
    for (let i = arr.length - 1; i >= 0; i--) {
        if (arr[i] === value) {
            arr.splice(i, 1);
            openedDropdownsHC.splice(i, 1);
            openedDropdownsBL.splice(i, 1);
            // break;       //<-- Uncomment  if only the first term has to be removed
        }

    }
    return arr;
}

function dropdown(hiddenclass, id, blur = 0) {
    const z = document.getElementsByClassName("osekai__blur-overlay")[0];
    const y = document.getElementsByClassName("osekai__panel-container")[0];
    const x = document.getElementById(id);
    if (x.classList.contains(hiddenclass)) {
        if (window.mobile && id != "dropdown__apps_mobile" && id != "dropdown__apps") {
            hide_dropdowns();
        }

        x.classList.remove(hiddenclass);
        openedDropdowns.push(id);
        openedDropdownsHC.push(hiddenclass);
        openedDropdownsBL.push(blur);
        if (blur == 1) {
            console.log(openedDropdowns.length);
            if (openedDropdowns.length == 1) {
                console.log("blur time");
                try {
                    y.classList.add("osekai__panel-container__blur");
                } catch {

                }
                z.classList.add("osekai__blur-overlay__active");
            }
        }
        if (openedDropdowns.length == 1) {
            document.getElementById("osekai__apps-dropdown-gradient").classList.remove("osekai__apps-dropdown-gradient-hidden");
        }
        if (openedDropdowns.includes("dropdown__user")) {
            if (id == "dropdown__notifs" || id == "dropdown__settings") {
                dropdown(hiddenclass, "dropdown__user", blur);
            }
        }
        if (openedDropdowns.includes("dropdown__notifs")) {
            if (id == "dropdown__user" || id == "dropdown__settings") {
                dropdown(hiddenclass, "dropdown__notifs", blur);
            }
        }
        if (openedDropdowns.includes("dropdown__settings")) {
            if (id == "dropdown__user" || id == "dropdown__notifs") {
                dropdown(hiddenclass, "dropdown__settings", blur);
            }
        }
    } else {
        x.classList.add(hiddenclass);
        openedDropdowns = removeItemAll(openedDropdowns, id);
        // openedDropdownsHC = removeItemAll(openedDropdownsHC, hiddenclass);
        // openedDropdownsBL = removeItemAll(openedDropdownsBL, blur);
        if (blur == 1) {
            console.log(openedDropdowns.length);
            if (openedDropdowns.length == 0) {
                try {
                    y.classList.remove("osekai__panel-container__blur");
                } catch {

                }
                z.classList.remove("osekai__blur-overlay__active");
                document.getElementById("osekai__apps-dropdown-gradient").classList.add("osekai__apps-dropdown-gradient-hidden");
            }
        } else {
            if (openedDropdowns.length == 0) {
                document.getElementById("osekai__apps-dropdown-gradient").classList.add("osekai__apps-dropdown-gradient-hidden");
            }
        }
    }

    console.log(openedDropdowns);
    console.log(openedDropdownsHC);
    console.log(openedDropdownsBL);
}

function hide_dropdowns() {
    // openedDropdowns.forEach((num1, index) => {
    //    console.log("removing " + num1)
    //    dropdown(openedDropdownsHC[index], num1, openedDropdownsBL[index])
    //    if(openedDropdownsBL[index] == 1){
    //        var z = document.getElementsByClassName("osekai__blur-overlay")[0];
    //        var y = document.getElementsByClassName("osekai__panel-container")[0];
    //
    //        y.classList.remove("osekai__panel-container__blur")
    //        z.classList.remove("osekai__blur-overlay__active");
    //    }
    // });
    // i am going to punch someone
    console.log("frijfiifgj");

    for (let index = 0; index < openedDropdowns.length; ++index) {
        let z = document.getElementsByClassName("osekai__blur-overlay")[0];
        let y = document.getElementsByClassName("osekai__panel-container")[0];
        let x = document.getElementById(openedDropdowns[index]);

        x.classList.add(openedDropdownsHC[index]);
        try {
            y.classList.remove("osekai__panel-container__blur");
        } catch {

        }
        z.classList.remove("osekai__blur-overlay__active");
    }

    openedDropdowns = [];
    openedDropdownsBL = [];
    openedDropdownsHC = [];

    let x = document.getElementById("nav_chevron");
    if (x.classList.contains("nav_chevron_flipped")) {
        x.classList.remove("nav_chevron_flipped");
    }
    document.getElementById("osekai__apps-dropdown-gradient").classList.add("osekai__apps-dropdown-gradient-hidden");
    hideOtherApps();
}
