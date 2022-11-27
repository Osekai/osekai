var currentlyReporting = false;
var type = "";
var id = 0;
var extras = {};

async function doReport(dtype, did, extrainfo = {}, b64extrainfo = false) {
    await loadSource("report");

    document.getElementById("osekai__report-overlay").classList.remove("osekai__report-overlay--hidden");
    type = dtype;
    id = did;
    currentlyReporting = true;
    extras = extrainfo;
    if (b64extrainfo) {
        for (var key in extrainfo) {
            extras[key] = btoa(extrainfo[key]);
        }
    }
    document.getElementById("report-title").innerHTML = GetStringRawNonAsync("report", "new." + type + ".title");
    document.getElementById("report-sub").innerHTML = GetStringRawNonAsync("report", "new." + type + ".body");
}

var typeCols = {
    "comment": "0xFF22FF",
    "beatmap": "0xFFAA22",
    "bug": "0xFF4422",
}

function sendReport() {
    if (!currentlyReporting) {
        return;
    }
    var reason = document.getElementById("report_comment_text").value;
    document.getElementById("osekai__report-overlay").classList.add("osekai__report-overlay--hidden");
    currentlyReporting = false;
    document.getElementById("report_comment_text").value = "";
    document.getElementById("osekai__report-overlay-success").classList.remove("osekai__report-overlay--hidden");

    document.getElementById("finished-text").innerHTML = GetStringRawNonAsync("report", "new." + type + ".finished");
    document.getElementById("finished-header").innerHTML = GetStringRawNonAsync("report", "new.finished.title");
    
    if(type == "bug")
    {
        document.getElementById("finished-button").innerHTML = GetStringRawNonAsync("report", "new." + type + ".finished.button");
    } else {
        document.getElementById("finished-button").innerHTML = GetStringRawNonAsync("report", "new.finished.button");
    }

    extras["reported_by"] = nUserID;
    extras["reported_by_username"] = nUsername;

    var xhr = new XMLHttpRequest();
    var url = "/api/sendReport.php";
    var colour = typeCols[type];
    var title = "Report : " + type;
    var description = "**user reason:** \n" + reason;
    var currenturl = window.location.href;
    var footer = "Report sent from " + currenturl;

    // if extras has any values, add them to the description

    description += "\n\n**extra info:**\n";
    for (var key in extras) {
        description += key + ": " + extras[key] + "\n";
    }

    // description may be url encoded, so decode it
    description = decodeURIComponent(description);


    // base 64 encode
    url += "?colour=" + btoa(colour);
    url += "&title=" + btoa(title);
    url += "&description=" + btoa(description);
    url += "&url=" + btoa(currenturl);
    url += "&footer=" + btoa(footer);
    xhr.open("GET", url, true);
    xhr.send();

    console.log(extras);

    type = "";
    id = 0;
    extras = {};
}

function closeReportSuccess() {
    document.getElementById("osekai__report-overlay-success").classList.add("osekai__report-overlay--hidden");
}

function cancelReport() {
    document.getElementById("osekai__report-overlay").classList.add("osekai__report-overlay--hidden");
    currentlyReporting = false;

    document.getElementById("report_comment_text").value = "";
    // do this last, because if you're logged out it doesnt exist and breaks the close button
}