var bLoggedIn = (typeof nUserID !== 'undefined' && nUserID.toString() !== "-1");

// after 0.6 seconds
setTimeout(function () {
    var panels = document.getElementsByClassName("osekai__panel-container");
    for (var i = 0; i < panels.length; i++) {
        if (!panels[i].classList.contains("hidden")) {
            panels[i].classList.add("hidden");
            panels[i].classList.add("wait-forload");
        }
    }
}, 10);

setTimeout(function () {
    var panels = document.getElementsByClassName("osekai__panel-container");
    for (var i = 0; i < panels.length; i++) {
        if (panels[i].classList.contains("wait-forload")) {
            panels[i].classList.remove("hidden");
            panels[i].classList.remove("wait-forload");
        }
    }
}, 600);
// this is to force the load animation to play again

//function enableLightMode() {
//    document.getElementById("css_cont").innerHTML += '<link id="light" rel="stylesheet" type="text/css" href="/global/css/light.css">';
//    document.getElementById("css_cont").innerHTML += '<link id="relative_light" rel="stylesheet" type="text/css" href="css/light.css">';
//}
//
//function disableLightMode() {
//    if (document.getElementById("light")) {
//        console.log("removing stuff");
//        document.getElementById("css_cont").innerHTML = "";
//    }
//}
//
//function switchLightMode() {
//    if (light) {
//        disableLightMode();
//    } else {
//        enableLightMode();
//    }
//

function OpenSettingsDropdown(id) {
    var dropdown = document.getElementById(id);
    dropdown.classList.toggle("osekai__dropdown-hidden");
}
var themes;
var theme;

themes = {
    "light": {
        "internal": "light",
        "name": "loading...",
        "css": ["light.css"],
    },
    "dark": {
        "internal": "dark",
        "name": "loading...",
        "css": "none",
    },
    "ultradark": {
        "internal": "ultradark",
        "name": "loading...",
        "css": ["ultradark.css"],
    },
    "colddark": {
        "internal": "colddark",
        "name": "loading...",
        "css": ["colddark.css"],
    },
    "flatwhite": {
        "internal": "softwhite",
        "name": "[experimental] Soft White",
        "css": ["light.css", "softwhite.css"],
        "experimental": true
    },
    "system": {
        "internal": "system",
        "name": "loading...",
        "css": "none",
    },
    "custom": {
        "internal": "custom",
        "name": "custom colours",
        "css": "none"
    },
    "custom-light": {
        "internal": "custom-light",
        "name": "custom colours (Light Mode)",
        "css": ["light.css"]
    }
}

async function loadThemes() {
    //themes = {
    //    "light": {
    //        "internal": "light",
    //        "name": await GetStringRaw("navbar", "settings.global.theme.light"),
    //        "css": "/global/css/light.css",
    //        "rel": "css/light.css"
    //    },
    //    "dark": {
    //        "internal": "dark",
    //        "name": await GetStringRaw("navbar", "settings.global.theme.dark"),
    //        "css": "none",
    //    },
    //    "ultradark": {
    //        "internal": "ultradark",
    //        "name": await GetStringRaw("navbar", "settings.global.theme.ultraDark"),
    //        "css": "/global/css/ultradark.css",
    //        "rel": "css/ultradark.css"
    //    },
    //    "colddark": {
    //        "internal": "colddark",
    //        "name": await GetStringRaw("navbar", "settings.global.theme.coldDark"),
    //        "css": "/global/css/colddark.css",
    //        "rel": "css/colddark.css"
    //    },
    //    "system": {
    //        "internal": "system",
    //        "name": await GetStringRaw("navbar", "settings.global.theme.system"),
    //        "css": "none",
    //    }
    //}
    themes["light"].name = await GetStringRaw("navbar", "settings.global.theme.light");
    themes["dark"].name = await GetStringRaw("navbar", "settings.global.theme.dark");
    themes["ultradark"].name = await GetStringRaw("navbar", "settings.global.theme.ultraDark");
    themes["colddark"].name = await GetStringRaw("navbar", "settings.global.theme.coldDark");
    themes["system"].name = await GetStringRaw("navbar", "settings.global.theme.system");

    loadThemesDropdown();
}

