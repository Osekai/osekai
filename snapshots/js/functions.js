

const tx = document.getElementsByTagName("textarea");
for (let i = 0; i < tx.length; i++) {
    var fe = tx[i].scrollHeight + 16;
    tx[i].setAttribute("style", "height:" + (fe) + "px;overflow-y:hidden;");
    tx[i].addEventListener("input", OnInput, false);
}

function OnInput() {
    this.style.height = "auto";
    this.style.height = (this.scrollHeight) + "px";
}

const API_URL = "https://osekai.net/snapshots/api/api.php";

let params = new URLSearchParams(window.location.search);

function createXHR(strUrl) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", strUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    return xhr;
}

function getOrdinalNum(n) {
    return n + (n > 0 ? ['th', 'st', 'nd', 'rd'][(n > 3 && n < 21) || n % 10 > 3 ? 0 : n % 10] : '');
}

if (params.get("version") != null) {
    openLoader('Loading version...');
}

var data;

var groups = [
    [1, "osu!stable"],
    [2, "osu!lazer"]
]

var loaded = false;
var data;

var wantedVer = 0;

var currentindex;

function listClick(element, id){
    // remove snapshots__list-version-active from all items which have it
    var active = document.getElementsByClassName("snapshots__list-version-active");
    for (var i = 0; i < active.length; i++) {
        active[i].classList.remove("snapshots__list-version-active");
    }
    
    element.classList.add("snapshots__list-version-active");
    loadVersion(id, true, true);
}

