// idea is that apps can register a callback for when we click on a search result
// for example if we're currently on profiles, it'll set a callback for "profiles" search result which opesn that profile
// if the current search result doesn't have any callback registered it'll instead go to a url

var search_callbacks = [];
var search_apps = [
    {
        "name": "medals",
        "icon": "oif-app-medals",
        "readable_name": "Osekai Medals"
    },
    {
        "name": "snapshots",
        "icon": "oif-app-snapshots",
        "readable_name": "Osekai Snapshots"
    },
    {
        "name": "profiles",
        "icon": "oif-app-profiles",
        "readable_name": "Osekai Profiles"
    }
]
// TODO: default cur_app to current app if the current app is in `apps`, else default to medals
var search_cur_app = "medals";
var search_input = document.getElementById("search_input");
var search_overlay = document.getElementById("search_overlay")
var search_inner = null;

search_input.addEventListener("focus", function () {
    loaderopened = false;
    if (search_overlay.classList.contains("osekai__navbar-search-overlay-hidden")) {
        search_overlay.classList.remove("osekai__navbar-search-overlay-hidden")
        search_initOverlay();
    }
})
search_input.addEventListener("keydown", function(e) {
    var k = e.which;
    // surely there's a better way to do this... https://stackoverflow.com/questions/7770561/reject-control-keys-on-keydown-event
    if (k == 20 /* Caps lock */
     || k == 16 /* Shift */
     || k == 9 /* Tab */
     || k == 27 /* Escape Key */
     || k == 17 /* Control Key */
     || k == 91 /* Windows Command Key */
     || k == 19 /* Pause Break */
     || k == 18 /* Alt Key */
     || k == 93 /* Right Click Point Key */
     || ( k >= 35 && k <= 40 ) /* Home, End, Arrow Keys */
     || k == 45 /* Insert Key */
     || ( k >= 33 && k <= 34 ) /*Page Down, Page Up */
     || (k >= 112 && k <= 123) /* F1 - F12 */
     || (k >= 144 && k <= 145 )) { /* Num Lock, Scroll Lock */
        return false;
    }
    search_startSearch();
});
document.addEventListener("click", function (e) {
    loaderopened = false;
    if (!e.target.classList.contains("osekai__navbar-search") && (e.target.closest(".osekai__navbar-search") == null)) {
        search_overlay.classList.add("osekai__navbar-search-overlay-hidden")
    }
})

// note: instead of focusout, we'll detect click, and if that's not on the dropdown or input, we close overlay

var search_startDelay = null;

// & the second the user even selects the input, the ui will be opened and set to a 
// & loading circle, so that will be managed here that will also allow the user to
// & switch what app they're searching if wanted. close when unselected as well
// & (can probably use some other existing code for that) 


function search_searchItem(data) {

    var item = Object.assign(document.createElement("a"), {className: "osekai__navbar-search-item"});
    if(typeof(data['img']) != "undefined") {
        var icon = document.createElement("img");
        icon.src = data['img'];
        item.appendChild(icon);
    }
    var title = Object.assign(document.createElement("p"), {innerHTML: data['name']});
    item.appendChild(title);

    item.href = data['url'];

    return item;
}
function search_searchResult(result) {
    var list = Object.assign(document.createElement("div"), {className: "osekai__navbar-search-list"});
    for(var data of result) {
        var item = search_searchItem(data);
        list.appendChild(item);
    }
    return list;
}

function search_initOverlay() {
    // TODO: add the app icons, make em clickable, etc
    search_overlay.innerHTML = "";
    let search_apps_el = Object.assign(document.createElement("div"), { "className": "osekai__navbar-search-overlay-apps" });
    for (let app of search_apps) {
        let el = Object.assign(document.createElement("i"), { "className": "tooltip-v2 " + app.icon });
        if (app.name == search_cur_app) {
            el.classList.add("active")
        }
        search_apps_el.appendChild(el);
        el.addEventListener("click", function () {
            search_cur_app = app.name;
            for (var _appel of search_apps_el.childNodes) {
                _appel.classList.remove("active");
            }
            el.classList.add("active")
        })
    }
    search_inner = Object.assign(document.createElement("div"), { "className": "osekai__navbar-search-overlay-inner" });
    search_overlay.appendChild(search_apps_el)
    search_overlay.appendChild(search_inner)
    search_inner.innerHTML = "<p>Start typing to search!</p>";
}
function search_openOverlay() {

}
function search_closeOverlay() {

}
var loaderopened = false;
function search_startSearch() {
    // timeout here, since apparently search_input.value does not have the brain power to update fast enough for us 
    setTimeout(function () {
        if (search_input.value != "") {
            console.log("searching with value " + search_input.value)
            if (loaderopened == false) {
                loaderopened = true;
                search_inner.innerHTML = loader;
            }
            // ? note: this is so we don't end up spamming the api, we wait 100ms after the user has *finished typing*
            // ? aka we replace this variable with a new timeout every character they type, should work fine
            // ? will also cancel the request in hte case that another character is typed
            clearTimeout(search_startDelay);
            search_startDelay = setTimeout(search_doSearch, 500);
        } else {
            clearTimeout(search_startDelay);
            search_inner.innerHTML = "<p>Start typing to search!</p>";
        }
    }, 50);
}

function search_doSearch() {
    console.log("attempting search!");
    loaderopened = false;
    var search_query = search_input.value;
    var data = new FormData();
    data.append("type", search_cur_app);
    data.append("query", search_query);
    var xhr = new XMLHttpRequest();

    xhr.open("POST", "/global/api/search.php", true);
    xhr.onload = function () {
        var response = JSON.parse(xhr.response);
        console.log(response);
        search_inner.innerHTML = "";
        search_inner.appendChild(search_searchResult(response));
    }
    xhr.send(data);

}