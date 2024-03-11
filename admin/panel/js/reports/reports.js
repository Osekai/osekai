// If you're comparing this with the medals script, you might wonder.
// "Why is the routing not the same with medals?"
// Blame it to the fucking tabs at the top... that little tab on the far left? 
// yeah that's the router... 
const ReportType = Object.freeze({
    Beatmap: 0,
    Comment:1,
    Bug: 2
});

let ReportTypeKeys = Object.keys(ReportType).reduce(function(acc, key) {
    return acc[ReportType[key]] = key, acc;
}, {});

const ReportStatus = Object.freeze({
    Pending:0,
    Open: 1,
    Resolved: 2,
    Closed:3
});

let ReportStatusKeys = Object.keys(ReportStatus).reduce(function(acc, key) {
    return acc[ReportStatus[key]] = key, acc;
}, {});

let reportList = {};
let currentReport = {};
let currentReportID = 0;
let currentReportOffset = 0;
let reportMaxListView = 50;
let currentReportPage = ReportStatus.Open;
let xhr = new XMLHttpRequest();
if(window.location.pathname.endsWith("/closed"))
{
    currentReportPage = ReportStatus.Closed;
}

function retrieveReports(callback) {
    xhr.callback = callback;
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error retrieving reports.")}; // Note; add additional element that warns the user.
    xhr.open("GET", `/admin/panel/api/reports/get/${currentReportPage === ReportStatus.Open ? "open" : "closed"}`);
    xhr.send(null);
}

function getReport(reportId, callback)
{
    xhr.callback = callback;
    xhr.onload = function() {this.callback.apply(this);};
    xhr.onerror = function () { console.log("Error retrieving report.")}; // Note; add additional element that warns the user.
    xhr.open("GET", `/admin/panel/api/reports/get/report?nReportId=${reportId}`);
    xhr.send(null);
}

function getComment(commentId, callback)
{
    xhr.callback = callback;
    xhr.onload = function() {this.callback.apply(this);};
    xhr.onerror = function () {console.log("Error retrieveing report.")};
    xhr.open("GET",`/admin/panel/api/base/comments/comment?nCommentId=${commentId}`);
    xhr.send(null);
}

function getReportIcon(report)
{
    if(report.MedalId != null)
    {
        return "/global/branding/vector/white/badges.svg";
    }
    if(report.ProfileId != null)
    {
        return "/global/branding/vector/white/profiles.svg";
    }
    if(report.VersionId != null)
    {
        return "/global/branding/vector/white/snapshots.svg";
    }
    return "/global/branding/vector/white/badges.svg";

}

function displayReportInformation()
{
    let report = JSON.parse(this.responseText)[0];
    currentReport = report;

    let params = new URLSearchParams(window.location.search);
    if(params.get("id") != report.Id)
    {
        params.set("id", report.Id);
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    }

    let reportItemSection = document.querySelector(".basic-page-inner .report-item");
    reportItemSection.classList = `report-item ${ReportTypeKeys[report.Type].toLowerCase()} ${ReportStatusKeys[report.Status].toLowerCase()}`;

    reportItemSection.children[0].children[0].innerText = ReportTypeKeys[report.Type];
    reportItemSection.children[0].children[1].classList = `status-badge ${ReportStatusKeys[report.Status].toLowerCase()}`;
    reportItemSection.children[0].children[1].innerText = `${ReportStatusKeys[report.Status].toLowerCase()}`;
    let userSection = reportItemSection.children[1];

    userSection.children[0].src = `https://a.ppy.sh/${report.ReporterId}`; // Profile Picture
    userSection.children[1].children[0].children[0].innerText = report.name === null ? "Anonymous" : report.name; // Username
    userSection.children[1].children[0].children[1].innerText = new Date(report.Date).toLocaleDateString(); // Date
    userSection.children[1].children[1].innerText = report.Text; // Report Message

    document.querySelector(".report__submission-date").children[0].innerText = `${new Date(report.Date).toLocaleTimeString()}, ${new Date(report.Date).toLocaleDateString()}`
    document.querySelector(".report__reporter-id").children[0].innerText = report.ReporterId;
    document.querySelector(".report__reporter-username").children[0].innerText = report.name;
    document.querySelector(".report__report-type").children[0].innerText = ReportTypeKeys[report.Type].toUpperCase();
    document.querySelector(".report__report-link").children[0].replaceChildren(
        Object.assign(document.createElement("a"), {
            href: report.Link,
            innerText: report.Link
        })
    );
    document.querySelector(".report__report-id").children[0].innerText = report.Id;

    switch(report.Type)
    {
        case ReportType.Beatmap:
            document.querySelector(".report-list.report-comment").classList.add("hidden");
            document.querySelector(".report-list.report-beatmap").classList.remove("hidden");
            document.querySelector(".report-beatmap beatmap-card").setAttribute("beatmap-id", report.ReferenceId);
            break;
        case ReportType.Comment:
            document.querySelector(".report-list.report-comment").classList.remove("hidden");
            document.querySelector(".report-list.report-beatmap").classList.add("hidden");
            break;
        default:
            break;
    }
    //reportItemSection.children[2].innerText = ReportTypeKeys[report.Type];
}