async function loadVersion(vername, pushstate = true, fromButton = false) {
    if(window.mobile == true && document.getElementsByClassName("osekai__3col_col1")[0].classList.contains("osekai__3col_col1_hide") == false){
        switch3col();
    }

    if(fromButton == false){
        // remove snapshots__list-version-active from all items which have it
        var active = document.getElementsByClassName("snapshots__list-version-active");
        for (var i = 0; i < active.length; i++) {
            active[i].classList.remove("snapshots__list-version-active");
        }

        console.log("finding button for " + vername);


        var truever = null;
        // go through all versions and see if their id is the same
        var versionButtons = document.getElementsByClassName("snapshots__list-version");
        for (var i = 0; i < versionButtons.length; i++){
            if(versionButtons[i].classList.contains("ver_id_" + vername))
            {
                versionButtons[i].classList.add("snapshots__list-version-active");
                console.log("found it");
                truever = versionButtons[i];
            }
        }

        setTimeout(function(){
            truever.scrollIntoView({behavior: "smooth",
            block: 'center',
            inline: 'center'});
        }, 450);
    }

    wantedVer = vername;

    var thisver;

    console.log(data);
    data.forEach(function (version, index) {
        if (version["version_info"]["id"] == vername) {
            thisver = version;
            currentindex = index;
        }
    });
    if (thisver == null) {
        alert("This version could not be found.");
        closeLoader();
        return;
    }

    if (pushstate == true) {
        let params = new URLSearchParams(window.location.search);
        params.set("version", vername);
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    }

    document.getElementById("version_name").innerHTML = thisver["version_info"]["name"] + " " + thisver["version_info"]["version"];

    var reldate = new Date(thisver["version_info"]["release"] * 1000).toLocaleDateString("en-AU", { month: "long", day: "numeric", year: "numeric" });
    document.getElementById("release_date").innerHTML = await GetStringRaw("snapshots", "version.released", [reldate]);
    


    document.getElementById("archived_by").innerHTML = await GetStringRaw("snapshots", "version.archivedBy", [`<strong class="user_hover" userid="` + thisver["archive_info"]["archiver"] + `" hoverside="right">` + thisver["archive_info"]["archiver"] + `</strong>`]); 

    if(thisver["archive_info"]["archiver_id"] != null && thisver["archive_info"]["archiver_id"] != ""){
        document.getElementById("archived_by").innerHTML = await GetStringRaw("snapshots", "version.archivedBy", [`<img class="snapshots__archiver-pfp" src="` + thisver['archive_info']['pfp'] + `"> <strong class="user_hover_v2" userid="` + thisver["archive_info"]["archiver_id"] + `" hoverside="right">` + thisver["archive_info"]["archiver"] + `</strong>`]);
    }

    document.getElementById("version_header").style = `background: linear-gradient(90deg, #262C7C 0%, rgba(38, 44, 124, 0.25) 100%), url(` + "versions/" + thisver["version_info"]["version"] + `/thumbnail.jpg) no-repeat top center; background-size: cover; background-position: center;`;

    document.getElementById("views").innerHTML = thisver["stats"]["views"];
    document.getElementById("downloads").innerHTML = thisver["stats"]["downloads"];

    document.getElementById("description").innerHTML = thisver["archive_info"]["description"];
    if(thisver["archive_info"]["description"] == null || thisver["archive_info"]["description"] == ""){
        document.getElementById("descriptionInner").classList.add("hidden");
    }else{
        console.log(thisver["archive_info"]["description"]);
        document.getElementById("descriptionInner").classList.remove("hidden");
    }
    // downloads
    document.getElementById("downloads_list").innerHTML = "";

    var downloads = thisver["downloads"];
    console.log(downloads);



    for(var key in downloads){
        var downloadlink;
        console.log(downloads[key]["name"]);
        if (downloads[key]["name"] == "Osekai Servers") {
            downloadlink = "versions/" + thisver["version_info"]["version"] + "/" + downloads[key]["link"]

            document.getElementById("downloads_list").innerHTML += `<div class="snapshots__download-outer"><a onclick="downloadVer(` + thisver['version_info']['id'] + `, '` + downloadlink + `')" class="snapshots__download-panel">
        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.9">
                <path d="M11.3906 0H15.6094C16.3107 0 16.875 0.564258 16.875 1.26562V10.125H21.4998C22.4385 10.125 22.9078 11.2588 22.2434 11.9232L14.2225 19.9494C13.827 20.3449 13.1783 20.3449 12.7828 19.9494L4.75137 11.9232C4.08691 11.2588 4.55625 10.125 5.49492 10.125H10.125V1.26562C10.125 0.564258 10.6893 0 11.3906 0ZM27 19.8281V25.7344C27 26.4357 26.4357 27 25.7344 27H1.26562C0.564258 27 0 26.4357 0 25.7344V19.8281C0 19.1268 0.564258 18.5625 1.26562 18.5625H9.00176L11.5857 21.1465C12.6457 22.2064 14.3543 22.2064 15.4143 21.1465L17.9982 18.5625H25.7344C26.4357 18.5625 27 19.1268 27 19.8281ZM20.4609 24.4688C20.4609 23.8887 19.9863 23.4141 19.4062 23.4141C18.8262 23.4141 18.3516 23.8887 18.3516 24.4688C18.3516 25.0488 18.8262 25.5234 19.4062 25.5234C19.9863 25.5234 20.4609 25.0488 20.4609 24.4688ZM23.8359 24.4688C23.8359 23.8887 23.3613 23.4141 22.7812 23.4141C22.2012 23.4141 21.7266 23.8887 21.7266 24.4688C21.7266 25.0488 22.2012 25.5234 22.7812 25.5234C23.3613 25.5234 23.8359 25.0488 23.8359 24.4688Z" fill="white" />
            </g>
        </svg>
        <div class="snapshots__download-text">
            <p class="snapshots__download-header">` + await GetStringRaw("snapshots", "version.download.title") + ` <span class="recommended">` + await GetStringRaw("snapshots", "version.download.recommended") + `</span></p>` + `
            <p class="snapshots__download-source">` + await GetStringRaw("snapshots", "version.download.from", [downloads[key]["name"]])  + `<p>
        </div>
    </a>
    </div>`;
        }
    }


    Object.keys(downloads).forEach(function (key, index) {
        var downloadlink;
        console.log(downloads[key]["name"]);
        if (downloads[key]["name"] != "Osekai Servers") {
            downloadlink = downloads[key]["link"];

            if (nRights > 0) {
                var admin = `<div class="osekai__left snapshots__admin-download-controls">
                <div onclick="deleteDownloadMirror(this, '` + downloads[key]["name"] + `')"</div>
                    <i class="far fa-trash-alt"></i>
                </div>
            </div>`;
            } else {
                var admin = "";
            }

            document.getElementById("downloads_list").innerHTML += `<div class="snapshots__download-outer"><a onclick="downloadVer(` + thisver['version_info']['id'] + `, '` + downloadlink + `')" class="snapshots__download-panel">
        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.9">
                <path d="M11.3906 0H15.6094C16.3107 0 16.875 0.564258 16.875 1.26562V10.125H21.4998C22.4385 10.125 22.9078 11.2588 22.2434 11.9232L14.2225 19.9494C13.827 20.3449 13.1783 20.3449 12.7828 19.9494L4.75137 11.9232C4.08691 11.2588 4.55625 10.125 5.49492 10.125H10.125V1.26562C10.125 0.564258 10.6893 0 11.3906 0ZM27 19.8281V25.7344C27 26.4357 26.4357 27 25.7344 27H1.26562C0.564258 27 0 26.4357 0 25.7344V19.8281C0 19.1268 0.564258 18.5625 1.26562 18.5625H9.00176L11.5857 21.1465C12.6457 22.2064 14.3543 22.2064 15.4143 21.1465L17.9982 18.5625H25.7344C26.4357 18.5625 27 19.1268 27 19.8281ZM20.4609 24.4688C20.4609 23.8887 19.9863 23.4141 19.4062 23.4141C18.8262 23.4141 18.3516 23.8887 18.3516 24.4688C18.3516 25.0488 18.8262 25.5234 19.4062 25.5234C19.9863 25.5234 20.4609 25.0488 20.4609 24.4688ZM23.8359 24.4688C23.8359 23.8887 23.3613 23.4141 22.7812 23.4141C22.2012 23.4141 21.7266 23.8887 21.7266 24.4688C21.7266 25.0488 22.2012 25.5234 22.7812 25.5234C23.3613 25.5234 23.8359 25.0488 23.8359 24.4688Z" fill="white" />
            </g>
        </svg>
        <div class="snapshots__download-text">
            <p class="snapshots__download-header">download</p>` + `
            <p class="snapshots__download-source">from ` + downloads[key]["name"] + `
            <p>
        </div>
        
    </a>` + admin + `</div>`;
        }
    });
    
    if (thisver["archive_info"]["extra_info"] == null || thisver["archive_info"]["extra_info"] == "") {
        document.getElementById("extra_info_panel").classList.add("hidden");
    } else {
        document.getElementById("extra_info_panel").classList.remove("hidden");
        document.getElementById("extra_info").innerHTML = thisver["archive_info"]["extra_info"];
    }

    if (thisver["archive_info"]["video"] == null || thisver["archive_info"]["video"] == "") {
        document.getElementById("video_panel").classList.add("hidden");
    } else {
        document.getElementById("video_panel").classList.remove("hidden");
        document.getElementById("video").src = thisver["archive_info"]["video"].replace("youtube.com", "www.youtube-nocookie.com");
    }

    warnings = false;

    document.getElementById("warnings_list").innerHTML = "";

    if (thisver["archive_info"]["auto_update"] == true) {
        warnings = true;
        document.getElementById("warnings_list").innerHTML += `<div class="snapshots__warning">
        <h1 class="snapshots__warning-header">
            <i class="fas fa-exclamation-triangle"></i> ` + await GetStringRaw("snapshots", "version.warnings.automaticallyUpdated.title") + `
        </h1>
        <p class="snapshots__warning-content">
            ` + await GetStringRaw("snapshots", "version.warnings.automaticallyUpdated.description") + `
        </p>
    </div>`;
    }

    if (thisver["archive_info"]["requires_supporter"] == true) {
        warnings = true;
        document.getElementById("warnings_list").innerHTML += `<div class="snapshots__warning">
        <h1 class="snapshots__warning-header">
            <i class="fas fa-exclamation-triangle"></i> ` + await GetStringRaw("snapshots", "version.warnings.requiresServer.title") + `
        </h1>
        <p class="snapshots__warning-content">
            ` + await GetStringRaw("snapshots", "version.warnings.requiresServer.description") + `
        </p>
    </div>`;
    }

    if (warnings == false) {
        document.getElementById("warnings_panel").classList.add("hidden");
    } else {
        document.getElementById("warnings_panel").classList.remove("hidden");
    }

    var screenshot_grid = document.getElementById("screenshot_grid");
    screenshot_grid.innerHTML = "";

    Object.keys(thisver["screenshots"]).forEach(function (key, index) {
        if (nRights < 1) {
            screenshot_grid.innerHTML += `<img onclick="showScreenshotOverlay(this.src)" class="snapshots__image" src="` +
                "versions/" + thisver["version_info"]["version"] + "/" + thisver['screenshots'][key]
                + `">`;
        } else {
            screenshot_grid.innerHTML += `<div id="screenshot_` + index + `" class="snapshots__admin-image"><div class="snapshots__admin-image-overlay">
            <p onclick="adminDeleteScreenshot(` + thisver['version_info']['id'] + `,` + index + `,this)" class="snapshots__admin-image-overlay-button">
                <i class="far fa-trash-alt"></i>
            </p>
            <p class="snapshots__admin-image-overlay-button">
            <i class="fas fa-pencil-alt"></i>
            </p>
        </div><img onclick="showScreenshotOverlay(this.src)" src="` +
                "versions/" + thisver["version_info"]["version"] + "/" + thisver['screenshots'][key]
                + `">
            
            </div>`;
        }
    });

    document.getElementById("home").classList.add("hidden");
    document.getElementById("version").classList.remove("hidden");
    if (document.getElementById("admin")) document.getElementById("admin").classList.add("hidden");

    var form = new FormData();
    form.append('verID', thisver['version_info']['id']);
    form.append('type', "view");
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/update_counter', true);
    xhr.onload = function () {
        console.log(this.responseText);
    };
    xhr.send(form);


    closeLoader();
    Comments_Require("", document.getElementById("comments__box"), true, parseInt(data[currentindex]['version_info']['id'])); // <mulraf> Initial Comment Loading </mulraf>
}

