var data;

function getBadgeFromID(id) {
    for (var i = 0; i < data.length; i++) {
        if (data[i].id == id) {
            return data[i];
        }
    }
    return null;
}

function loadData() {
    // /badges/api/getBadges.php
    strUrl = "/badges/api/getBadges.php";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", strUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            data = JSON.parse(xhr.responseText);

            document.getElementById("title").innerHTML = GetStringRawNonAsync("badges", "badges.title", [data.length]);



            if (localStorage.getItem("imageSize") != null) {
                setImageSize(localStorage.getItem("imageSize"), false);
            }

            if (localStorage.getItem("viewType") != null) {
                changeViewtype(localStorage.getItem("viewType"), false);
            }

            // geturl query
            var url = new URL(window.location.href);
            var badgeId = url.searchParams.get("badge");
            if (badgeId != null) {
                openBadge(badgeId);
            }

            fillData();
        }
    }
}

var viewTypes = ["grid_large", "list_2wide", "list_1wide", "ultra_compact"];
var viewType = "grid_large";

var sortingTypes = ["awarded_at_asc", "awarded_at_desc", "name_asc", "name_desc", "players_asc", "players_desc"]
var sortingTypes_Names = [GetStringRawNonAsync("badges", "sort.awardedAt.asc"), GetStringRawNonAsync("badges", "sort.awardedAt.desc"), GetStringRawNonAsync("badges", "sort.name.asc"), GetStringRawNonAsync("badges", "sort.name.desc"), GetStringRawNonAsync("badges", "sort.playerCount.asc"), GetStringRawNonAsync("badges", "sort.playerCount.desc")];
var currentSorting = "awarded_at_desc";


loadSource("badges").then(function () {
    sortingTypes_Names = [GetStringRawNonAsync("badges", "sort.awardedAt.asc"), GetStringRawNonAsync("badges", "sort.awardedAt.desc"), GetStringRawNonAsync("badges", "sort.name.asc"), GetStringRawNonAsync("badges", "sort.name.desc"), GetStringRawNonAsync("badges", "sort.playerCount.asc"), GetStringRawNonAsync("badges", "sort.playerCount.desc")];
    // grab these again in case the like fuckin' 10% chance they didnt load the last time hits
    // i dont know why this is an issue
    // help
    var items = document.getElementById("sort_items");
    var items_html = "";
    for (var i = 0; i < sortingTypes.length; i++) {
        items_html += `<div class="osekai__dropdown-item" onclick="changeSorting('` + sortingTypes[i] + `')" id="sort_` + sortingTypes[i] + `">` + sortingTypes_Names[i] + `</div>`;
    }
    items.innerHTML = items_html;

    document.getElementById("sort_activeItem").innerHTML = sortingTypes_Names[sortingTypes.indexOf(currentSorting)];
});

var changeSorting = function (type) {
    currentSorting = type;
    // wait 1 second

    var content = document.getElementById("content");
    content.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>";

    setTimeout(function () {
        fillData();
    }, 100);

    for (var i = 0; i < sortingTypes.length; i++) {
        var item = document.getElementById("sort_" + sortingTypes[i]);
        if (sortingTypes[i] == type) {
            item.classList.add("osekai__dropdown-item-active");
        } else {
            item.classList.remove("osekai__dropdown-item-active");
        }
    }
    document.getElementById("sort_activeItem").innerHTML = sortingTypes_Names[sortingTypes.indexOf(type)];
}

function openSortDropdown() {
    document.getElementById("sort_items").classList.toggle("osekai__dropdown-hidden");
}


function fillData() {
    // resort badges
    var ndata = data.sort(function (a, b) {
        if (currentSorting == "awarded_at_asc") {
            var date = new Date(a.awarded_at);
            var date2 = new Date(b.awarded_at);
            return date - date2;
        } else if (currentSorting == "awarded_at_desc") {
            var date = new Date(a.awarded_at);
            var date2 = new Date(b.awarded_at);
            return date2 - date;
        } else if (currentSorting == "name_asc") {
            return a.name.localeCompare(b.name);
        } else if (currentSorting == "name_desc") {
            return b.name.localeCompare(a.name);
        } else if (currentSorting == "players_asc") {
            var count = a.users.length;
            var count2 = b.users.length;
            return count - count2;
        } else if (currentSorting == "players_desc") {
            var count = a.users.length;
            var count2 = b.users.length;
            return count2 - count;
        }
    });




    var search = document.getElementById("search").value;
    var content = document.getElementById("content");
    content.innerHTML = loader;
    // remove viewtypes from content
    for (var i = 0; i < viewTypes.length; i++) {
        content.classList.remove(viewTypes[i]);
    }
    content.classList.add(viewType);

    var html = "";

    for (var i = 0; i < data.length; i++) {
        var badge = ndata[i];
        html += "<div class='badge " + viewType + "' onclick='openBadge(" + badge.id + ")' id='badge-" + badge.id + "'>";
        var image = badge.image_url;
        if (imageSize == "2x") {
            image = image.replace(".png", "@2x.png");
        }
        html += "<div class='badge_img'><img src='" + image + "' onerror='this.src=\"/badges/img/badge_default.png\";' /></div>";
        html += "<div class='badge_texts'><div class='badge_name'>" + badge.name + "</div>";
        html += "<div class='badge_desc'>" + badge.description + "</div>";
        var users = badge.users;
        // this is a string but it's json
        var users = JSON.parse(users);

        var awarded_at = badge.awarded_at;
        if (awarded_at == "2013-08-07") {
            awarded_at = "long ago";
        }

        html += "<div class='badge_info'>";
        html += GetStringRawNonAsync("badges", "badge.firstAchieved", [awarded_at]);
        html += " • ";
        if (users.length != 1) {
            html += GetStringRawNonAsync("badges", "badge.ownedBy", [users.length]);
        } else {
            html += GetStringRawNonAsync("badges", "badge.ownedBySingular", [users.length]);
        }
        html += "</div>";
        html += "</div>";
        html += "</div>";

    }

    content.innerHTML = html;
    runSearch();
}
loadData();

