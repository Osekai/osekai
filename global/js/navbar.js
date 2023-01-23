// const apps
// <style id="cardstyle">
// .osekai__apps-dropdown-applist-right-card {
//     linear-gradient(92.75deg, rgba(var(--appColour), 0.5) 0%, rgba(var(--appColour), 0.25) 100%), linear-gradient(92.75deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.25) 100%), url(/global/img/cover/rankings.jpg);
// }
// </style>

var cardstyle = document.getElementById("cardstyle");
var card_icon = document.getElementById("dropdown_card_icon") // img
var card_title = document.getElementById("dropdown_card_title") // h1
var card_content = document.getElementById("dropdown_card_content") // p
var background_image = document.getElementById("background_image");
var extra_style = document.getElementById("extra_style");

cardstyle.innerHTML = `.osekai__apps-dropdown-applist-right-card {
    background: linear-gradient(92.75deg, rgba(var(--appColour), 0.5) 0%, rgba(var(--appColour), 0.25) 100%), linear-gradient(92.75deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.25) 100%), url(/global/img/` + apps[currentlyApp]['cover'] + `.jpg);
    background-size: cover;
        background-position: center;
}`;

var card_original_bg = cardstyle.innerHTML;
var card_original_icon = card_icon.src;
var card_original_title = card_title.innerHTML;
var card_original_content = card_content.innerHTML;

//console.log(apps);


function setCardDetails(appSimpleName) {
    var app = apps[appSimpleName];

    cardstyle.innerHTML = `.osekai__apps-dropdown-applist-right-card {
        background: linear-gradient(92.75deg, rgba(var(--appColour), 0.5) 0%, rgba(var(--appColour), 0.25) 100%), linear-gradient(92.75deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.25) 100%), url(/global/img/` + app['cover'] + `.jpg);
        background-size: cover;
        background-position: center;
    }
    .osekai__apps-dropdown {
        --appColour: ` + app['color_dark'] + `;
        --appColourLight: ` + app['color'] + `;
    }
    .osekai__apps-dropdown {
        --appColour: ` + app['color_dark'] + `;
        --appColourLight: ` + app['color'] + `;
    }
    `;

    extra_style.innerHTML = `.osekai__apps-dropdown-image {
        background-image: url(/home/img/` + app['simplename'] + `.png);
        opacity: 1;
    }`;

    //https://www.osekai.net/global/img/branding/vector/<?= $a['logo']; ?>.svg
    card_icon.src = "https://www.osekai.net/global/img/branding/vector/" + app['logo'] + ".svg";
    card_title.innerHTML = "osekai <strong>" + app['simplename'] + "</strong>";
    card_content.innerHTML = app['slogan'];
}


document.getElementById("applist").onmouseout = (e) => {
    cardstyle.innerHTML = card_original_bg;
    card_icon.src = card_original_icon;
    card_title.innerHTML = card_original_title;
    card_content.innerHTML = card_original_content;
    extra_style.innerHTML += `.osekai__apps-dropdown-image {
        opacity: 0 !important;
    }`
    // after 0.4 seconds
}

var userInfo;

function loadUserDropdown() {
    let userid = nUserID;
    if (userid != -1) {
        // /api/profiles/get_user.php?id=4598966
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/api/profiles/get_user.php?id=" + userid, true);
        xhr.onreadystatechange = function () {
            if (this.readyState != 4) return;
            if (this.status != 200) return; // or whatever error handling you want

            let resp = JSON.parse(xhr.responseText);
            // amount of medals in resp['user_achievements']
            let medals = resp['user_achievements'].length;
            let club = 0;
            // possible percentage clubs: 40%, 60%, 80%, 90%, 95%
            // we have a variable called "medalAmount" which is maximum of medals
            var percentage = medals / medalAmount * 100;
            if (percentage < 40) club = "no";
            if (percentage >= 40) club = 40;
            if (percentage >= 60) club = 60;
            if (percentage >= 80) club = 80;
            if (percentage >= 90) club = 90;
            if (percentage >= 95) club = 95;
            document.getElementById("dropdown__user").classList.add("col" + club + "club");
            let perctext = percentage.toFixed(2) + "%";
            // surround all text after '.' with <span>
            perctext = perctext.replace(/\.([^.]+)/g, ".<span>$1</span>");
            document.getElementById("userdropdown_club").innerHTML = perctext
            if (club == "no") {
                /* document.getElementById("userdropdown_club").classList.add("hidden"); */
            }
            let roundedpp = resp['statistics']['pp'];
            roundedpp = roundedpp.toFixed(0);
            document.getElementById("userdropdown_pp").innerHTML = roundedpp + "pp";
            document.getElementById("userdropdown_medals").innerHTML = medals + " <span>medals</span>";
            document.getElementById("userdropdown__bar").style.width = percentage + "%";
            document.getElementById("userdropdown_texts-loading").classList.add("hidden");
            document.getElementById("userdropdown_texts").classList.remove("hidden");
            userInfo = resp;
        }
        xhr.send();
    }
    else {
        // user is not logged in
        return;
    }
}