function deleteDownloadMirror(from, name) {
    version = data[currentindex];
    var id = version['version_info']['id'];
    var form = new FormData();
    form.append('id', id);
    form.append('downloadName', name);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/admin_deletemirror', true);
    xhr.onload = function () {
        console.log(this.responseText);
    };
    xhr.send(form);
    from.closest(".snapshots__download-outer").remove();
}

function addDownloadMirror(name, link) {
    version = data[currentindex];
    var id = version['version_info']['id'];
    var form = new FormData();
    form.append('id', id);
    form.append('downloadName', name);
    form.append('downloadLink', link);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/admin_addmirror', true);
    xhr.onload = function () {
        console.log(this.responseText);
    };
    xhr.send(form);
}

function addScreenshotToCurrentVersion() {
    document.getElementById("screenshotAddForm").classList.remove("hidden");
    var thisver = data[currentindex];

    document.getElementById("screenshot_id").value = thisver['version_info']['id'];
}

function addMirrorToCurrentVersion() {
    document.getElementById("mirrorAddForm").classList.remove("hidden");
    var thisver = data[currentindex];

    document.getElementById("mirror_id").value = thisver['version_info']['id'];
}

function adminDeleteScreenshot(id, index, obje) {
    var form = new FormData();
    form.append('id', id);
    form.append('screenshotIndex', index);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/admin_deletescreenshot', true);
    xhr.onload = function () {
        console.log(this.responseText);
    };
    xhr.send(form);
    document.getElementById("screenshot_" + index).remove();
}

