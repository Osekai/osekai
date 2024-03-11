var by = ""; // log by who
var includes = "";// text includes
var pagination_number = 0;
var last_page = false;

var logData = "";

function search() {
    by = document.getElementById("logs__by").value;
    includes = document.getElementById("logs__includes").value;
    updateLogs();
}

document.search = search;

function updateLogs() {
    resetList();
    pagination_number = 0;
    logData = [];
    last_page = false;
    nextPage();
}

function nextPage(button = null) {
    var xhr = createXHR("/admin/panel/api/logs/get");
    xhr.send(`filter=${encodeURIComponent(includes)}&user=${by}&offset=${pagination_number}`)
    xhr.onload = function () {
        var first = pagination_number == 0;
        pagination_number += 50;
        var tmp = JSON.parse(xhr.responseText);
        for (var x = 0; x < tmp.length; x++) {
            logData.push(tmp[x]);
        }
        if (tmp.length < 50) {
            last_page = true;
        }
        if (button != null) {
            button.remove();
        }
        fillList(first);
    }
}

let spinner;
const list = document.getElementById("logs__list");

function resetList() {
    list.innerHTML = "";
    spinner = new loadingSpinner(list);
    list.innerHTML += "<br>Loading...";
}

function fillList(first = false) {
    if (first) {
        list.classList.remove("spinner-container");
        list.innerHTML = ""; // remove spinner
    }

    for (var x = pagination_number - 50; x < logData.length; x++) {
        var log = logData[x];
        list.innerHTML += `<div class="logs__item">
        <img src="https://a.ppy.sh/${log.user}">
        <div class="logs__item-texts">
            <user id="${log.user}" class="logs__item-texts-username">${log.username}</user>
            <div class="logs__item-texts-data">
            ${log.data}
            </div>
        </div>
    </div>`;
    }

    // todo: add to list
    if (!last_page) {
        list.innerHTML += `<div class="button" onclick="nextPage(this)">Load more</div>`;
    }

    // Tippy goes here... because it doesn't work if the element doesn't exist
    createUserTippys();
}

document.addEventListener("DOMContentLoaded", function () {
    updateLogs();
});