theme = themes["system"];


loadThemesDropdown();
loadThemes();


//console.log(themes);
//console.log(theme);

function loadThemesDropdown() {
    var dropdown = document.getElementById("dropdown__themes");
    dropdown.innerHTML = "";
    for (var i in themes) {
        if (themes[i].experimental == true && experimental == 0) continue;
        var div = document.createElement("div");
        div.classList.add("osekai__dropdown-item");
        div.innerHTML = themes[i].name;
        div.setAttribute("onclick", "setTheme('" + themes[i].internal + "')");
        dropdown.appendChild(div);
    }
    updateThemesDropdown();
}


function updateThemesDropdown() {
    document.getElementById("dropdown__themes-text").innerHTML = theme.name;
    var dropdown = document.getElementById("dropdown__themes");
    // give the right element osekai__dropdown-item-active
    for (var i in dropdown.children) {
        try {
            if (dropdown.children[i].innerHTML == theme.name) {
                dropdown.children[i].classList.add("osekai__dropdown-item-active");
            } else {
                dropdown.children[i].classList.remove("osekai__dropdown-item-active");
            }
        } catch (e) {
            // it's fine, ignore
        }
    }
}


function setTheme(stheme) {
    if (typeof stheme == "string") {
        for (var i in themes) {
            if (themes[i].internal == stheme) {
                theme = themes[i];
                break;
            }
        }
    } else {
        theme = stheme;
    }

    updateTheme();
    saveSettings();
    updateThemesDropdown();
}

var customTheme = {
    accent_dark: [53, 61, 85],
    accent: [153, 161, 185]
}

if (window.localStorage.getItem('accent_dark') != null) {
    customTheme.accent_dark = window.localStorage.getItem('accent_dark').split(",");
    customTheme.accent = window.localStorage.getItem('accent').split(",");
    console.log(window.localStorage.getItem('accent_dark'));
    console.log(customTheme.accent_dark);
}

var accentDark_picker = document.getElementById("custom_colpicker_accent-dark");
var accent_picker = document.getElementById("custom_colpicker_accent");

var cp_accentdark = null;
var cp_accent = null;

function updateTheme() {
    //console.log("switching to: " + theme);
    document.getElementById("custom_theme_container").innerHTML = "";
    console.log("theme is " + theme.internal);
    if (theme.internal == "system") {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // dark mode
        } else {
            // light mode
            document.getElementById("css_cont").innerHTML = "<link id='" + theme + "' rel='stylesheet' type='text/css' href='/global/css/" + themes["light"].css[0] + "'>";
            document.getElementById("css_cont").innerHTML += "<link id='" + theme + "' rel='stylesheet' type='text/css' href='css/" + themes["light"].css[0] + "'>";
        }
    }
    else {
        document.getElementById("css_cont").innerHTML = "";
        if (theme["css"] != "none") {
            for (var i in theme['css']) {
                document.getElementById("css_cont").innerHTML += "<link id='" + theme + "' rel='stylesheet' type='text/css' href='/global/css/" + theme["css"][i] + "?v=" + version + "'>";
                document.getElementById("css_cont").innerHTML += "<link id='" + theme + "_relative' rel='stylesheet' type='text/css' href='css/" + theme["css"][i] + "?v=" + version + "'>";
            }
        }
    }

    if (theme.internal == "custom" || theme.internal == "custom-light") {
        document.getElementById("custom_theme_container").innerHTML = `html{
            --accentdark: ${customTheme.accent_dark} !important;
            --accent: ${customTheme.accent} !important;
        }`;
        window.localStorage.setItem("accent_dark", customTheme.accent_dark);
        window.localStorage.setItem("accent", customTheme.accent);
        document.getElementById("customThemePicker").classList.remove("hidden");
        if (cp_accent == null) {
            console.log("making cp with default " + customTheme.accent_dark)
            cp_accentdark = new newColourPicker("custom_colpicker_accent-dark", function (col) {
                customTheme.accent_dark = col;
                updateTheme();
            }, customTheme.accent_dark);

            cp_accent = new newColourPicker("custom_colpicker_accent", function (col) {
                customTheme.accent = col;
                updateTheme();
            }, customTheme.accent);
        }

    } else {
        document.getElementById("customThemePicker").classList.add("hidden");
    }
}