function download(url) {
    const a = document.createElement('a')
    a.href = url
    a.download = url.split('/').pop()
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
}

function downloadVer(id, downloadlink) {
    download(downloadlink);
    console.log(downloadlink);
    var form = new FormData();
    form.append('verID', id);
    form.append('type', "download");
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/update_counter', true);
    xhr.onload = function () {
        console.log(this.responseText);
    };
    xhr.send(form);
}

window.addEventListener('popstate', function (event) {
    let params = new URLSearchParams(window.location.search);
    if (params.get("version") != null) {
        loadVersion(params.get("version"), false);
    } else {
        document.getElementById("home").classList.remove("hidden");
        document.getElementById("version").classList.add("hidden");
        document.getElementById("admin").classList.add("hidden");
    }
});

async function addToScroller(oResponse) {
    data = oResponse;

    var scroller = document.getElementById("oVersionSection");

    scroller.innerHTML = "";

    var versioncount = 0;

    //document.getElementById("home_slogan").innerHTML = "serving " + oResponse.length + " osu! versions from 2007 to now!";
    document.getElementById("home_slogan").innerHTML = await GetStringRaw("snapshots", "home.slogan", [oResponse.length]);

    groups.forEach(function (element) {
        scroller.innerHTML +=
            `<section class="osekai__panel">
            <div class="osekai__panel-header" id="group_header_` + element[0] + `">
            ` + element[1] + `
            </div>
            <div class="osekai__panel-inner snapshots__vlist-inner" id="group_` + element[0] + `">
            </div>
        </section>`;
        versioncount = 0;
        oResponse.forEach(function (version, index) {
            
            if (version["archive_info"]["group"] == element[0]) {
                versioncount++;
                var group_div = document.getElementById("group_" + element[0]);
                var image01 = "versions/" + version["version_info"]["version"] + `/` + version["screenshots"][0];
                var thumbnail = "versions/" + version['version_info']['version'] + "/thumbnail.jpg";
                if (image01.includes("hubza")) {
                    image01 = version["screenshots"][0];
                } else {
                    image01 = thumbnail;
                }
                var newText = "";
                if(version["archive_info"]['new'] == true){
                    newText = ` <div class="snapshots__list-version-new">New!</div>`;
                }
                group_div.innerHTML +=
                    `<div id="ver_` + index + `" class="ver_id_` + version["version_info"]["id"] + ` snapshots__list-version" onclick="listClick(this, ` + version["version_info"]["id"] + `)">
                    <div class="snapshots__list-version-image">
                        <img src="` + image01 + `">
                        <div class="highlight"></div>
                        ` + newText + `
                    </div>
                    <div class="snapshots__list-version-info-row1">
                        <h1>` + version["version_info"]["version"] + `</h1>
                        <h2>` + new Date(version["version_info"]["release"] * 1000).toLocaleDateString("en-AU", { month: "long", day: "numeric", year: "numeric" }) + `</h2>
                        <p><span class="translatable">??snapshots.version.archivedBy??</span> <strong class="user_hover" userid="` + version["archive_info"]["archiver"] + `" hoverside="right">` + version["archive_info"]["archiver"] + `</strong></p>
                    </div>
                    <div class="snapshots__list-version-info-row2">
                        <div class="snapshots__list-views">
                            <i class="fas fa-eye"></i> ` + version["stats"]["views"] + `
                        </div>
                        <div class="snapshots__list-downloads">
                            <i class="fas fa-download"></i> ` + version["stats"]["downloads"] + `
                        </div>
                    </div>
                </div>`;
            }
        });
        document.getElementById("group_header_" + element[0]).innerHTML = element[1] + " (" + versioncount + ")";

    });

    loaded = true;

    let params = new URLSearchParams(window.location.search);
    if (params.get("version") != null) {
        loadVersion(params.get("version"), false);
        //closeLoader();
    } else {
        document.getElementById("home").classList.remove("hidden");
        document.getElementById("version").classList.add("hidden");
    }
}

