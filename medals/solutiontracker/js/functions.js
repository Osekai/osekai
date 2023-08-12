var colMedals = [];


var xhr = new XMLHttpRequest();
xhr.open('POST', '/medals/api/medals.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
xhr.onreadystatechange = function () {
    var oResponse = getResponse(xhr);
    if (handleUndefined(oResponse)) return;

    // Sort using medals_grouping_ordering
    oResponse = oResponse.sort((a, b) => medals_grouping_ordering.indexOf(a.Grouping) - medals_grouping_ordering.indexOf(b.Grouping));

    for (medal of oResponse) {
        colMedals[medal.Name] = medal;
    }

    doPage();
};
xhr.send('type=solutiontracker&strSearch=');

function doPage() {
    let params = new URLSearchParams(window.location.search);
    if (params.get("medal") != null) {
        switchPage("medal")
        loadMedal(params.get("medal"));
    } else {
        console.log("home");
        switchPage("home")
        loadHome();
    }
    localStorage.setItem("url", location.href);
}

window.addEventListener('popstate', function (event) {
    doPage();
});

function loadMedal(medal) {
    set_breadcrums("{app}/Solution Tracker/" + medal);
    console.log("loading " + medal);
    if(typeof(colMedals[medal]) == "undefined") {
        switchPage("home");
        return;
    }
    
    let params = new URLSearchParams(window.location.search);
    params.set("medal", encodeURIComponent(medal));
    window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));

    var medalobj = colMedals[medal];
    console.log(medalobj);
}



var pages = {
    "home": {
        "id": "st_home"
    },
    "medal": {
        "id": "st_medal"
    }
}
function switchPage(index) {
    for (var page in pages) {
        document.getElementById(pages[page].id).classList.add("hidden");
    }
    document.getElementById(pages[index].id).classList.remove("hidden");

    if(index == "home") {
        let params = new URLSearchParams(window.location.search);
        params.delete("medal");
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}${params}`));
    }
}

function medalPanel(medal) {
    var panel = Object.assign(document.createElement("div"), {"className": "st__medalpanel"});
    
    var img = Object.assign(document.createElement("img"), {"src": medal.Link});

    var texts = document.createElement("div");

    var h1 = Object.assign(document.createElement("h1"), {"innerText": medal.Name});
    var h2 = Object.assign(document.createElement("h2"), {"innerText": medal.Description});
    var h3 = Object.assign(document.createElement("h3"), {"innerHTML": medal.Instructions});

    texts.appendChild(h1);
    texts.appendChild(h2);
    texts.appendChild(h3);

    panel.appendChild(img);
    panel.appendChild(texts);

    return panel;
}

function clickableMedalPanel(medal, callback) {
    var a = Object.assign(document.createElement("a"), {"className": "st__medalpanel-clickable"});
    a.addEventListener("click", callback);
    a.appendChild(medalPanel(medal));
    return a;
}


var homeLoaded = false;
function loadHome() {
    if(homeLoaded == true) return;
    console.log(colMedals);
    set_breadcrums("{app}/Solution Tracker/Home");
    for(let medal in colMedals) {
        console.log(medal);
        console.log(1);
        document.getElementById("st_home_medal_grid").appendChild(clickableMedalPanel(colMedals[medal], function() {
            console.log(medal);
            switchPage("medal");
            loadMedal(medal);
        }));
    }

    homeLoaded = true;
}