loadUserDropdown();

function showOtherApps() {
    document.getElementById("outer-app-list").classList.add("osekai__apps-dropdown-hidden");
    document.getElementById("otherapplist").classList.remove("osekai__apps-dropdown-hidden");

    document.getElementById("dropdown__apps-mobile-base").classList.add("osekai__apps-dropdown-mobile-hidden");
    document.getElementById("dropdown__apps-mobile-other").classList.remove("osekai__apps-dropdown-mobile-hidden");
}

function hideOtherApps() {
    document.getElementById("outer-app-list").classList.remove("osekai__apps-dropdown-hidden");
    document.getElementById("otherapplist").classList.add("osekai__apps-dropdown-hidden");

    document.getElementById("dropdown__apps-mobile-base").classList.remove("osekai__apps-dropdown-mobile-hidden");
    document.getElementById("dropdown__apps-mobile-other").classList.add("osekai__apps-dropdown-mobile-hidden");
}

function open_dropdown(classname, id) {
    document.getElementById(id).classList.remove(classname);
}

function close_dropdown(classname, id) {
    document.getElementById(id).classList.add(classname);
}


function ExperimentalOff() {
    openDialog("Disable Experimental Mode", "Are you sure?", "Unless you have the expon.php link, you can't turn it back on!", "Cancel", function () {
        return;
    }, "Disable", function () {
        window.location.href = "/global/api/expoff.php";
    });
}

// <Start> Notification System
setTimeout(function () { GetNotifications(false, false) }, 1000); //Loads the Amount of Notifications on the bell icon before opening the dropdown
var NotificationBell = document.getElementById("notif__bell__button");
NotificationBell && NotificationBell.addEventListener("click", () => {
    dropdown("osekai__nav-dropdown-hidden", "dropdown__notifs", 1);
    if (!document.getElementById("dropdown__notifs").classList.contains("osekai__nav-dropdown-hidden")) {
        GetNotifications(false, true);
    }
})

ClearAll.addEventListener("click", () => {
    markRead();
})

const NOTIFICATION_SYSTEM_API_URL = "/global/api/notification_system.php"
function GetNotifications(ShowCleared, UI) {
    if (UI) document.getElementById("notification__list__v2").innerHTML = ""; //in case the xhrequest fails still get rid of the panel
    let xhr = createXHR(NOTIFICATION_SYSTEM_API_URL);
    xhr.send(`ShowCleared=${ShowCleared}`);
    xhr.onreadystatechange = function () {
        var Response = getResponse(xhr);
        if (handleUndefined(Response)) return;
        CreateNotifications(Response, UI);
    };
}

function CreateNotifications(Notifications, UI) {
    let NotificationList
    if (UI) {
        NotificationList = document.getElementById("notification__list__v2");
        document.getElementById("notification__list__v2").innerHTML = ""; // get rid of the panel again in case loading takes longer and someone rapid clicks on the icon
    }

    let nCount = 0;
    Object.keys(Notifications).forEach(function (obj) {
        if (UI) CreateNotificationItem(NotificationList, Notifications[obj]);
        nCount += 1;
    });
    document.getElementById("NotificationCountIcon").innerHTML = nCount;
    if (nCount > 0 && document.getElementById("NotificationCountIcon").classList.contains("hidden")) document.getElementById("NotificationCountIcon").classList.remove("hidden");
    document.getElementById("NotificationCount").innerHTML = GetStringRawNonAsync("navbar", "notifications.count", [nCount]);
}