function changeViewtype(type, fill = true) {
    viewType = type;

    for (var i = 0; i < viewTypes.length; i++) {
        document.getElementById("viewtype-" + viewTypes[i]).classList.remove("badges__panel-header-viewtype-active");
    }
    document.getElementById("viewtype-" + type).classList.add("badges__panel-header-viewtype-active");

    localStorage.setItem("viewType", type);

    if (fill == true) {
        fillData();
    }


}

var imageSize = "1x";

function setImageSize(size, fill = true) {
    document.getElementById("1x").classList.remove("badges__panel-header-viewtype-active");
    document.getElementById("2x").classList.remove("badges__panel-header-viewtype-active");

    document.getElementById(size).classList.add("badges__panel-header-viewtype-active");

    imageSize = size;

    if (fill == true) {
        fillData();
    }

    // remember options using localStorage
    localStorage.setItem("imageSize", size);
}

function openBadge(index) {
    var obj_name = document.getElementById("bop_name");
    var obj_desc = document.getElementById("bop_desc");
    var obj_img = document.getElementById("bop_img");
    var obj_img2 = document.getElementById("bop_img2");
    var obj_img_1x = document.getElementById("bop_img_1x");
    var obj_achieved = document.getElementById("bop_achieved");
    var obj_amount = document.getElementById("bop_amount");
    var container_users = document.getElementById("bop_users");

    var badge = getBadgeFromID(index);

    obj_name.innerHTML = badge.name;
    obj_desc.innerHTML = badge.description;
    obj_img.src = badge.image_url.replace(".png", "@2x.png");
    obj_img2.src = badge.image_url.replace(".png", "@2x.png");

    document.getElementById("1x_var").classList.remove("hidden");

    obj_img.onerror = function () {
        var obj_img = document.getElementById("bop_img");
        var obj_img2 = document.getElementById("bop_img2");

        obj_img.src = badge.image_url;
        obj_img2.src = badge.image_url;
        document.getElementById("1x_var").classList.add("hidden");
    }

    obj_img_1x.src = badge.image_url;

    var awarded_at = badge.awarded_at;
    if (awarded_at == "2013-08-07") {
        awarded_at = "long ago";
    }

    obj_achieved.innerHTML = GetStringRawNonAsync("badges", "badge.firstAchieved", [awarded_at]);
    var users = badge.users;
    // this is a string but it's json
    var users = JSON.parse(users);
    obj_amount.innerHTML = GetStringRawNonAsync("badges", "badge.ownedBy", [users.length]);

    // api/getUsers.php?badge_id=1
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/badges/api/getUsers.php?badge_id=" + badge.id, true);
    xhr.send();
    // set container to loading
    container_users.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>";

    var obj_main = document.getElementById("bop_overlay");
    obj_main.classList.remove("badges__badge-overlay_hidden");
    var obj_panel = document.getElementById("bop_panel");
    obj_panel.classList.remove("badges__badge-panel_hidden");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var users = JSON.parse(xhr.responseText);
            var html = "";
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                html += "<a class='badge_user' href='https://osu.ppy.sh/users/" + user.id + "'>";
                html += "<img src='https://a.ppy.sh/" + user.id + "'><div class='badge_user_name'>" + user.name + "</div>";
                html += "</a>";
            }
            container_users.innerHTML = html;
        }
    }
    // set url query to badge id
    window.history.replaceState("", "", "?badge=" + badge.id);
}

function hideOverlay() {
    // remove queyr
    URLSearchParams = window.URLSearchParams || window.location.search;
    var urlParams = new URLSearchParams(window.location.search);
    urlParams.delete("badge");
    window.history.replaceState("", "", "?" + urlParams.toString());


    var obj_main = document.getElementById("bop_overlay");
    obj_main.classList.add("badges__badge-overlay_hidden");
    var obj_panel = document.getElementById("bop_panel");
    obj_panel.classList.add("badges__badge-panel_hidden");
}




function runSearch() {
    console.log("runSearch");

    var searchQuery = document.getElementById("search").value;
    var searchQuery = searchQuery.toLowerCase();


    if (searchQuery == "") {
        for (var i = 0; i < data.length; i++) {
            document.getElementById("badge-" + data[i].id).classList.remove("hidden");
            document.getElementById("title").innerHTML = "Badges (" + data.length + ")";
        }
        return
    }

    var count = 0;

    for (var i = 0; i < data.length; i++) {
        var badge = data[i];
        var name = badge.name.toLowerCase();
        if (name.includes(searchQuery) || badge.description.toLowerCase().includes(searchQuery)) {
            document.getElementById("badge-" + badge.id).classList.remove("hidden");
            count++;
        } else {
            document.getElementById("badge-" + badge.id).classList.add("hidden");
        }
    }

    document.getElementById("title").innerHTML = "Badges (" + count + ")";


}

document.getElementById("search").addEventListener("keyup", runSearch);