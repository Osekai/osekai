let badges = [];
var badgesLazyLoadInstance = new LazyLoad({
    elements_selector: ".badge-lazy",
    callback_error: (img, instance) => {
        if (img.getAttribute("data-src").includes('@2x.png')) {
            img.src = img.getAttribute("data-src").replace('@2x.png', '.png');
            img.setAttribute("data-src", img.src); // stops the if from falling through
        } else {
            img.src = '/badges/img/badge_default.png';
            img.onerror = null;
        }
    }
});


function getBadgeFromID(id) {
    for (let i = 0; i < badges.length; i++) {
        if (badges[i].id == id) {
            return badges[i];
        }
    }
    return null;
}

const viewTypes = ["grid_large", "list_2wide", "list_1wide", "ultra_compact"];
let viewType = "grid_large";

const sortingTypes = ["awarded_at_asc", "awarded_at_desc", "name_asc", "name_desc", "players_asc", "players_desc"]
let sortingTypes_Names = [GetStringRawNonAsync("badges", "sort.awardedAt.asc"), GetStringRawNonAsync("badges", "sort.awardedAt.desc"), GetStringRawNonAsync("badges", "sort.name.asc"), GetStringRawNonAsync("badges", "sort.name.desc"), GetStringRawNonAsync("badges", "sort.playerCount.asc"), GetStringRawNonAsync("badges", "sort.playerCount.desc")];
let currentSorting = "awarded_at_desc";

function loadData() {
    // /badges/api/getBadges.php
    strUrl = "/badges/api/getBadges.php";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", strUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.onload = function() {
        if (xhr.status == 200) {
            badges = JSON.parse(xhr.responseText);

            // geturl query
            var url = new URL(window.location.href);
            var badgeId = url.searchParams.get("badge");
            if (badgeId != null) {
                openBadge(badgeId);
            }

            fillData();
        }
    }
    xhr.send();
}

loadSource("badges").then(function() {
    sortingTypes_Names = [GetStringRawNonAsync("badges", "sort.awardedAt.asc"), GetStringRawNonAsync("badges", "sort.awardedAt.desc"), GetStringRawNonAsync("badges", "sort.name.asc"), GetStringRawNonAsync("badges", "sort.name.desc"), GetStringRawNonAsync("badges", "sort.playerCount.asc"), GetStringRawNonAsync("badges", "sort.playerCount.desc")];
    // grab these again in case the like fuckin' 10% chance they didnt load the last time hits
    // i dont know why this is an issue
    // help
    const items = document.getElementById("sort_items");
    let items_html = "";
    for (let i = 0; i < sortingTypes.length; i++) {
        items_html += `<div class="osekai__dropdown-item" onclick="changeSorting('` + sortingTypes[i] + `')" id="sort_` + sortingTypes[i] + `">` + sortingTypes_Names[i] + `</div>`;
    }
    items.innerHTML = items_html;

    document.getElementById("sort_activeItem").textContent = sortingTypes_Names[sortingTypes.indexOf(currentSorting)];
    loadData();
});

function openSortDropdown() {
    document.getElementById("sort_items").classList.toggle("osekai__dropdown-hidden");
}

