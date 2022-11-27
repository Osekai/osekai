window.reportSys = rs_reportSys
window.rs_closeReport = rs_closeReport
window.rs_submitReport = rs_submitReport
window.rs_createXHR = rs_createXHR

function rs_createXHR(strUrl) {
    var rs_xhr = new XMLHttpRequest();
    rs_xhr.open("POST", strUrl, true);
    rs_xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    return rs_xhr;
}

function rs_reportSys(type, oid) {
    var rs_text = "Oops, something went wrong! Developer Info: Your TYPE_ID has been set wrong. Please make sure it is 0, 1, or 2."
    var rs_type_name;

    if (type == 0) {
        rs_text = "Report this beatmap";
        rs_type_name = "beatmap";
    } else if (type == 1) {
        rs_text = "Report this comment";
        rs_type_name = "comment";
    } else if (type == 2) {
        rs_text = "Report a bug on this page";
        rs_type_name = "bug";
    }

    document.getElementById("osekai__popup_overlay").innerHTML += '<div id="report_overlay" class="osekai__overlay"> ' +
        '<section class="osekai__panel osekai__overlay__panel"> ' +
        '<div class="osekai__panel-header"> ' +
        '<p><i class="fas fa-exclamation-triangle"></i> ' + rs_text + '</p> ' +
        '</div> ' + 
        '<div class="osekai__panel-inner"> ' +
        '<p class="medals__addbeatmap-ifo1"> ' +
        'You are about to report a ' + rs_type_name + '.' +
        '</p> ' +
        '<p class="medals__addbeatmap-ifo2"> ' +
        'Please give us in depth details on why you think  ' +
        '</p> ' +
        '<input id="reportReason" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="Report Reason"> ' +
        '<div class="osekai__flex_row"> ' +
        '<a class="osekai__button" onclick="rs_closeReport();">Cancel</a> ' +
        '<a class="osekai__button osekai__left" onclick="rs_submitReport(' + type + ', ' + oid + ');">Submit Report</a> ' +
        '</div> ' +
        '</div> ' +
        '</section> ' +
        '</div>';
}

function rs_closeReport(){
    document.getElementById("report_overlay").remove();
}

function rs_submitReport(type, oid){
    var inval = document.getElementById("reportReason").value;
    var url = window.location.href;
    
    console.log("url is " + url);

    var rs_xhr = rs_createXHR("/global/api/report_issue.php");
    rs_xhr.send("errortype=" + type + "&report_text=" + inval + "&currenturl=" + url + "&oid=" + oid);
    rs_xhr.onreadystatechange = function () {
        console.log(rs_xhr.response);
        // empty because we don't need to do anything with it
    };

    // [AI] Close the popup and remove the overlay and the panel at the same time delete the panel and overlay
    generatenotification("info", "Thank you for your report! We will look into it as soon as possible!");
    document.getElementById("report_overlay").remove();
}