function doSearch(search) {
    if (loaded == true) {
        data.forEach(function (version, index) {
            if (version["version_info"]["name"].includes(search) ||
                version["version_info"]["version"].includes(search)) {
                document.getElementById("ver_" + index).classList.remove("hidden");
            } else {
                document.getElementById("ver_" + index).classList.add("hidden");
            }
        });
    }
}

function loadData() {
    var xhr = createXHR(API_URL);
    xhr.send();
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        addToScroller(oResponse);
    };
}

function hideScreenshotOverlay() {
    document.getElementById("screenshot_overlay").classList.add("snapshots__screenshotoverlay-hidden");
}

function showScreenshotOverlay(img) {
    document.getElementById("screenshot_overlay_img").src = img;
    document.getElementById("screenshot_overlay").classList.remove("snapshots__screenshotoverlay-hidden");
}

var submission_versionName = document.getElementById("submission_versionName");
var submission_versionFile = document.getElementById("submission_versionFile");
var submission_versionInfo = document.getElementById("submission_versionInfo");
var submission_userID = window.nUserID;

function openSubmission() {
    submission_versionName.value = "";

    submission_versionFile.value = "";

    submission_versionInfo.value = "";

    document.getElementById("submission_overlay").classList.remove("osekai__overlay-hidden");
}