//enableLightMode();

// XHR


localStorage.setItem("url", location.href);
window.addEventListener("hashchange", function () {
    localStorage.setItem("url", location.href);
});
window.addEventListener("popstate", function () {
    localStorage.setItem("url", location.href);
});


function navflip() {
    // flips the little arrow under the logo
    // TODO: it's fucked when clicking off oops

    var x = document.getElementById("nav_chevron");
    x.classList.toggle("nav_chevron_flipped");
}



// ported from comment_system


var navheight = 0;
// next part is because i got bored
function positionNav() {
    navheight = document.getElementsByClassName("osekai__navbar-container")[0].clientHeight.toString();
    var extraheight = navheight - 59;
    var body = document.body;
    body.setAttribute("style", "--navheight: " + navheight + "px; --extraheight: " + extraheight + "px;");
}
positionNav();
window.onresize = positionNav;
window.onload = positionNav();
//changeOptions
var cbNotifsNB = document.getElementById("styled-checkbox-notifs");
cbNotifsNB && cbNotifsNB.addEventListener("change", () => {
    let xhr = createXHR("/global/api/member_information.php");
    xhr.send("update_experimental=1");
    toggleExperimental();
    pushNotification("Welcome", "Welcome to Osekai. Enjoy your stay :)", "Welcome", "");
})

// experimental features
function toggleExperimental() {
    positionNav();
}

// notif system
var btnNotifsBellNB = document.getElementById("notif__bell__button");
btnNotifsBellNB && btnNotifsBellNB.addEventListener("click", () => {
    dropdown("osekai__nav-dropdown-hidden", "dropdown__notifs", 1);
    if (!document.getElementById("dropdown__notifs").classList.contains("osekai__nav-dropdown-hidden")) {
        getNotifications();
        GetNotifications(false);
    }
})

function getNotifications() {
    document.getElementById("notification__list").innerHTML = "";
    let xhr = createXHR("/global/api/notification_system.php");
    xhr.send("getNotifs=1");
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) {
            notificationCleanup();
            return;
        };
        let nCounter = 0;
        Object.keys(oResponse).forEach(function (obj) {
            createNotificationItem(oResponse[obj]);
            nCounter = nCounter + 1;
        });
        if (document.getElementById("notification__dot")) document.getElementById("notification__dot").remove();
        if (nCounter > 0) {
            let oDot = document.createElement("div");
            oDot.id = "notification__dot";
            oDot.classList.add("osekai__notification-dot");
            document.getElementById("notif__bell__button").appendChild(oDot);
        }
        document.getElementById("notification__counter").innerHTML = nCounter;
    };
}

function notificationCleanup() {
    if (document.getElementById("notification__dot")) document.getElementById("notification__dot").remove();
    document.getElementById("notification__counter").innerHTML = "0";
}

function pushNotification(strTitle, strMessage, strSystemID = "", strHTML = "") {
    let xhr = createXHR("/global/api/notification_system.php");
    xhr.send("notifTitle=" + strTitle + "&pushNotif=" + strMessage + "&sysID=" + strSystemID + "&notifHTML=" + strHTML);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            getNotifications();
        }
    };
}