function fillData() {
    let ndata;
    switch (currentSorting) {
        case "awarded_at_asc":
            // data is already sorted by awarded_at, just need to reverse it
            ndata = badges.slice().reverse();
            break;
        case "awarded_at_desc":
            // Not need to sort, its already sorted from API
            ndata = badges;
            break;
        case "name_asc":
            ndata = badges.sort((a, b) => {
                return a.name.localeCompare(b.name);
            });
            break;
        case "name_desc":
            ndata = badges.sort((a, b) => {
                return b.name.localeCompare(a.name);
            });
            break;

        case "players_asc":
            ndata = badges.sort((a, b) => {
                let count = a.users.length;
                let count2 = b.users.length;
                return count - count2;
            });
            break;

        case "players_desc":
            ndata = badges.sort((a, b) => {
                let count = a.users.length;
                let count2 = b.users.length;
                return count2 - count;
            });
            break;

        default:
            console.log('what');
            break;
    }

    let badgeList = [];
    ndata.forEach(async(badge) => {
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
        badgeImage.setAttribute("data-src", image);
        badgeImage.src = tp1x1
        badgeImage.classList.add("badge-lazy");
        badgeImage.removeAttribute("data-ll-status");
        badgeImageDiv.appendChild(badgeImage);
        badgeElement.appendChild(badgeImageDiv);

        let badgeTexts = document.createElement('div');
        badgeTexts.classList.add('badge_texts');

        let badgeName = document.createElement('div');
        badgeName.classList.add('badge_name');
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

        badgeInfo.textContent += ' • ';

        let users = JSON.parse(badge.users);
        if (users.length != 1)
            badgeInfo.innerHTML += GetStringRawNonAsync("badges", "badge.ownedBy", [users.length]);
        else
            badgeInfo.innerHTML += GetStringRawNonAsync("badges", "badge.ownedBySingular", [users.length]);

        badgeTexts.appendChild(badgeInfo);
        badgeElement.appendChild(badgeTexts);
        badgeList.push(badgeElement);
    });
    let content = document.getElementById("content");
    content.replaceChildren(...badgeList);

    runSearch();


    badgesLazyLoadInstance.update();
}


function changeSorting(type) {
    currentSorting = type;
    // wait 1 second

    let content = document.getElementById("content");
    content.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>";

    sortingTypes.forEach((sortingType) => {
        let item = document.getElementById("sort_" + sortingType);
        if (sortingType == type)
            item.classList.add("osekai__dropdown-item-active");
        else
            item.classList.remove("osekai__dropdown-item-active");
    });

    document.getElementById("sort_activeItem").textContent = sortingTypes_Names[sortingTypes.indexOf(type)];

    setTimeout(function() {
        fillData();
    }, 1);
}

let imageSize = "1x";

function setImageSize(size) {
    document.getElementById("1x").classList.remove("badges__panel-header-viewtype-active");
    document.getElementById("2x").classList.remove("badges__panel-header-viewtype-active");

    document.getElementById(size).classList.add("badges__panel-header-viewtype-active");

    imageSize = size;

    if (size == "1x") {
        let imgs = document.getElementsByClassName('badge_img');
        Object.values(imgs).forEach((element) => {
            let imgElement = element.children[0];
            imgElement.setAttribute("data-src", imgElement.getAttribute("data-src").replace('@2x.png', '.png'));
            imgElement.src = tp1x1;
            imgElement.classList.add("badge-lazy");
            imgElement.removeAttribute("data-ll-status");
        });
        badgesLazyLoadInstance.update();
    } else {
        let imgs = document.getElementsByClassName('badge_img');
        Object.values(imgs).forEach((element) => {
            let imgElement = element.children[0];
            imgElement.setAttribute("data-src", imgElement.getAttribute("data-src").replace('.png', '@2x.png'));
            imgElement.src = tp1x1;
            imgElement.classList.add("badge-lazy");
            imgElement.removeAttribute("data-ll-status");
        });
        badgesLazyLoadInstance.update();
    }

    // remember options using localStorage
    localStorage.setItem("imageSize", size);

}

function changeViewtype(type) {
    viewType = type;

    for (let i = 0; i < viewTypes.length; i++) {
        document.getElementById("viewtype-" + viewTypes[i]).classList.remove("badges__panel-header-viewtype-active");
    }
    document.getElementById("viewtype-" + type).classList.add("badges__panel-header-viewtype-active");

    localStorage.setItem("viewType", type);

    let content = document.getElementById('content');

    oldType = '';
    viewTypes.forEach(type => {
        if (content.classList.contains(type)) {
            oldType = type;
        }
    });

    // Most of the lag here comes from the browser working on the layout
    // Put it in a timeout to not block the js execution
    setTimeout(() => {
        const badgesElements = Object.values(content.children);
        content.replaceChildren(); // Emtpy content and deatach badges to DOM so editing it is faster
        if (oldType != "")
            content.classList.replace(oldType, viewType);
        else
            content.classList.add(viewType);
        badgesElements.forEach(async(b) => b.classList.replace(oldType, viewType));
        content.replaceChildren(...badgesElements);
        badgesLazyLoadInstance.update();
    }, 1);

    badgesLazyLoadInstance.update();
}