function closeSubmission() {
    document.getElementById("submission_overlay").classList.add("osekai__overlay-hidden");
}

function submitSubmission() {
    var data = new FormData();
    data.append('submission_versionName', submission_versionName.value);
    data.append('submission_versionFile', submission_versionFile.value);
    data.append('submission_versionInfo', submission_versionInfo.value);
    data.append('submission_userID', nUserID);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/user_submit', true);
    xhr.onload = function () {
        // do something to response
        if (this.responseText == "SUCCESS") {
            cancelWarning();
            closeSubmission();
            generatenotification("normal", "Thank you for your submission! We will check it out ASAP!");
        } else {
            cancelWarning();
            document.getElementById("error_message").innerHTML = this.responseText;
        }
    };
    xhr.send(data);
}

function openWarning(){
    document.getElementById("submitWarning").classList.remove("snapshots__submission-verification-closed");
    document.getElementById("submission_overlay").classList.add("osekai__overlay-hidden");
}

function cancelWarning(){
    document.getElementById("submitWarning").classList.add("snapshots__submission-verification-closed");
    document.getElementById("submission_overlay").classList.remove("osekai__overlay-hidden");
}

function openAdminPanel() {
    document.getElementById("version").classList.add("hidden");
    document.getElementById("home").classList.add("hidden");
    document.getElementById("admin").classList.remove("hidden");
}

function openUploadPopup() {
    document.getElementById("uploadForm").classList.remove("hidden");
}

function cancelVersion() {
    document.getElementById("uploadForm").classList.add("hidden");
}

function cancelEdit() {
    document.getElementById("editForm").classList.add("hidden");
}

var verGroups = ["stable", "lazer"];

