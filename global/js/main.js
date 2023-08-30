var bLoggedIn = (typeof nUserID !== 'undefined' && nUserID.toString() !== "-1");

var mobile = false;
window.mobile = mobile;

window.addEventListener('resize', checkMobile);
checkMobile();

function checkMobile() {
    if (window.innerWidth >= 900) {
        if (window.mobile == true) {
            // we do this check to not spam the console when the user is not on a mobile device
            console.log("moved to desktop");
        }
        mobile = false;
    }

    if (window.innerWidth < 900) {
        if (mobile == false) {
            // we do this check to not spam the console when the user is not on a desktop device
            console.log("moved to mobile");
        }
        mobile = true;
    }
}

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

var colours = {
    RGBToHSL: function (r, g, b) {
        var oldR = r;
        var oldG = g;
        var oldB = b;

        r /= 255;
        g /= 255;
        b /= 255;

        var max = Math.max(r, g, b);
        var min = Math.min(r, g, b);

        var h;
        var s;
        var l = (max + min) / 2;
        var d = max - min;

        if (d == 0) {
            h = s = 0; // achromatic
        } else {
            s = d / (1 - Math.abs(2 * l - 1));

            switch (max) {
                case r:
                    h = 60 * (((g - b) / d) % 6);
                    if (b > g) {
                        h += 360;
                    }
                    break;

                case g:
                    h = 60 * ((b - r) / d + 2);
                    break;

                case b:
                    h = 60 * ((r - g) / d + 4);
                    break;
            }
        }

        return [Math.round(h, 2), Math.round(s * 100, 2), Math.round(l * 100, 2)];
        // don\'t question the multiplications, it works
    }
}

var theme;

const themes = {
    "colourful": {
        "internal": "colourful",
        "name": "loading...",
        "css": "none",
    },
    "dark": {
        "internal": "dark flat",
        "name": "Dark",
        "css": ["themes/flat-dark.css"],
    },
    "light": {
        "internal": "light flat",
        "name": "Light",
        "css": ["themes/flat-dark.css", "themes/flat-light.css"],
    },
    "gruvbox-dark": {
        "internal": "gruvbox dark",
        "name": "Gruvbox",
        "css": ["themes/flat-dark.css", "themes/flat-gruvbox-dark.css"],
    },
    "nord-dark": {
        "internal": "nord",
        "name": "Nord",
        "css": ["themes/flat-dark.css", "themes/flat-nord.css"],
    },
    "catppuccin-dark": {
        "internal": "catppuccin dark",
        "name": "Catppuccin",
        "css": ["themes/flat-dark.css", "themes/flat-catppuccin.css"],
    },
    "ultradark": {
        "internal": "Ultra Dark",
        "name": "loading...",
        "css": ["themes/flat-dark.css", "themes/flat-ultradark.css"]
    },
    /* "flatwhite": {
        "internal": "softwhite",
        "name": "[experimental] Soft White",
        "css": ["themes/light.css", "softwhite.css"],
        "experimental": true
    }, */
    "lightweight": {
        "internal": "lightweight",
        "name": "lightweight theme",
        "css": ['themes/lightweight.css']
    },
    "custom": {
        "internal": "custom",
        "name": "loading...",
        "css": "none"
    }
}

async function loadThemes() {
    themes["colourful"].name = await GetStringRaw("navbar", "settings.theme.colourful");
    themes["light"].name = await GetStringRaw("navbar", "settings.theme.light");
    themes["dark"].name = await GetStringRaw("navbar", "settings.theme.dark");
    themes["ultradark"].name = await GetStringRaw("navbar", "settings.theme.ultraDark");

    themes["custom"].name = await GetStringRaw("navbar", "settings.theme.custom");
    themes["lightweight"].name = await GetStringRaw("navbar", "settings.theme.lightweight");
}

theme = themes["colourful"];
loadThemes();

