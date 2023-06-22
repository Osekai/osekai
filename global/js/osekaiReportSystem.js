var currentlyReporting = false;
var type = "";
var id = 0;

var reportMapping = {
    "beatmap": 0,
    "comment": 1,
    "bug": 2,
}

async function doReport(dtype, did) {
    await loadSource("report");

    document.getElementById("osekai__modal-overlay").classList.remove("osekai__modal-overlay--hidden");
    type = dtype;
    id = did;
    currentlyReporting = true;

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
    document.getElementById("osekai__modal-overlay").classList.add("osekai__modal-overlay--hidden");
    currentlyReporting = false;
    document.getElementById("report_comment_text").value = "";
    document.getElementById("osekai__modal-overlay-success").classList.remove("osekai__modal-overlay--hidden");

    document.getElementById("finished-text").innerHTML = GetStringRawNonAsync("report", "new." + type + ".finished");
    document.getElementById("finished-header").innerHTML = GetStringRawNonAsync("report", "new.finished.title");
    
    if(type == "bug")
    {
        document.getElementById("finished-button").innerHTML = GetStringRawNonAsync("report", "new." + type + ".finished.button");
    } else {
        document.getElementById("finished-button").innerHTML = GetStringRawNonAsync("report", "new.finished.button");
    }

    var data = new FormData();
    var xhr = new XMLHttpRequest();

    data.append("reportType", reportMapping[type]);
    data.append("reportText", reason);
    data.append("reportLink", window.location.href);
    data.append("refId", id);

    xhr.open("POST", "/api/sendReport.php", true)
    xhr.send(data);

    type = "";
    id = 0;
    extras = {};
}

function closeReportSuccess() {
    document.getElementById("osekai__modal-overlay-success").classList.add("osekai__modal-overlay--hidden");
}

function cancelReport() {
    document.getElementById("osekai__modal-overlay").classList.add("osekai__modal-overlay--hidden");
    currentlyReporting = false;

    document.getElementById("report_comment_text").value = "";
    // do this last, because if you're logged out it doesnt exist and breaks the close button
}