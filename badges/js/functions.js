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
    xhr.onload = function () {
        if (xhr.status == 200) {
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

    sortingTypes.forEach((sortingType) => {
        var item = document.getElementById("sort_" + sortingType);
        console.log(item);
        if (sortingType == type)
            item.classList.add("osekai__dropdown-item-active");
        else
            item.classList.remove("osekai__dropdown-item-active");
    });

    document.getElementById("sort_activeItem").innerHTML = sortingTypes_Names[sortingTypes.indexOf(type)];

    setTimeout(function () {
        fillData();
    }, 1);

}

function openSortDropdown() {
    document.getElementById("sort_items").classList.toggle("osekai__dropdown-hidden");
}


function fillData() {
    // resort badges
    let ndata;
    switch (currentSorting) {
        case "awarded_at_asc":
            // data is already sorted by awarded_at, just need to reverse it
            ndata = data.slice().reverse();
            break;
        case "awarded_at_desc":
            // Not need to sort, its already sorted from API
            ndata = data;
            break;
        case "name_asc":
            ndata = data.sort((a, b) => {
                return a.name.localeCompare(b.name);
            });
            break;
        case "name_desc":
            ndata = data.sort((a, b) => {
                return b.name.localeCompare(a.name);
            });
            break;

        case "players_asc":
            ndata = data.sort((a, b) => {
                let count = a.users.length;
                let count2 = b.users.length;
                return count - count2;
            });
            break;

        case "players_desc":
            ndata = data.sort((a, b) => {
                let count = a.users.length;
                let count2 = b.users.length;
                return count2 - count;
            });
            break;

        default:
            console.log('what');
            break;
    }

    let content = document.getElementById("content");
    // remove viewtypes from content
    for (let i = 0; i < viewTypes.length; i++) {
        content.classList.remove(viewTypes[i]);
    }
    content.classList.add(viewType);

    console.time('badges proc');

    let badgeList = [];
    ndata.forEach(async (badge) => {
        let badgeElement = document.createElement('div');
        badgeElement.classList.add('badge');
        badgeElement.classList.add(viewType);
        badgeElement.id = `badge-${badge.id}`;
        badgeElement.onclick = () => {
            openBadge(badge.id);
        };

        let image = badge.image_url;
        if (imageSize == "2x")
            image = image.replace(".png", "@2x.png");

        let badgeImageDiv = document.createElement('div');
        badgeImageDiv.classList.add('badge_img');

        let badgeImage = document.createElement('img');
        badgeImage.src = image;
        badgeImage.onerror = () => {
            // If the img is @2x, revert to @1x in case the badge has no @2x version
            // if it still fails then fallback to badge_default.png
            if (badgeImage.src.includes('@2x.png')) {
                badgeImage.src = badgeImage.src.replace('@2x.png', '.png');
            } else {
                badgeImage.src = '/badges/img/badge_default.png';
                badgeImage.onerror = null;
            }
        }
        badgeImageDiv.appendChild(badgeImage);
        badgeElement.appendChild(badgeImageDiv);

        let badgeTexts = document.createElement('div');
        badgeTexts.classList.add('badge_texts');

        let badgeName = document.createElement('div');
        badgeTexts.classList.add('badge_name');
        badgeName.textContent = badge.name;
        badgeTexts.appendChild(badgeName);

        let badgeDesc = document.createElement('div');
        badgeDesc.classList.add('badge_desc');
        badgeDesc.textContent = badge.description;
        badgeTexts.appendChild(badgeDesc);

        let awarded_at = badge.awarded_at;
        if (awarded_at == "2013-08-07")
            awarded_at = "long ago";

        let badgeInfo = document.createElement('div');
        badgeInfo.classList.add('badge_info');
        badgeInfo.innerHTML = GetStringRawNonAsync("badges", "badge.firstAchieved", [awarded_at]);

        badgeInfo.innerHTML += ' • ';

        let users = JSON.parse(badge.users);
        if (users.length != 1)
            badgeInfo.innerHTML += GetStringRawNonAsync("badges", "badge.ownedBy", [users.length]);
        else
            badgeInfo.innerHTML += GetStringRawNonAsync("badges", "badge.ownedBySingular", [users.length]);

        badgeTexts.appendChild(badgeInfo);
        badgeElement.appendChild(badgeTexts);
        badgeList.push(badgeElement);
    });
    console.timeEnd('badges proc');

    console.time('badges append');
    content.textContent = '';
    badgeList.forEach(element => {
        content.appendChild(element);
    });
    console.timeEnd('badges append');

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
        setTimeout(fillData, 1);
    }
}

var imageSize = "1x";

function setImageSize(size, fill = true) {
    document.getElementById("1x").classList.remove("badges__panel-header-viewtype-active");
    document.getElementById("2x").classList.remove("badges__panel-header-viewtype-active");

    document.getElementById(size).classList.add("badges__panel-header-viewtype-active");

    imageSize = size;

    if (size == "1x") {
        let imgs = document.getElementsByClassName('badge_img');
        Object.values(imgs).forEach((element) => {
            let imgElement = element.children[0];
            imgElement.src = imgElement.src.replace('@2x.png', '.png');
        });
    } else {
        let imgs = document.getElementsByClassName('badge_img');
        Object.values(imgs).forEach((element) => {
            let imgElement = element.children[0];
            imgElement.src = imgElement.src.replace('.png', '@2x.png');
        });
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

    xhr.onload = function () {
        if (xhr.status == 200) {
            var users = JSON.parse(xhr.responseText);
            container_users.textContent = '';

            Object.values(users).forEach((user) => {
                let badgeUser = document.createElement('a');
                badgeUser.classList.add('badge_user');
                badgeUser.href = `https://osu.ppy.sh/users/${user.id}`;

                let badgeUserImg = document.createElement('img');
                badgeUserImg.src = `https://a.ppy.sh/${user.id}`;
                badgeUser.appendChild(badgeUserImg);

                let badgeUserUsername = document.createElement('div');
                badgeUserUsername.classList.add('badge_user_name');
                badgeUserUsername.textContent = user.name;
                badgeUser.appendChild(badgeUserUsername);

                container_users.appendChild(badgeUser);
            });
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
    var searchQuery = document.getElementById("search").value;
    var searchQuery = searchQuery.toLowerCase();

    document.getElementById("title").innerHTML = "Badges (" + data.length + ")";
    if (searchQuery == "") {
        data.forEach(async (v, i) => {
            document.getElementById("badge-" + v.id).classList.remove("hidden");
        });
        return;
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