function openBadge(index) {
    let obj_name = document.getElementById("bop_name");
    let obj_desc = document.getElementById("bop_desc");
    let obj_img = document.getElementById("bop_img");
    let obj_img2 = document.getElementById("bop_img2");
    let obj_img_1x = document.getElementById("bop_img_1x");
    let obj_achieved = document.getElementById("bop_achieved");
    let obj_amount = document.getElementById("bop_amount");
    let container_users = document.getElementById("bop_users");

    let badge = getBadgeFromID(index);

    obj_name.textContent = badge.name;
    obj_desc.textContent = badge.description;
    obj_img.src = badge.image_url.replace(".png", "@2x.png");
    obj_img2.src = badge.image_url.replace(".png", "@2x.png");

    document.getElementById("1x_var").classList.remove("hidden");

    obj_img.onerror = function() {
        let obj_img = document.getElementById("bop_img");
        let obj_img2 = document.getElementById("bop_img2");

        obj_img.src = badge.image_url;
        obj_img2.src = badge.image_url;
        document.getElementById("1x_var").classList.add("hidden");
    }

    obj_img_1x.src = badge.image_url;

    let awarded_at = badge.awarded_at;
    if (awarded_at == "2013-08-07") {
        awarded_at = "long ago";
    }

    obj_achieved.innerHTML = GetStringRawNonAsync("badges", "badge.firstAchieved", [awarded_at]);
    let users = JSON.parse(badge.users);
    obj_amount.innerHTML = GetStringRawNonAsync("badges", "badge.ownedBy", [users.length]);

    // set container to loading
    container_users.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>";

    let obj_main = document.getElementById("bop_overlay");
    obj_main.classList.remove("badges__badge-overlay_hidden");
    let obj_panel = document.getElementById("bop_panel");
    obj_panel.classList.remove("badges__badge-panel_hidden");

    // api/getUsers.php?badge_id=1
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "/badges/api/getUsers.php?badge_id=" + badge.id, true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            const users = JSON.parse(xhr.responseText);
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
    xhr.send();
    // set url query to badge id
    window.history.replaceState("", "", "?badge=" + badge.id);
}

function hideOverlay() {
    // remove queyr
    URLSearchParams = window.URLSearchParams || window.location.search;
    let urlParams = new URLSearchParams(window.location.search);
    urlParams.delete("badge");
    window.history.replaceState("", "", "?" + urlParams.toString());


    let obj_main = document.getElementById("bop_overlay");
    obj_main.classList.add("badges__badge-overlay_hidden");
    let obj_panel = document.getElementById("bop_panel");
    obj_panel.classList.add("badges__badge-panel_hidden");
}

function runSearch() {
    const searchQuery = document.getElementById("search").value.toLowerCase();

    document.getElementById("title").innerHTML = "Badges (" + badges.length + ")";
    if (searchQuery == "") {
        badges.forEach(async(v, i) => {
            document.getElementById("badge-" + v.id).classList.remove("hidden");
        });
        return;
    }

    let count = 0;

    for (let i = 0; i < badges.length; i++) {
        const badge = badges[i];
        const name = badge.name.toLowerCase();
        if (name.includes(searchQuery) || badge.description.toLowerCase().includes(searchQuery)) {
            document.getElementById("badge-" + badge.id).classList.remove("hidden");
            count++;
        } else {
            document.getElementById("badge-" + badge.id).classList.add("hidden");
        }
    }

    document.getElementById("title").innerHTML = "Badges (" + count + ")";
}

if (localStorage.getItem("imageSize") != null) {
    setImageSize(localStorage.getItem("imageSize"));
}

if (localStorage.getItem("viewType") != null) {
    changeViewtype(localStorage.getItem("viewType"));
}

document.getElementById("search").addEventListener("keyup", runSearch);