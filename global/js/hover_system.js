// dont even try to read any of this

//#region vars

var loading_icon = "";
var loading_name = "";
var loading_desc = "Loading...";

var m_icon = "";
var m_name = "";
var m_desc = "Loading...";

var loading_id = 0;
var loading_avatar = "";
var loading_name = "Loading...";
var loading_countrycode = "XX";

var u_id = 0;
var u_avatar = "";
var u_name = "Loading...";
var u_countrycode = "XX";

const bmpanel = document.getElementById("beatmap_hover_panel");
var hovering = 0;
var timeoutId = "";

const userpanel = document.getElementById("userhoverpanel_v2");
var user_hovering = 0;
var user_timeoutId = "";

const mdpanel = document.getElementById("medal_hover_panel");
var medal_hovering = 0;
var medal_timeoutId = "";

const tooltip = document.getElementById("tooltip");
var tooltip_hovering = 0;
var tooltip_timeoutId = "";

var tooltip_parent = document.getElementById("tooltip");

//#endregion

function offset(el) {
    var rect = el.getBoundingClientRect(),
        scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
        scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return { top: rect.top, left: rect.left }
}

function update_tooltip_position() {
    if (tooltip_parent) {
        let x = offset(tooltip_parent).left;
        let y = offset(tooltip_parent).top;

        tooltip.style.setProperty('--mouse-x', x + "px");
        tooltip.style.setProperty('--mouse-y', y + "px");
        tooltip.style.setProperty('--xoffset', "0px");
        tooltip.style.setProperty('--yoffset', "0px");
        tooltip.style.setProperty('--pobj-width', tooltip_parent.offsetWidth + "px");

        // if it's too close to the edge, move it away
        if (x < 200) {
            tooltip.style.setProperty('--mouse-x', x + 200 + "px");
            tooltip.style.setProperty('--xoffset', "-200px");
        }
        if (screen.width - x < 200) {
            tooltip.style.setProperty('--mouse-x', x - 200 + "px");
            tooltip.style.setProperty('--xoffset', "200px");
        }

    }
}


document.addEventListener('mousemove', evt => {
    // if (hovering == 0) {
    //     let x = evt.clientX / innerWidth;
    //     let y = evt.clientY / innerHeight;
    // 
    //     bmpanel.style.setProperty('--mouse-x', x);
    //     bmpanel.style.setProperty('--mouse-y', y);
    // }

    if (mdpanel) {
        if (medal_hovering == 0) {
            let x = evt.clientX / innerWidth;
            let y = evt.clientY / innerHeight;

            mdpanel.style.setProperty('--mouse-x', x);
            mdpanel.style.setProperty('--mouse-y', y);
        }
    }
    if (userpanel) {

        if (user_hovering == 0) {
            let x = evt.clientX / innerWidth;
            let y = evt.clientY / innerHeight;

            if (x > 0.85) {
                x = 0.85;
            }

            userpanel.style.setProperty('--mouse-x', x);
            userpanel.style.setProperty('--mouse-y', y);
        }
    }



    update_tooltip_position();

});


document.addEventListener('scroll', evt => {
    update_tooltip_position();
});