function inttobool(int) {
    if (int == 0 || int == "0") {
        return false;
    } else {
        return true;
    }
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function openEditPopup() {
    var thisver = data[currentindex];

    document.getElementById("editForm").classList.remove("hidden");
    var date = new Date(thisver['version_info']['release'] * 1000);

    document.getElementById("edit_id").value = thisver['version_info']['id'];

    document.getElementById("edit_releasedate").value = formatDate(date);

    document.getElementById("edit_name").value = thisver['version_info']['name'];
    document.getElementById("edit_version").value = thisver['version_info']['version'];
    document.getElementById("edit_arch_name").value = thisver['archive_info']['archiver'];
    document.getElementById("edit_arch_id").value = thisver['archive_info']['archiver_id'];
    document.getElementById("edit_description").value = thisver['archive_info']['description'];
    document.getElementById("edit_video").value = thisver['archive_info']['video'];
    document.getElementById("edit_extrainfo").value = thisver['archive_info']['extra_info'];

    document.getElementById("edit_group").value = verGroups[thisver['archive_info']['group'] - 1];

    document.getElementById("edit_autoupdate").checked = thisver['archive_info']['auto_update'];
    document.getElementById("edit_requireserver").checked = thisver['archive_info']['requires_supporter'];

    document.getElementById("editBarText").value = "You are currently editing " + thisver['version_info']['version'] + "... please be careful.";
}

var submissions;
var submissionIndexChangingId;

function refreshSubmissions() {
    var xhr = createXHR("https://osekai.net/snapshots/api/get_submissions");
    xhr.send();
    xhr.onreadystatechange = function () {
        var sublist = document.getElementById("submission_list");
        sublist.innerHTML = "";
        var oResponse = getResponse(xhr);
        console.log(oResponse);
        if (handleUndefined(oResponse)) return;
        submissions = oResponse;
        oResponse.forEach(function (item, index) {
            wip = false;
            if (item['processing'] == 1) {
                wip = true;
            }
            time = TimeAgo.inWords(new Date(item['date']).getTime());
            sublist.innerHTML += `<div class="snapshots__submission">
                <div class="snapshots__submission-inner">
                    <h1><div id="wip_` + index + `" onclick="switchWIP(` + index + `)" class="snapshots__submission-wip-icon ` + (wip ? "green" : "") + `"></div> ` + item['name'] + `</h1>
                    <p>` + item['info'] + `</p>
                </div>
                <div class="snapshots__submission-footer">
                    <img src="https://a.ppy.sh/` + item['userid'] + `">
                    <p>submitted by <strong>` + item['username'] + `</strong> ` + time + `</p>
                    <div class="osekai__left osekai__button-row">
                        <a href="` + item['link'] + `" class="osekai__button">download version</a>
                        <a onclick="submissionAccept(` + index + `)"class="osekai__button"><i class="fas fa-vote-yea"></i></a>
                        <a href="https://osu.ppy.sh/home/messages/users/` + item['userid'] + `" class="osekai__button"><i class="fas fa-inbox"></i></a>
                        <a onclick="submissionDeny(` + index + `)"class="osekai__button"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </div>`;
        });
    };
}

function submissionAccept(index) {
    submissionIndexChangingId = index;
    document.getElementById("submission_status_status").value = 2;
    document.getElementById("submission_status_notification").value = "Your version, " + submissions[index]['name'] + ", has been accepted! We'll be uploading it shortly.";
    document.getElementById("submission_status_overlay").classList.remove("osekai__overlay-hidden");
}

function switchWIP(index) {
    submissionIndexChangingId = index;
    dot = document.getElementById("wip_" + index);
    if(dot.classList.contains("green"))
    {
        dot.classList.remove("green");
        // need to contact the apis
        var xhr = createXHR("https://osekai.net/snapshots/api/set_processing");
        xhr.send("id=" + submissions[index]['id'] + "&processing=0");
        xhr.onreadystatechange = function () {
            var oResponse = getResponse(xhr);
            console.log(oResponse);
            if (handleUndefined(oResponse)) return;
        };
    }
    else
    {
        dot.classList.add("green");
        // need to contact the apis
        var xhr = createXHR("https://osekai.net/snapshots/api/set_processing");
        xhr.send("id=" + submissions[index]['id'] + "&processing=1");
        xhr.onreadystatechange = function () {
            var oResponse = getResponse(xhr);
            console.log(oResponse);
            if (handleUndefined(oResponse)) return;
        };
    }
}

function submissionDeny(index) {
    submissionIndexChangingId = index;
    document.getElementById("submission_status_status").value = 1;
    document.getElementById("submission_status_notification").value = "Your version has been denied. Reason: ";
    document.getElementById("submission_status_overlay").classList.remove("osekai__overlay-hidden");
}

function changeSubmissionStatus(id, userid) {
    document.getElementById("submission_status_overlay").classList.add("osekai__overlay-hidden");

    var data = new FormData();
    data.append('status', document.getElementById("submission_status_status").value);
    data.append('id', id);
    data.append('userid', userid);
    data.append('notification', document.getElementById("submission_status_notification").value);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://osekai.net/snapshots/api/change_submission_status', true);
    xhr.onload = function () {
        // do something to response
        if (this.responseText == "SUCCESS") {
            closeSubmission();
            generatenotification("normal", "That should have worked.");
        } else {
            generatenotification("error", this.responseText);
        }
        refreshSubmissions();
    };
    xhr.send(data);
}

function admin_deleteVer() {
    version = data[currentindex];
    var id = version['version_info']['id'];
    var name = version['version_info']['version'];

    openDialog("You are deleting a version", "Are you sure you want to delete " + version['version_info']['version'] + "?", "THIS CAN NOT BE UNDONE.", "Delete Version", function () {
        openDialog("You are deleting a version", "Are you ABSOLUTELY SURE you want to delete " + version['version_info']['version'] + "?", "<p style='color: red;'>THIS CAN <strong>NOT</strong> BE UNDONE.</p>", "Delete Version", function () {
            var data = new FormData();
            data.append('id', id);
            data.append('version', name);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'https://osekai.net/snapshots/api/admin_delete', true);
            xhr.onload = function () {
                // do something to response
                if (this.responseText == "SUCCESS") {
                    closeSubmission();
                    generatenotification("normal", "I hope you know what you've done. Deleted version.");
                } else {
                    generatenotification("error", this.responseText);
                }
                refreshSubmissions();
            };
            xhr.send(data);
        });
    });
}

