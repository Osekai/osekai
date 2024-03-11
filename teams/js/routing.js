var cur_url = window.location.href;
var cur_page = null;
var cur_page_parent = null;

var teams_page = document.getElementById("teams_page");

function switchUrl(url) {
    window.history.pushState('test', 'Test', "/teams/" + url);
    switchPage();
}

function updatePage() {
    cur_url = window.location.pathname;
    cur_url = cur_url.split("/");
    cur_url.shift();
    cur_url.shift();
}

window.onpopstate = function (event) {
    switchPage();
}


var teamname = "";
var attributes = {};

function setNavBar() {
    var navbar = document.getElementById("pages");
    navbar.innerHTML = "";
    for(let page of cur_page_parent['pages']) {
        let item = Object.assign(document.createElement("p"), {"className": "osekai__tab-page-navigation-item", "innerText": page.display_name});
        navbar.appendChild(item);
        item.addEventListener("click", function() { 
            switchUrl(cur_url[0] + "/" + page.name)
        });
    }
}

function switchPage() {
    attributes = {};
    teams_page.innerHTML = loader;
    updatePage();

    console.log(cur_url);
    console.log(teams_pages);
    if (typeof teams_pages[cur_url[0]] !== "undefined") {
        /* for(var page of teams_pages[cur_url[1]]['pages']) {

        }
        if(teams_pages[cur_url[1]]['pages'][cur_url[2]] != undefined ){
            
        } else {
            if(teams_pages[cur_url[1]]['pages'] != undefined) {
                cur_page = teams_pages[cur_url[1]]['pages'][0];
            } else {
                cur_page = teams_pages[cur_url[1]];
            }
        } */

        if (typeof teams_pages[cur_url[0]]['pages'] !== "undefined") {
            var found = false;
            for (let page of teams_pages[cur_url[0]]['pages']) {
                console.log(page['name']);
                if (page['name'] == cur_url[1]) {
                    console.log(page);
                    found = true;
                    cur_page = page;
                }
            }
            if (found == false) cur_page = teams_pages[cur_url[0]]['pages'][0];

            cur_page_parent = teams_pages[cur_url[0]];

            if (cur_page_parent['name'] == "team") return;
        } else {
            cur_page = teams_pages[cur_url[0]];
            cur_page_parent = null;
        }
        setNavBar();
        loadPage();
    } else {
        if (cur_url[0].startsWith("@")) {
            console.log("is team");
            teamname = cur_url[0];
            attributes['team'] = cur_url[0];
            cur_page_parent = teams_pages["team"];
            var found_page = false;
            console.log(cur_url[1]);
            if (cur_url[1] == "" || typeof (cur_url[1]) == "undefined") {
                cur_page = teams_pages["team"]["pages"][0];
                found_page = true;
            }
            else {
                for (var page of cur_page_parent['pages']) {
                    if (page.name == cur_url[1]) {
                        found_page = true;
                        cur_page = page;
                    }
                }
            }
            //cur_page = teams_pages["team"]["pages"][0];
            if (!found_page) {
                console.log("404");
                teams_page.innerHTML = "404";
                return
            }
            setNavBar();
            loadPage();
            return;
        } else {
            console.log("404");
            teams_page.innerHTML = "404";
        }
    }
}

var teams_js = document.getElementById("teams_js");
var teams_css = document.getElementById("teams_css");
function loadPage() {
    var data = new FormData();
    var xhr = new XMLHttpRequest();

    if (cur_page_parent != null) {
        data.append('page', cur_page_parent.name);
        data.append('subpage', cur_page.name);
    } else {
        data.append('page', cur_page.name);
    }
    for (var attribute in attributes) {
        data.append(attribute, attributes[attribute]);
    }
    xhr.onload = function () {
        // do something to response
        teams_css.innerHTML = "";
        var stylesheet = document.createElement("link");
        stylesheet.rel = "stylesheet";
        stylesheet.href = "/teams/css/" + cur_page_parent.name + "/" + cur_page.name + ".css";
        teams_css.appendChild(stylesheet);
        teams_page.innerHTML = xhr.responseText;
        teams_js.innerHTML = "";
        var script = document.createElement('script');
        script.src = "/teams/js/" + cur_page_parent.name + "/" + cur_page.name + ".js";
        teams_js.appendChild(script);
    };
    xhr.open('POST', '/teams/views/load_view.php', true);
    xhr.send(data);
}

switchPage();