var instances = [];
function replaceTippy() {
    // wait 0.1 seconds
    setTimeout(function () {
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
    replaceTippy();
});

mutationObserver.observe(document.body, {
    childList: true,
    subtree: true
});



document.medals = null;

window.onload = function fw() {
    if (document.medals == null) {
        // /medals/api/medals
        // send with POST "strSearch="
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.medals = JSON.parse(this.responseText);
                //console.log(medals);
            }
        }
        xhttp.open("POST", "/medals/api/medals_nogrouping", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("strSearch=");
    }
    document.onmouseover = function fa(e) {
        var e = e || window.event,
            el = e.target || el.srcElement;

        // medal hover

        if (el.closest('.medal_hover')) {
            medal_timeoutId = window.setTimeout(function ea() {
                window.setTimeout(function eo() {
                    mdpanel.classList.remove("medal_hover_panel_hidden");

                    var obj;

                    var id = el.getAttribute("medalname");

                    //fetch('/api/medals/get_medal.php?name=' + id)
                    //    .then(res => res.json())
                    //    .then(data => obj = data)
                    //    .then(() => medal_loaddata(obj))

                    for (var i = 0; i < document.medals.length; i++) {
                        if (document.medals[i]['name'] == id) {
                            obj = document.medals[i];
                            //console.log(obj);
                            break;
                        }
                    }
                    medal_loaddata(obj);

                    medal_hovering = 1;

                }, 100);
            }, 800);

        }


        // user hover

        // if (el.closest('.user_hover')) {
        //     timeoutId = window.setTimeout(function fo() {
        //         window.setTimeout(function fe() {
        //             bmpanel.classList.remove("beatmap_hover_panel_hidden");
        // 
        //             var obj;
        // 
        //             var id = el.getAttribute("userid");
        // 
        //             fetch('/api/profiles/get_user.php?id=' + id)
        //                 .then(res => res.json())
        //                 .then(data => obj = data)
        //                 .then(() => loaddata(obj))
        // 
        //             hovering = 1;
        // 
        //         }, 100);
        //     }, 800);
        // 
        // }

        if (el.closest('.user_hover_v2')) {
            document.getElementById("userhoverpanel_v2_username").innerHTML = "Loading...";
            document.getElementById("userhoverpanel_v2_rank").innerHTML = 0;
            document.getElementById("userhoverpanel_v2_pp").innerHTML = 0;
            document.getElementById("userhoverpanel_v2_pfp").src = "https://osu.ppy.sh/assets/images/avatar-guest.8a2df920.png";
            document.getElementById("userhoverpanel_v2_blur").src = "https://osu.ppy.sh/assets/images/avatar-guest.8a2df920.png";
            document.getElementById("userhoverpanel_v2").href = "https://osu.ppy.sh/users/" + 0

            timeoutId = window.setTimeout(function fo() {
                window.setTimeout(function fe() {
                    userpanel.classList.remove("osekai__userpanel-hoverpanel-hidden");

                    var obj;

                    var id = el.getAttribute("userid");

                    fetch('/api/profiles/get_user.php?id=' + id)
                        .then(res => res.json())
                        .then(data => obj = data)
                        .then(() => userpanel_load(obj))

                    user_hovering = 1;

                }, 100);
            }, 800);

        }


        // tooltip hover

        if (el.closest('.tooltip')) {
            el = el.closest("div, h1, h2, b, p, img, .medals__bmp3-bold, .forcetooltip");
            document.getElementById("tooltip_text").innerHTML = el.getAttribute("tooltip");
            //console.log("tooltip: " + el.getAttribute("tooltip"));
            tooltip_parent = el;
            tooltip_timeoutId = window.setTimeout(function fo() {
                tooltip.classList.remove("tooltip_hidden");
                tooltip_hovering = 1;
            }, 200);

        }
    };

    document.onmouseout = function fp(e) {
        var e = e || window.event,
            el = e.target || el.srcElement;

        // medal hover

        if (!el.closest('.medal_hover')) {
            if (typeof medal_timeoutId !== 'undefined') {

                window.clearTimeout(medal_timeoutId);
                if (mdpanel) {
                    mdpanel.classList.add("medal_hover_panel_hidden");
                }
                window.setTimeout(function () {
                    medal_hovering = 0;
                    //document.getElementById('bhp_avi').src = loading_avatar;
                    //document.getElementById('bhp_ctc').src = loading_countrycode;
                    //document.getElementById('bhp_usn').innerHTML = loading_name;
                    //mdpanel.href = "https://osu.ppy.sh/users/" + loading_id;
                }, 500);
            }
        }


        // user hover

        // if (!el.closest('.user_hover')) {
        //     if (typeof timeoutId !== 'undefined') {
        // 
        //         window.clearTimeout(timeoutId);
        //         bmpanel.classList.add("beatmap_hover_panel_hidden");
        //         window.setTimeout(function () {
        //             hovering = 0;
        //             document.getElementById('bhp_avi').src = loading_avatar;
        //             document.getElementById('bhp_ctc').src = "https://osu.ppy.sh/images/flags/" + loading_countrycode + ".png";
        //             document.getElementById('bhp_usn').innerHTML = loading_name;
        //             bmpanel.href = "https://osu.ppy.sh/users/" + loading_id;
        //         }, 500);
        //     }
        // }

        if (!el.closest('.user_hover_v2') || !el.closest('osekai__userpanel-hoverpanel')) {
            if (typeof timeoutId !== 'undefined') {

                window.clearTimeout(timeoutId);
                if (userpanel) {
                    userpanel.classList.add("osekai__userpanel-hoverpanel-hidden");
                }
                window.setTimeout(function () {
                    user_hovering = 0;
                }, 500);
            }
        }


        // tooltip hover

        if (!el.closest('.tooltip')) {
            if (typeof tooltip_timeoutId !== 'undefined') {

                window.clearTimeout(tooltip_timeoutId);
                window.setTimeout(function () {
                    tooltip.classList.add("tooltip_hidden");
                    tooltip_hovering = 0;
                }, 200);
            }
        }
    };
};

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function userpanel_load(obj) {
    var playmode = obj["playmode"];
    document.getElementById("userhoverpanel_v2_gamemode").src = "/global/img/gamemodes/" + playmode + ".svg";

    //console.log(obj);
    document.getElementById("userhoverpanel_v2_username").innerHTML = obj['username'];
    try {
        document.getElementById("userhoverpanel_v2_rank").innerHTML = "#" + numberWithCommas(obj['statistics']['global_rank']);
    } catch {
        document.getElementById("userhoverpanel_v2_rank").innerHTML = "-";
    }
    //         document.getElementById("userhoverpanel_v2_pp").innerHTML = obj['statistics']['pp'];
    try {
        document.getElementById("userhoverpanel_v2_pp").innerHTML = numberWithCommas(obj['statistics']['pp']) + "pp";
    } catch {
        document.getElementById("userhoverpanel_v2_pp").innerHTML = "-";
    }

    document.getElementById("userhoverpanel_v2_pfp").src = "https://a.ppy.sh/" + obj['id'];
    document.getElementById("userhoverpanel_v2_blur").src = "https://a.ppy.sh/" + obj['id'];
    document.getElementById("userhoverpanel_v2").href = "https://osu.ppy.sh/users/" + obj['id'];
}

function loaddata(obj) {
    u_avatar = obj['avatar_url'];
    u_name = obj['name'];
    u_countrycode = obj['country_code'];
    u_id = obj['id'];

    document.getElementById('bhp_avi').src = u_avatar;
    document.getElementById('bhp_ctc').src = "https://osu.ppy.sh/images/flags/" + u_countrycode + ".png";
    document.getElementById('bhp_usn').innerHTML = u_name;
    // bmpanel.href = "https://osu.ppy.sh/users/" + u_id;
}

function medal_loaddata(obj) {
    m_icon = obj['link'];
    m_name = obj['name'];
    m_desc = obj['description'];

    document.getElementById('mhp_blur').src = m_icon;
    document.getElementById('mhp_mic').src = m_icon;
    document.getElementById('mhp_nam').innerHTML = m_name;
    document.getElementById('mhp_dsc').innerHTML = m_desc;
    document.getElementById('mhp_sol').innerHTML = obj['instructions'].replace("NULL", "");
    // mdpanel.href = "/medals";
};