function CreateNotificationItem(List, Notification) {
    console.log("Getting Notifications");
    let Outer = document.createElement("div");
    Outer.classList.add("osekai__nav-dropdown-v2-notification");

    if (Notification['Message'] == "") {
        let Upper = document.createElement("div");
        Upper.classList.add("osekai__nav-dropdown-v2-notification-upper");

        let Image = document.createElement("img");
        if (Notification['logo'] == "" || Notification['logo'] == null) {
            Image.src = "/global/img/branding/vector/osekai_light.svg";
        } else {
            Image.src = `https://www.osekai.net/global/img/branding/vector/${Notification["logo"]}.svg`;
        }

        let Message = document.createElement("p");
        Message.innerHTML = Notification["Title"];

        Upper.appendChild(Image);
        Upper.appendChild(Message);
        Outer.appendChild(Upper);
    } else {
        let Upper
        if (Notification['Link'] == "" || Notification['Link'] == null) {
            Upper = document.createElement("div");
        } else {
            Upper = document.createElement("a");
            Upper.classList.add("osekai__nav-dropdown-v2-notification-upper-clickable");
            Upper.href = Notification["Link"];
        }
        Upper.classList.add("osekai__nav-dropdown-v2-notification-upper");

        let Image = document.createElement("img");
        if (Notification['logo'] == "" || Notification['logo'] == null) {
            Image.src = "/global/img/branding/vector/osekai_light.svg";
        } else {
            Image.src = `https://www.osekai.net/global/img/branding/vector/${Notification["logo"]}.svg`;
        }

        let Title = document.createElement("p");
        Title.innerHTML = Notification["Title"];

        let DescriptionOuter = document.createElement("div");
        DescriptionOuter.classList.add("osekai__nav-dropdown-v2-notification-lower");

        let Description = document.createElement("p");
        Description.innerHTML = Notification["Message"];

        DescriptionOuter.appendChild(Description);

        Upper.appendChild(Image);
        Upper.appendChild(Title);

        Outer.appendChild(Upper);
        Outer.appendChild(DescriptionOuter);
    }

    List.appendChild(Outer);
}

function markRead() {
    let xhr = createXHR("/global/api/notification_system.php");
    xhr.send("markRead=1");
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            dropdown("osekai__nav-dropdown-hidden", "dropdown__notifs", 1);
            if (!document.getElementById("NotificationCountIcon").classList.contains("hidden")) document.getElementById("NotificationCountIcon").classList.add("hidden");
            GetNotifications(false, true);
        }
    };
}
// <End> Notification System