function setTheme(stheme) {
    console.log("setting theme to " + stheme);
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

const settingsLoadEvent = new Event('settings-load');

function generateCustomThemeVars(accent, accentDark, valueOffsetOffset = 0, valueOffsetOffsetDark = 0) {
    var accentDark_split = String(accentDark).split(",");
    var accent_split = String(accent).split(",");
    var accentDark_hsl = colours.RGBToHSL(accentDark_split[0], accentDark_split[1], accentDark_split[2]);
    var accent_hsl = colours.RGBToHSL(accent_split[0], accent_split[1], accent_split[2]);
    console.log("$(*(*" + accentDark);
    return `--accentdark: ${accentDark} !important;
            --accent: ${accent} !important;
            
            --accentdark_hue: ${accentDark_hsl[0]}deg;
            --accent_hue: ${accent_hsl[0]}deg;
            --accentdark_saturation: ${accentDark_hsl[1]}%;
            --accent_saturation: ${accent_hsl[1]}%;
            --accentdark_value: ${accentDark_hsl[2]}%;
            --accent_value: ${accent_hsl[2]}%;

            --accentdark_valueoffset: ${(accentDark_hsl[2]) / 45 + 0.2 + valueOffsetOffsetDark};
            --accent_valueoffset: ${(accentDark_hsl[2] / 50) + 0.2 + valueOffsetOffset};`;
}

function updateTheme() {
    document.getElementById("custom_theme_container").innerHTML = "";

    document.getElementById("css_cont").innerHTML = "";
    if (theme["css"] != "none") {
        for (var i in theme['css']) {
            document.getElementById("css_cont").innerHTML += "<link id='" + theme + "' rel='stylesheet' type='text/css' href='/global/css/" + theme["css"][i] + "?v=" + version + "'>";
            document.getElementById("css_cont").innerHTML += "<link id='" + theme + "_relative' rel='stylesheet' type='text/css' href='css/" + theme["css"][i] + "?v=" + version + "'>";
        }
    }
    if (typeof theme["customAccent"] != 'undefined') {

        document.getElementById("custom_theme_container").innerHTML = `body {` + generateCustomThemeVars(theme["customAccent"].light, theme["customAccent"].dark, theme["customAccent"].lightOffset, theme["customAccent"].darkOffset) + `}`
    }

    if (theme.internal == "custom" || theme.internal == "custom-light") {
        document.getElementById("custom_theme_container").innerHTML = `body {${generateCustomThemeVars(customTheme.accent, customTheme.accent_dark)}}`

        // NOTE: i can't test the accent_valueoffset yet since the light mode doesn't support the new HSL colours just yet.
        // this probably will look weird when that's finished since the values are tailored for dark mode instead.

        // NOTE 2: this'd look better if "valueoffset" was actually an offset and not just a multiplication
        // of the base value, but sadly that's not a thing so we have to live with not actually giving
        // the user the brightness of the colour they've selected. fine, it works, but bit annoying
        window.localStorage.setItem("accent_dark", customTheme.accent_dark);
        window.localStorage.setItem("accent", customTheme.accent);
        window.addEventListener('settings-load', function () {
            document.getElementById("dropdown-settings-custom-theme").classList.remove("greyed");
            if (cp_accent == null) {
                cp_accentdark = new newColourPicker("custom_colpicker_accent-dark", function (col) {
                    customTheme.accent_dark = col;
                    updateTheme();
                }, customTheme.accent_dark);

                cp_accent = new newColourPicker("custom_colpicker_accent", function (col) {
                    customTheme.accent = col;
                    updateTheme();
                }, customTheme.accent);
            }
        });
    } else {
        window.addEventListener('settings-load', function () {
            document.getElementById("dropdown-settings-custom-theme").classList.add("greyed");
        });
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

window.openDialog = function (title, header, message, buttons = [], content = null) {
    var modal_overlay = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay osekai__modal-overlay-bland osekai__modal-overlay--hidden" });
    var modal_overlay_close_layer = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay-closelayer" });
    modal_overlay.appendChild(modal_overlay_close_layer);

    var modal_overlay_panel = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay-panel" });
    var modal_overlay_panel_top = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay-panel-top" });
    var modal_overlay_panel_bottom = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay-panel-bottom" });

    modal_overlay_panel.appendChild(modal_overlay_panel_top);
    modal_overlay_panel.appendChild(modal_overlay_panel_bottom);

    var title = Object.assign(document.createElement("h1"), { innerText: title });
    var sub = Object.assign(document.createElement("h3"), { innerText: header });
    var message = Object.assign(document.createElement("p"), { innerText: message });

    modal_overlay_panel_bottom.appendChild(title);
    modal_overlay_panel_bottom.appendChild(sub);
    modal_overlay_panel_bottom.appendChild(message);

    function close() {
        modal_overlay.remove();
    }

    modal_overlay_close_layer.addEventListener("click", function () { close() });

    if (content != null)
        modal_overlay_panel_bottom.appendChild(content);

    var button_container = Object.assign(document.createElement("div"), { className: "osekai__modal-overlay-buttons" });

    for (let button of buttons) {
        let buttonEl = document.createElement("div");
        buttonEl.className = "osekai__button";
        if (button.highlighted == true) {
            buttonEl.classList.add("osekai__button-highlighted");
        }
        buttonEl.innerHTML = button.text;
        buttonEl.addEventListener("click", button.callback);
        buttonEl.addEventListener("click", close);

        button_container.appendChild(buttonEl);
    }

    modal_overlay_panel_bottom.appendChild(button_container);

    modal_overlay_panel.appendChild(modal_overlay_panel_top);
    modal_overlay_panel.appendChild(modal_overlay_panel_bottom);

    modal_overlay.appendChild(modal_overlay_panel);

    console.log("appending...?");

    document.body.appendChild(modal_overlay);
    setTimeout(function () {
        // need this so it plays the animation... UGHHHH
        modal_overlay.classList.remove("osekai__modal-overlay--hidden");
    }, 100);
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

var snowflakesCreated = false;

function snowflakes(enabled) {
    console.log("SNOWFLAKES: " + enabled);
    if (enabled == true || enabled == "true") {
        if (snowflakesCreated == false) {
            document.getElementById("snowflakes").innerHTML = "";

            snowflakesCreated = true;
            var styleContainer = document.createElement("style");
            for (var x = 0; x < 40; x++) {
                var delay1 = Math.random() * 10;
                var delay2 = Math.random() * 10;
                var position = Math.random() * 100;

                styleContainer.innerHTML += `.snowflake:nth-of-type(${x}) {
                    left: ${position}%;
                    -webkit-animation-delay: ${delay1}s, ${delay2}s;
                    animation-delay: ${delay1}s, ${delay2}s
                }`;
                document.getElementById("snowflakes").innerHTML += `<div class="snowflake">
                <i class="fas fa-snowflake"></i>
            </div>`;
            }

            document.getElementById("snowflakes").appendChild(styleContainer);
        }
        document.getElementById("snowflakes").classList.remove("hidden");
    } else {
        document.getElementById("snowflakes").classList.add("hidden");
    }
}


// the reason for this, is so that during christmas the option can be stored in a different place
// so that if you have never turned them on, during christmas they'll auto-turn on
var snowflakesDefault = false;
var snowflakesOption = "settings_global__snowflakes-nochristmas";
if (christmas) {
    var snowflakesDefault = true;
    var snowflakesOption = "settings_global__snowflakes";
}


//document.getElementById("settings_profiles__showmedalsfromallmodes").checked = true;

function defaultSettings() {
    if (window.localStorage.getItem('theme') == null) {
        console.log("defaulting theme");
        setTheme("colourful");
        window.localStorage.setItem('theme', "colourful");
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
    console.log(e.target.classList);
    if (e.target.closest(".osekai__group-dropdown-arrow") == null) {
        document.querySelectorAll(".osekai__group-dropdown").forEach((colItems) => {
            colItems.classList.add("osekai__group-dropdown-hidden");
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

        var tooltipv2s = document.getElementsByClassName("tooltip-v2");
        for (var i = 0; i < tooltipv2s.length; i++) {
            var content = tooltipv2s[i].getAttribute("tooltip-content");
            var temp = tippy(tooltipv2s[i], {
                appendTo: tooltipv2s[i].closest(".osekai__panel-container, body"),
                arrow: true,
                content: content,
            });
            tooltipv2s[i].classList.remove("tooltip-v2");
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
                <object class="osekai__navbar-alert-close">
                    <i class="fas fa-times"></i>
                </object>
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
                <object class="osekai__navbar-alert-close">
                <a href="#">
                    <i class="fas fa-times"></i>
                    </a>
                </object>
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
    var container2 = document.getElementById("alerts_container_br");
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
                if (alert['Link'] != null && alert['Link'] != "") {
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
                var _container = container;
                if (alert['Placement'] == 1) {
                    _container = container2;
                }

                _container.appendChild(htmlObject);
                _container.classList.remove("hidden");
            }
        }
        positionNav();
    };

}

function insertParam(key, value) {
    if (!window.history.pushState) {
        return;
    }

    if (!key) {
        return;
    }

    var url = new URL(window.location.href);
    var params = new window.URLSearchParams(window.location.search);
    if (typeof value == 'undefined' || value === null) {

        params.delete(key);
    } else {
        params.set(key, value);
    }

    url.search = params;
    url = url.toString();
    window.history.pushState({url: url}, null, url);

}

function getParam(key) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(key);
}

document.addEventListener("DOMContentLoaded", function () {
    getAlerts();

    var oTabContainers = document.querySelectorAll("[otab-container]");
    for (let oTabContainer of oTabContainers) {
        // TODO: store open page in url
        let oTabs = oTabContainer.querySelectorAll("[otab-name]")
        var oTabButtons = oTabContainer.querySelectorAll("[otab-button]")
        var last_tab = "";
        function switchTab(tabName, push = true) {
            for (var tab of oTabs) {
                if (tab.getAttribute("otab-name") == tabName) {
                    tab.classList.remove("osekai__otab-hidden");
                    if (tab.getAttribute("otab-callback")) {
                        console.log(tab.getAttribute("otab-callback"));
                        window[tab.getAttribute("otab-callback")]();
                    }
                } else {
                    tab.classList.add("osekai__otab-hidden");
                }
            }

            for (let button of oTabButtons) {
                if (button.getAttribute("otab-button") == tabName) {
                    button.classList.add("osekai__otab-button-active");
                } else {
                    button.classList.remove("osekai__otab-button-active");
                }
            }
            if (last_tab != tabName && push == true) insertParam(oTabContainer.getAttribute("otab-container"), tabName)
            last_tab = tabName;
        }

        for (let oTab of oTabs) {
            if (oTab.getAttribute("otab-default") == "") {
                switchTab(oTab.getAttribute("otab-name"));
            }
        }

        for (let oTabButton of oTabButtons) {
            oTabButton.innerHTML = oTabButton.getAttribute("otab-button");
            oTabButton.addEventListener("click", function () {
                switchTab(oTabButton.getAttribute("otab-button"));
            });
        }

        function loadFromUrl() {
            if (getParam(oTabContainer.getAttribute("otab-container")) != null) {
                switchTab(getParam(oTabContainer.getAttribute("otab-container")), false)
            }
        }
        window.addEventListener("popstate", function () {
            loadFromUrl();
        })
        loadFromUrl();
    }
});

loadSource("groups");

var groupDropdownCounter = 0; // global, dumb shit
var groupUtils = {
    getGroupFromId: function (id) {
        for (var x = 0; x < userGroups.length; x++) {
            if (userGroups[x]['Id'] == id) {
                return userGroups[x];
            }
        }
    },
    badgeHtmlFromGroupId: function (id, size = "small") {
        var group = this.getGroupFromId(id);
        // this is technically illegal according to html spec but i don't care
        return `<object class="tooltip-v2" tooltip-content="${LocalizeTextNonAsync(group.Name)}"><a href="/misc/groups/?group=${id}" class="osekai__group-badge osekai__group-badge-${size}" style="--colour: ${group['Colour']}">${group['ShortName']}</a></object>`;
    },
    orderBadgeArray: function (array) {
        return array.sort((a, b) => a.Order - b.Order)
    },
    badgeHtmlFromArray: function (array, size = "small", limit = "none") {
        console.log(array);
        var orderedList = [];
        for (var x = 0; x < array.length; x++) {
            orderedList.push(this.getGroupFromId(array[x]));
        }
        orderedList = this.orderBadgeArray(orderedList);
        console.log(orderedList);
        var finalHtml = "";
        let hiddenGroups = []
        let createExtraDropdown = false;
        for (var x = 0; x < orderedList.length; x++) {
            if (limit == "none" || x < limit) {
                finalHtml += this.badgeHtmlFromGroupId(orderedList[x]['Id'], size);
            } else {
                if (this.getGroupFromId(orderedList[x]['Id'])['ForceVisible'] == 1) {
                    finalHtml += this.badgeHtmlFromGroupId(orderedList[x]['Id'], size);
                } else {
                    hiddenGroups.push(orderedList[x]['Id']);
                    createExtraDropdown = true;
                }
            }
        }
        if (createExtraDropdown) {
            finalHtml += '<div class="osekai__group-dropdown-arrow" onclick="groupUtils.openDropdown(this, \'' + hiddenGroups + '\')"><i class="fas fa-chevron-down"></i></div>';
        }
        return finalHtml;
    },
    badgeHtmlFromCommaSeperatedList: function (list, size = "small", limit = "none") {
        if (list == null) return "";
        var array = [];
        var split = list.split(",");
        for (var x = 0; x < split.length; x++) {
            array.push(split[x]);
        }
        return this.badgeHtmlFromArray(array, size, limit);
    },
    openDropdown: function (arrow, list) {
        if (arrow.querySelector("div")) {
            if (arrow.querySelector("div").classList.contains("osekai__group-dropdown-hidden")) {
                var dropdowns = document.getElementsByClassName("osekai__group-dropdown");
                for (var x = 0; x < dropdowns.length; x++) {
                    dropdowns[x].classList.add("osekai__group-dropdown-hidden");
                }
            }
            arrow.querySelector("div").classList.toggle("osekai__group-dropdown-hidden");
            return;
        }

        var dropdowns = document.getElementsByClassName("osekai__group-dropdown");
        for (var x = 0; x < dropdowns.length; x++) {
            dropdowns[x].classList.add("osekai__group-dropdown-hidden");
        }

        var dropdown = document.createElement('div');
        dropdown.classList.add("osekai__group-dropdown");
        dropdown.innerHTML = this.badgeHtmlFromCommaSeperatedList(list);
        arrow.appendChild(dropdown);
    }
}


function checkPermission(permission) {
    const parts = permission.split(".");


    for (var x = 0; x < userPermissions.length; x++) {
        var split = userPermissions[x].split(".");
        for (var y = 0; y < split.length; y++) {
            if (split[y] == "*" && y <= parts.length - 1) return true
            if (split[y] != parts[y]) break;
            if (split[y] == parts[y] && y == parts.length - 1) return true
        }
    }
    return false;
}