function pushNotificationToUser(strUser, strTitle, strMessage, strSystemID = "", strHTML = "") {
    let xhr = createXHR("/global/api/notification_system.php");
    xhr.send("userID=" + strUser + "&notifTitle=" + strTitle + "&pushNotif=" + strMessage + "&sysID=" + strSystemID + "&notifHTML=" + strHTML);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            getNotifications();
        }
    };
}

function createNotificationItem(oNotification) {
    let oItem = document.createElement("div");
    oItem.classList.add("osekai__notifications-list-item");
    oItem.innerHTML = '<div class="osekai__notifications-list-item-header">' +
        '<h1 class="osekai__notifications-title">' + oNotification['Title'] + '</h1>' +
        '<div class="osekai__notification-close" onclick="markRead(' + oNotification['ID'] + ');">' +
        '<i class="fas fa-times-circle"></i>' +
        '</div>' +
        oNotification['HTML'] +
        '</div>' +
        '<p class="osekai__notifications-description">' +
        oNotification['Message'] +
        '</p>';
    document.getElementById("notification__list").appendChild(oItem);
}

function markRead(nID) {
    let xhr = createXHR("/global/api/notification_system.php");
    xhr.send("markRead=1&id=" + nID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            getNotifications();
        }
    };
}

function openLoader(text = "Placeholder text...") {
    document.getElementById("loading_overlay").innerHTML += `<div id="global_loading_overlay" class="osekai__loading-overlay">
<svg viewBox="0 0 50 50" class="spinner">
    <circle class="ring" cx="25" cy="25" r="22.5"></circle>
    <circle class="line" cx="25" cy="25" r="22.5"></circle>
</svg>
<h1 class="osekai__h1">` + text + `</h1>
</div>`;
}

function closeLoader() {
    if (document.getElementById("global_loading_overlay")) {
        document.getElementById("global_loading_overlay").remove();
    }
}

window.openDialog = function (title, header, message, button1, b1Callback, button2 = "", b2Callback = function () { }) {
    html = `<div class="osekai__overlay"><section class="osekai__panel osekai__overlay__panel">
    <div class="osekai__panel-header">
        <p>` + title + `</p>
    </div>
    <div class="osekai__panel-inner">
        <p class="osekai__popup-ifo1">` + header + `</p>
        <p class="osekai__popup-ifo2">` + message + `</p>
        <div class="osekai__flex_row">
            <a id="glb_tmp_cancel" class="osekai__button">Cancel</a>
            <div class=" osekai__left osekai__center-flex-row">
                <a id="glb_tmp_button1" class=" osekai__button">` + button1 + `</a>`;

    if (button2 != "") {
        html += `<a id="glb_tmp_button2" class="osekai__button">` + button2 + `</a>`;
    }

    html += `</div>
            </div>
        </div>
    </section></div>`;

    document.getElementById("other_overlays").innerHTML += html;

    document.getElementById("glb_tmp_button1").onmousedown = function () {
        document.getElementById("other_overlays").innerHTML = "";
        b1Callback();
    }
    if (button2 != "") {
        document.getElementById("glb_tmp_button2").onmousedown = function () {
            document.getElementById("other_overlays").innerHTML = "";
            b2Callback();
        }
    }
    document.getElementById("glb_tmp_cancel").onmousedown = function () {
        document.getElementById("other_overlays").innerHTML = "";
    }
}

//countries


