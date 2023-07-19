var cur_url = window.location.href;
var cur_page = null;
var cur_page_parent = null;

var teams_page = document.getElementById("teams_page");

function switchUrl(url) {
    window.history.pushState('test', 'Test', url);
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

function switchPage() {
    teams_page.innerHTML = loader;
    updatePage();
    console.log(cur_url);

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
        } else {
            cur_page = teams_pages[cur_url[0]];
            cur_page_parent = null;
        }
        loadPage();
    } else {
        console.log("404");
        teams_page.innerHTML = "404";
    }
}

function loadPage() {
    var data = new FormData();
    var xhr = new XMLHttpRequest();

    if (cur_page_parent != null) {
        data.append('page', cur_page_parent.name);
        data.append('subpage', cur_page.name);
    } else {
        data.append('page', cur_page.name);
    }
    xhr.onload = function () {
        // do something to response
        teams_page.innerHTML = xhr.responseText;
    };
    xhr.open('POST', '/teams/views/load_view.php', true);
    xhr.send(data);
}

switchPage();