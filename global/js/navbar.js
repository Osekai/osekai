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

    //https://www.osekai.net/global/img/branding/vector/<?php echo $a['logo']; ?>.svg
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
    if(userid != -1)
    {
        // /api/profiles/get_user.php?id=4598966
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/api/profiles/get_user.php?id=" + userid, true);
        xhr.onreadystatechange = function() {
            if (this.readyState != 4) return;
            if (this.status != 200) return; // or whatever error handling you want
                
            let resp = JSON.parse(xhr.responseText);
            // amount of medals in resp['user_achievements']
            let medals = resp['user_achievements'].length;
            let club = 0;
            // possible percentage clubs: 40%, 60%, 80%, 90%, 95%
            // we have a variable called "medalAmount" which is maximum of medals
            var percentage = medals / medalAmount * 100;
            if(percentage < 40) club = "no";
            if(percentage >= 40) club = 40;
            if(percentage >= 60) club = 60;
            if(percentage >= 80) club = 80;
            if(percentage >= 90) club = 90;
            if(percentage >= 95) club = 95;
            document.getElementById("dropdown__user").classList.add("col" + club + "club");
            let perctext =percentage.toFixed(2) + "%";
            // surround all text after '.' with <span>
            perctext = perctext.replace(/\.([^.]+)/g, ".<span>$1</span>");
            document.getElementById("userdropdown_club").innerHTML = perctext
            if(club == "no")
            {
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

function open_dropdown(classname, id)
{
    document.getElementById(id).classList.remove(classname);
}

function close_dropdown(classname, id)
{
    document.getElementById(id).classList.add(classname);
}

function open_apps_dropdown() {
    dropdown("osekai__apps-dropdown-mobile-hidden", "dropdown__apps_mobile", 1);
    dropdown("osekai__apps-dropdown-hidden", "dropdown__apps", 1);
}

function ExperimentalOff() {
    openDialog("Disable Experimental Mode", "Are you sure?", "Unless you have the expon.php link, you can't turn it back on!", "Cancel", function() {
        return;
    }, "Disable", function() {
        window.location.href = "/global/api/expoff.php";
    });
}

// >Start> Notification System
setTimeout(function() {GetNotifications(false, false)}, 1000); //Loads the Amount of Notifications on the bell icon before opening the dropdown
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
    if(UI) document.getElementById("notification__list__v2").innerHTML = ""; //in case the xhrequest fails still get rid of the panel
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
    if(UI) {
        NotificationList = document.getElementById("notification__list__v2");
        document.getElementById("notification__list__v2").innerHTML = ""; // get rid of the panel again in case loading takes longer and someone rapid clicks on the icon
    }

    let nCount = 0;
    Object.keys(Notifications).forEach(function (obj) {
        if(UI) CreateNotificationItem(NotificationList, Notifications[obj]);
        nCount += 1;
    });
    document.getElementById("NotificationCountIcon").innerHTML = nCount;
    if(nCount > 0 && document.getElementById("NotificationCountIcon").classList.contains("hidden")) document.getElementById("NotificationCountIcon").classList.remove("hidden");
    document.getElementById("NotificationCount").innerHTML = GetStringRawNonAsync("navbar", "notifications.count", [nCount]);
}

function CreateNotificationItem(List, Notification) {
    console.log("Getting Notifications");
    let Outer = document.createElement("div");
    Outer.classList.add("osekai__nav-dropdown-v2-notification");

    if(Notification['Message'] == "") {
        let Upper = document.createElement("div");
        Upper.classList.add("osekai__nav-dropdown-v2-notification-upper");

        let Image = document.createElement("img");
        if(Notification['logo'] == "" || Notification['logo'] == null) {
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
        if(Notification['Link'] == "" || Notification['Link'] == null) {
            Upper = document.createElement("div");
        } else {
            Upper = document.createElement("a");
            Upper.classList.add("osekai__nav-dropdown-v2-notification-upper-clickable");
            Upper.href = Notification["Link"];
        }
        Upper.classList.add("osekai__nav-dropdown-v2-notification-upper");

        let Image = document.createElement("img");
        if(Notification['logo'] == "" || Notification['logo'] == null) {
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
            if(!document.getElementById("NotificationCountIcon").classList.contains("hidden")) document.getElementById("NotificationCountIcon").classList.add("hidden");
            GetNotifications(false, true);
        }
    };
}
// <End> Notification System