function gracefullyExit() {
    //document.body.classList.add("osekai__loadnewpage");

    var root = document.getElementsByTagName('html')[0]; // '0' to assign the first (and only `HTML` tag)
    root.classList.add("osekai__loadnewpage");

    setTimeout(function () {
        root.innerHTML += `<div class="osekai__loadnewpage_text"><p>Loading Page...</p></div>`;
        root.classList.add("osekai__loadnewpage_over2s");
        root.classList.remove("osekai__loadnewpage");
    }, 1000);
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

if (getCookie("fromLegacy") == 1) {
    setTimeout(function () {
        document.getElementById("welcome_panel").classList.remove("osekai__eclipse-welcome-hidden");
    }, 1200);
    document.cookie = "fromLegacy=0; expires=Thu, 18 Dec 2021 12:00:00 UTC; path=/";
}

function closeWelcomePanel() {
    document.getElementById("welcome_panel").classList.add("osekai__eclipse-welcome-hidden");
}

function AddSettingCheckbox(id, internalName, defaultValue, optionExperimental = false, callback = null) {
    if(optionExperimental == true && experimental != 1) return;
    if (window.localStorage.getItem(internalName) == null) {
        // sets to default value
        document.getElementById(id).checked = defaultValue;
        window.localStorage.setItem(internalName, defaultValue);
    } else {
        document.getElementById(id).checked = window.localStorage.getItem(internalName) == "true";
    }

    document.getElementById(id).addEventListener('change', (event) => {
        if (event.currentTarget.checked) {
            if(callback != null) callback(true);
        } else {
            if(callback != null) callback(false);
        }
        window.localStorage.setItem(internalName, event.currentTarget.checked);
    })
}

AddSettingCheckbox("settings_profiles__showmedalsfromallmodes", "profiles__showmedalsfromallmodes", true)
AddSettingCheckbox("settings_medals__hidemedalswhenunobtainedfilteron", "medals__hidemedalswhenunobtainedfilteron", false, false, function (enabled) {
    var filtered = document.getElementsByClassName("medals__medal-filtered");
    for(var x = 0; x < filtered.length; x++) {
        var parent = filtered[x].parentElement;
        if(enabled) {
            parent.classList.add("hidden");
        } else {
            parent.classList.remove("hidden");
        }
    }
});

//document.getElementById("settings_profiles__showmedalsfromallmodes").checked = true;

function defaultSettings() {
    if (window.localStorage.getItem('theme') == null) {
        setTheme("system");
        window.localStorage.setItem('theme', "system");
    }
}

function saveSettings() {
    window.localStorage.setItem('theme', theme["internal"]);
}

function loadSettings() {
    setTheme(window.localStorage.getItem('theme'));
}

defaultSettings();
loadSettings();

function setLanguage(code) {
    // /api/setLanguage?language=en
    var xhttp = new XMLHttpRequest();

    GetStringRaw("medals", "searchbar.placeholder").then(function (text) {
        //console.log(text);
    });

    GetStringRaw("general", "language.switch").then(function (text) {
        openLoader(text);
        hide_dropdowns();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                /// reload
                location.reload();
            }
        };
        xhttp.open("GET", "/api/setLanguage?language=" + code, true);
        xhttp.send();
    });
}

function getRole(id) {
    for (var i = 0; i < roles.length; i++) {
        if (roles[i].RoleID == id) {
            return roles[i];
        }
    }
    return false;
}

function copy(text) {
    var input = document.createElement('textarea');
    input.innerHTML = text;
    document.body.appendChild(input);
    input.select();
    var result = document.execCommand('copy');
    document.body.removeChild(input);
    return result;
}

function cantContactOsu() {
    document.getElementById("cantContactOsu").classList.remove("hidden");
    positionNav();
}


let lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
let active = false;



window.addEventListener('click', function (e) {
    if (!e.target.classList.contains("osekai__dropdown") && !e.target.classList.contains("osekai__dropdown-item") && !e.target.classList.contains("osekai__dropdown-opener") && (e.target.closest(".osekai__dropdown-opener") == null)) {
        document.querySelectorAll(".osekai__dropdown").forEach((colItems) => {
            colItems.classList.add("osekai__dropdown-hidden");
        });
    }
});

function strip(html) {
    let doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.body.textContent || "";
}

function osekaiScrollTo(elem) {
    // manages scrolling issue with navbar
    const y = elem.getBoundingClientRect().top + window.pageYOffset - navheight - 18; // some extra padding, looks nicer
    window.scrollTo({ top: y, behavior: "smooth" });
}

function linkify(inputText) {
    // death
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}