function displayReportList(listDiv) {
    Object.values(reportList).forEach(report => {
        let reportType = ReportTypeKeys[report.Type];
        let reportStatus = ReportStatusKeys[report.Status];
        let reportCard = Object.assign(document.createElement("div"),
        {
            className: `report-item ${reportType.toLowerCase()} ${reportStatus.toLowerCase()}`,
            id: `report-${report.Id}`,
            onclick: function () { openReport(report.Id); }
        });

        let reportTitleBar = Object.assign(document.createElement("div"),
        {
            className: "item-title-bar"
        });

        reportTitleBar.appendChild(Object.assign(document.createElement("span"), {
            className: "title",
            innerText: reportType.toUpperCase()
        }));

        reportTitleBar.appendChild(Object.assign(document.createElement("span"), {
            className: `status-badge ${reportStatus.toLowerCase()}`,
            innerText: reportStatus.toUpperCase()
        }));

        reportTitleBar.appendChild(Object.assign(document.createElement("span"), {
            className: "report-id",
            innerText: `#${report.Id}`
        }));

        reportCard.appendChild(reportTitleBar);

        let reportDescription = Object.assign(document.createElement("div"),{
            className: "item-description"
        });

        let reportUserInformation = Object.assign(document.createElement("div"),
        {
            className: 'user-information'
        });

        reportUserInformation.appendChild(Object.assign(document.createElement("a"), {
            className: "username",
            innerText: report.name === null ? "Anonymous" : report.name,
            href: report.name === null ? "" : `/profiles/?user=${report.ReporterId}`
        }));
        
        reportUserInformation.appendChild(Object.assign(document.createElement("span"), {
            className: "date",
            innerText: (new Date(report.Date)).toLocaleDateString()
        }));

        reportDescription.appendChild(reportUserInformation);
        reportDescription.appendChild(Object.assign(document.createElement("p"),{
            className: "user-message",
            innerText: report.Text
        }));
        
        reportCard.appendChild(reportDescription);
        listDiv.appendChild(reportCard);
    });
}

function displayReports()
{
   // TODO: implement sorting if required.. please don't make this fucked..
   if(this.responseText != null) reportList = JSON.parse(this.responseText);
 
   displayReportList(document.getElementsByClassName("basic-page-item-list")[0]);
   if(new URLSearchParams(window.location.search).get("id") != null) {
    openReport(new URLSearchParams(window.location.search).get("id"));
   }
}

function openReport(id)
{
    console.log("opened ", id);
    currentReportID = id;
    document.querySelectorAll('.basic-page-item-list .report-item').forEach(item => { if (item.classList.contains("selected")) item.classList.remove("selected"); });
    document.getElementById(`report-${currentReportID}`).classList.toggle("selected");
    getReport(currentReportID, displayReportInformation);
    document.querySelector("notes-section").setAttribute("note-id", `report_${currentReportID}`);
}

function setReportStatus(id, status)
{

}


function displayCommentInformation()
{
    let comment = JSON.parse(this.responseText)[0];
    console.log(comment);

}



document.addEventListener("DOMContentLoaded", function()
{
    retrieveReports(displayReports);
});