// #region Settings Screen
var settingUtils = {
    "genericSection": function (classname = null) {
        var outerDiv = document.createElement("div");
        outerDiv.classList.add("osekai__dropdown-settings-section");
        if (classname != null) outerDiv.classList.add(classname);
        return outerDiv;
    },
    "genericList": function (items, selectedItem, itemNameKey, parent, clickCallback, mainClassName, itemClassName, activeClassName, itemInner) {
        var section = this.genericSection(mainClassName);

        for (p in items) {
            let innerDiv = document.createElement('div');
            innerDiv.className = itemClassName;
            if (selectedItem == items[p] || selectedItem == p) innerDiv.classList.add(activeClassName);
            innerDiv.innerHTML = itemInner.replace("$1", items[p][itemNameKey]);

            let key = p;
            innerDiv.addEventListener("click", function (e) {
                parent.getElementsByClassName(activeClassName)[0].classList.remove(activeClassName);
                innerDiv.classList.add(activeClassName)
                clickCallback(key);
            });

            section.appendChild(innerDiv);
        }

        parent.appendChild(section);
    },
    "buttonList": function (items, selectedItem, itemNameKey, parent, clickCallback) {
        this.genericList(items, selectedItem, itemNameKey, parent, clickCallback, "osekai__dropdown-settings-radio-list", "osekai__dropdown-settings-radio-item", "osekai__dropdown-settings-radio-item-checked", "<span></span><p>$1</p>")
    },
    "choiceGrid": function (items, selectedItem, itemNameKey, parent, clickCallback) {
        this.genericList(items, selectedItem, itemNameKey, parent, clickCallback, "osekai__dropdown-settings-choicegrid", "osekai__dropdown-settings-choicegrid-item", "osekai__dropdown-settings-choicegrid-item-checked", "$1")
    },
    "baseCheckbox": function (name, checked, callback) {
        let innerDiv = document.createElement('div');
        innerDiv.classList.add("osekai__dropdown-settings-checkbox");
        if (checked == true || checked == "true") {
            innerDiv.classList.add("osekai__dropdown-settings-checkbox-active");
        }
        innerDiv.innerHTML = "<span><i class=\"fas fa-check\"></i></span><p>" + name + "</p>";

        innerDiv.addEventListener("click", function (e) {
            if (innerDiv.classList.contains("osekai__dropdown-settings-checkbox-active")) {
                callback(false);
                innerDiv.classList.remove("osekai__dropdown-settings-checkbox-active");
            } else {
                callback(true);
                innerDiv.classList.add("osekai__dropdown-settings-checkbox-active");
            }
        })

        return innerDiv;
    },
    "linkedCheckbox": function (name, internalName, section, defaultValue = false, callback = null) {
        var checked = false;
        if (window.localStorage.getItem(internalName) == null) {
            // sets to default value
            checked = defaultValue;
            window.localStorage.setItem(internalName, defaultValue);
        } else {
            checked = window.localStorage.getItem(internalName) == "true";
        }

        var baseCheckbox = this.baseCheckbox(name, checked, function (checked) {
            window.localStorage.setItem(internalName, checked);

            if (checked) {
                if (callback != null) callback(true);
            } else {
                if (callback != null) callback(false);
            }
        });

        section.appendChild(baseCheckbox)
    },
}

const settingsPages = [
    {
        name: "theme",
        icon: "fas fa-brush ",
        generate: async function (htmlInner) {
            settingUtils.buttonList(themes, theme, "name", htmlInner, function (key) {
                setTheme(themes[key]);
                if (key != "custom" && key != "custom-light") {
                    document.getElementById("dropdown-settings-custom-theme").classList.add("greyed");
                } else {
                    document.getElementById("dropdown-settings-custom-theme").classList.remove("greyed");
                }
                window.dispatchEvent(settingsLoadEvent);
                // dont mind this
            });;

            let themeDiv = document.createElement('div');
            themeDiv.className = 'osekai__dropdown-settings-section';
            themeDiv.id = "dropdown-settings-custom-theme"
            themeDiv.innerHTML += `<div id="customThemePicker" class="osekai__nav-dropdown-v2-split-colour-picker">
            <div class="osekai__nav-dropdown-v2-split-colour-picker-half">
                <div class="osekai__colour-picker" id="custom_colpicker_accent-dark" style="background: rgb(53, 61, 85);">
                    <input type="text" class="color-picker__source">
                </div>
                <p>Accent Dark</p>
            </div>
            <div class="osekai__nav-dropdown-v2-split-colour-picker-half">
                <div class="osekai__colour-picker" id="custom_colpicker_accent" style="background: rgb(153, 161, 185);">
                    <input type="text" class="color-picker__source">
                </div>
                <p>Accent</p>
            </div>
        </div>`;
            htmlInner.appendChild(themeDiv);

            var section = settingUtils.genericSection();
            var snowflakesDefault = false;
            var snowflakesOption = "settings_global__snowflakes-nochristmas";
            if (christmas) {
                var snowflakesDefault = true;
                var snowflakesOption = "settings_global__snowflakes";
            }
            settingUtils.linkedCheckbox("snowflakes :D", snowflakesOption, section, snowflakesDefault, snowflakes);
            htmlInner.appendChild(section);
        }
    },
    {
        name: "language",
        icon: "fas fa-globe",
        generate: async function (htmlInner) {
            let languages = {};
            for (x in locales) {
                var include = false;
                var prefix = "";
                if (locales[x]['experimental'] == true) prefix = `<p class="osekai__dropdown-item-exp">EXP</p>`;
                if (locales[x]['wip'] == true) prefix = `<p class="osekai__dropdown-item-wip">WIP</p>`;

                if (experimental == 1) include = true;
                else {
                    // if experimental is false, or isn't set
                    if (locales[x]['experimental'] == false || locales[x]['experimental'] == undefined) include = true;
                }

                if (include == true) {
                    languages[locales[x]['code']] = {
                        "name": `<img src="${locales[x]['flag']}"></img> ${prefix} <p>${locales[x]['name']}</p>`
                    };
                }
            }

            settingUtils.choiceGrid(languages, currentLocale['code'], "name", htmlInner, function (key) {
                setLanguage(key);
            });
        }
    },
    {
        name: "medals",
        icon: "oif-app-medals",
        generate: async function (htmlInner) {
            var section = settingUtils.genericSection();
            settingUtils.linkedCheckbox("completely hide medals when unobtained filter enabled", "settings_medals__hidemedalswhenunobtainedfilteron", section, false, function (enabled) {
                if (typeof filterAchieved != 'undefined') filterAchieved(true, true);
            });
            htmlInner.appendChild(section);
        }
    },
    {
        name: "profiles",
        icon: "oif-app-profiles",
        generate: async function (htmlInner) {
            var section = settingUtils.genericSection();
            settingUtils.linkedCheckbox("show medals from all modes", "settings_profiles__showmedalsfromallmodes", section, true);
            htmlInner.appendChild(section);
        }
    },
]