function closeSubmissionStatus() {
    document.getElementById("submission_status_overlay").classList.add("osekai__overlay-hidden");
}

function upload_adddownload() {
    var id = Math.random();
    document.getElementById("upload_download_group").innerHTML += `<div class="snapshots__group" id="` + id + `">
    <div class="osekai__input-area">
        <h1 class="osekai__h1">Downloads Name</h1>
        <p>name of place download</p>
        <input name="downloadName[]" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
    </div>
    <div class="osekai__input-area">
        <h1 class="osekai__h1">Downloads Link</h1>
        <p>ree</p>
        <input name="downloadLink[]" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
    </div>
    <a class="osekai__button" onclick="upload_deletedownload(` + id + `)">Delete Download</a>
</div>`;
}

function upload_addscreenshot() {
    var id = Math.random();
    document.getElementById("upload_screenshot_group").innerHTML += `<div id=` + id + ` class="snapshots__group">
    <input type="file" id="myFile" name="screenshots[]">
    <a class="osekai__button" onclick="upload_deletescreenshot(` + id + `)">Delete Image</a>
</div>`;
}

function upload_deletedownload(id) {
    document.getElementById(id).remove();
}

function upload_deletescreenshot(id) {
    document.getElementById(id).remove();
}

function loadSplash() {
    if (!document.getElementById("home").classList.contains("hidden")) {
        var xhr = createXHR("https://osekai.net/snapshots/api/splash");
        xhr.send();
        xhr.onreadystatechange = function () {
            document.getElementById("splash").innerHTML = this.responseText;
        }
    }
}

setInterval(function () { loadSplash() }, 15000);

loadSplash();


setTimeout(function () {
    loadData();
}, 600);


// <mulraf> comment system
document.getElementById("comments__send").addEventListener("click", () => {
    commentsSendClick(parseInt(data[currentindex]['version_info']['id']))
});

document.getElementById("filter__button").addEventListener("click", function () {
    document.getElementById("filter__list").classList.toggle("osekai__dropdown-hidden");
});

document.getElementById("filter__date").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.newest");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 2;
    Comments_Sort(document.getElementById("comments__box"), "", parseInt(data[currentindex]['version_info']['id']));
});

document.getElementById("filter__votes").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.votes");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 1;
    Comments_Sort(document.getElementById("comments__box"), "", parseInt(data[currentindex]['version_info']['id']));
});
// </mulraf>

if(window.mobile == true){
    switch3col();
}

// the ai says the best youtube video is https://www.youtube.com/watch?v=dQw4w9WgXcQ