function mutation() {
    // wait 0.1 seconds
    setTimeout(function () {
        var collapsablePanels = document.getElementsByClassName("osekai__panel-collapsable");
        for (var x = 0; x < collapsablePanels.length; x++) {
            if (!collapsablePanels[x].classList.contains("osekai__panel-collapsable-initialized")) {
                collapsablePanels[x].classList.add("osekai__panel-collapsable-initialized");
                let el = collapsablePanels[x];
                collapsablePanels[x].querySelector(".fa-chevron-down").addEventListener("click", function () {
                    el.classList.toggle("osekai__panel-collapsable-collapsed")
                });
            }
        }
    }, 100);
}

// replace with tippy every time DOM updates using mutation observer
var mutationObserver = new MutationObserver(function (mutations) {
    mutation();
});

mutationObserver.observe(document.body, {
    childList: true,
    subtree: true
});

mutation();

var alertTypes = {
    "default": {
        "template": `<a>
        <div class="osekai__navbar-alert">
            <div class="osekai__navbar-alert-inner">
                <div class="osekai__navbar-alert-inner-text">
                    <p>
                        {text}
                    </p>
                </div>
                <div class="osekai__navbar-alert-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        </a>`
    },
    "warning": {
        "template": `<a>
        <div class="osekai__navbar-alert osekai__navbar-alert-warning"> 
            <div class="osekai__navbar-alert-inner">
                <div class="osekai__navbar-alert-inner-text">
                    <i class="fas fa-exclamation-triangle osekai__navbar-alert-glowing-icon"></i>
                    <p>
                        {text}
                    </p>
                </div>
                <div class="osekai__navbar-alert-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div></a>`
    }
}

function closeAlert(alert, el) {
    console.log("deleting " + alert['Id']);
    el.remove();
    positionNav();

    var closedAlerts = []; // array of closed IDs
    if (localStorage.getItem("closedAlerts")) {
        closedAlerts = JSON.parse(localStorage.getItem("closedAlerts"));
    }

    closedAlerts.push(alert['Id']);

    localStorage.setItem("closedAlerts", JSON.stringify(closedAlerts))
}

function getAlerts() {
    var container = document.getElementById("alerts_container");
    container.classList.add("hidden");
    container.innerHTML = "";

    var closedAlerts = []; // array of closed IDs
    if (localStorage.getItem("closedAlerts")) {
        closedAlerts = JSON.parse(localStorage.getItem("closedAlerts"));
    }

    let xhr = createXHR("/api/alerts.php?app=" + nAppId);
    xhr.send();
    xhr.onload = function () {
        console.log(xhr.responseText);
        var oResponse = JSON.parse(xhr.responseText);
        for (var x = 0; x < oResponse.length; x++) {
            let alert = oResponse[x];
            if (!closedAlerts.includes(alert['Id'])) {
                shown = true;
                var type = "default";
                if (alert['Type'] != "") type = alert['Type'];
                var template = alertTypes[type]['template'];
                template = template.replace("{text}", alert['Text']);
                let htmlObject = document.createElement('div');
                htmlObject.innerHTML = template;
                htmlObject.id = "alert_" + alert['Id'];
                if(alert['Link'] != null && alert['Link'] != "") {
                    htmlObject.querySelector(".osekai__navbar-alert").classList.add("osekai__navbar-alert-clickable")
                    htmlObject.querySelector("a").href = alert['Link'];
                }
                if (alert['Permanent'] == 0) {
                    htmlObject.querySelector(".osekai__navbar-alert-close").addEventListener("click", (event) => {
                        closeAlert(alert, htmlObject);
                    });
                } else {
                    htmlObject.querySelector(".osekai__navbar-alert-close").remove();
                }
                container.appendChild(htmlObject);
                container.classList.remove("hidden");
            }
        }
        positionNav();
    };

}
document.addEventListener("DOMContentLoaded", function () {
    getAlerts();
});