async function loadSettings() {
    await loadSource("navbar"); // need this
    document.getElementById("settings-page-list").innerHTML = "";
    var contentContainer = document.getElementById("settings-content");
    for (var x = 0; x < settingsPages.length; x++) {
        let page = settingsPages[x];
        document.getElementById("settings-page-list").innerHTML +=
            `<div class="osekai__dropdown-settings-page" onclick="openSettingsPage('${page.name}', this)">
            <i class="${page.icon}"></i>
            <p>${page.name}</p>
        </div>`;

        var innerDiv = document.createElement('div');
        innerDiv.className = 'osekai__dropdown-settings-page-inner';
        innerDiv.classList.add("osekai__dropdown-settings-page-inner-hidden");
        innerDiv.innerHTML = `<p class="osekai__dropdown-settings-page-header"><i onclick="showSettingsSidebarMobile()" class="fas fa-chevron-left mobile"></i> <i class="${page.icon}"></i> ${page.name}</h1>`;
        innerDiv.setAttribute("settings-page", page.name);

        var innerContent = document.createElement('div');
        innerContent.className = 'osekai__dropdown-settings-page-content';
        await page.generate(innerContent);
        innerDiv.appendChild(innerContent);
        contentContainer.appendChild(innerDiv);
    }
    window.dispatchEvent(settingsLoadEvent);
    if (!mobile) {
        // TODO: open a default page. need to rewrite the way Active works beforehand or find a way to find the item
    }
}

function openSettingsPage(name, sidebar) {
    var all = document.querySelectorAll("*[settings-page]");
    for (var x = 0; x < all.length; x++) {
        all[x].classList.add("osekai__dropdown-settings-page-inner-hidden");
    }
    document.querySelector("[settings-page='" + name + "']").classList.remove("osekai__dropdown-settings-page-inner-hidden");


    var sidebarButtons = document.getElementsByClassName("osekai__dropdown-settings-page")
    for (var x = 0; x < sidebarButtons.length; x++) {
        sidebarButtons[x].classList.remove("osekai__dropdown-settings-page-active");
    }
    sidebar.classList.add("osekai__dropdown-settings-page-active");
    document.getElementById("dropdown-settings-new").classList.add("osekai__dropdown-settings-sidebar-collapsed");
}
function showSettingsSidebarMobile() {
    document.getElementById("dropdown-settings-new").classList.remove("osekai__dropdown-settings-sidebar-collapsed");
}



window.addEventListener('load', async function () {
    document.getElementsByClassName("osekai__dropdown-settings-loader")[0].remove();
    await loadSettings();
});
// #endregion