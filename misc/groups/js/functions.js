var data;

var params = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

function updateURLParameter(url, param, paramVal) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function getPathFromUrl(url) {
    return url.split(/[?#]/)[0];
}

async function loadData() {
    openLoader("Loading...");
    document.getElementById("group").classList.add("hidden");
    document.getElementById("grouplist").classList.add("hidden");
    await loadSource("misc/groups")
    await loadSource("groups")
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "api/api.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send();
    xhr.onload = async function () {
        data = JSON.parse(xhr.responseText);
        var html = "";
        for (var x = 0; x < data.length; x++) {
            html += `<div onclick="loadGroup(${data[x]['Id']}, true)" class="groups__group-list-item" style="--colour: ${data[x]['Colour']}">
            <div class="groups__group-list-item-inner">
                <h3>${await LocalizeText(data[x]['Name'])}</h3>
                <div class="groups__group-list-item-bottom">
                    <div class="osekai__group-badge osekai__group-badge-large">${data[x]['ShortName']}</div>
                    <small>${GetStringRawNonAsync("misc\/groups", "users", [data[x]['Users'].length])}</small>
                </div>
            </div>
        </div>`;
        }
        document.getElementById("groups__list").innerHTML = html;
        checkGroupChange();
        closeLoader();
    }
}

function checkGroupChange() {
    params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    if (params.group == null) {
        document.getElementById("group").classList.add("hidden");
        document.getElementById("grouplist").classList.remove("hidden");
    } else {
        loadGroup(params.group);
    }
}

window.addEventListener('popstate', function (event) {
    checkGroupChange();
});


async function loadGroup(id, push = false) {
    console.log("Loading group: " + id);
    document.getElementById("group").classList.remove("hidden");
    document.getElementById("grouplist").classList.add("hidden");
    var found = false;

    var html = "";
    await loadSource("misc/groups")
    await loadSource("groups")
    for (var x = 0; x < data.length; x++) {
        if (data[x]['Id'] == id) {
            found = true;
            if (push) window.history.pushState('', '', updateURLParameter(window.location.href, "group", id));

            group = data[x];
            document.getElementById("group").style = "--colour: " + group['Colour'];
            document.getElementById("title").innerHTML = await LocalizeText(group['Name']);
            document.getElementById("badge").innerHTML = group['ShortName'];
            document.getElementById("users").innerHTML = GetStringRawNonAsync("misc\/groups", "users", [group['Users'].length]);
            document.getElementById("description").innerHTML = await LocalizeText(group['Description']);

            for (var y = 0; y < data[x]['Users'].length; y++) {
                user = data[x]['Users'][y];
                user['Groups'] = [];
                user['GroupsIds'] = [];
                for (var z = 0; z < data.length; z++) {
                    for (var e = 0; e < data[z]['Users'].length; e++) {
                        if (data[z]['Users'][e]['UserId'] == user['UserId']) {
                            user['Groups'].push(data[z]);
                            user['GroupsIds'].push(data[z]['Id']);
                        }
                    }
                }
                user['Groups'] = groupUtils.orderBadgeArray(user['Groups']);
                console.log(user);
                html += `
                <a class="groups__userpanel" style="--colour: ${user['Groups'][0]['Colour']}" href="https://osekai.net/profiles/?user=${user['UserId']}">
                    <img src="https://a.ppy.sh/${user['UserId']}" class="osekai__pfp-blur-bg">
                    <div class="groups__userpanel-inner">
                        <img src="https://a.ppy.sh/${user['UserId']}">
                        <div class="groups__userpanel-text">
                            <p>${user['name']}</p>
                            <div class="groups__userpanel-badges">
                            ${groupUtils.badgeHtmlFromArray(user['GroupsIds'])}
                            </div>
                        </div>
                    </div>
                </a>`;
            }
        }
    }

    if (found == false) {
        document.getElementById("group").classList.add("hidden");
        document.getElementById("grouplist").classList.remove("hidden");
        generatenotification("error", "Group not found.");
        window.history.replaceState('', '', getPathFromUrl(window.location.href));
    } else {
        document.getElementById("group__user-list").innerHTML = html;
    }
}

function goHome() {
    document.getElementById("group").classList.add("hidden");
    document.getElementById("grouplist").classList.remove("hidden");
    window.history.pushState('', '', getPathFromUrl(window.location.href));
}

loadData();