let currentFilter = {
    user: "",
    dateAfter: "",
    dateBefore: "",
}

let userData = {};
const list = document.getElementById("restrictions__list");

function searchRestrictions() {
    list.innerHTML = "";
    spinner = new loadingSpinner(list);
    list.innerHTML += "<br>Loading...";
    currentFilter.user = document.getElementById("restrictions__filter-user").value;
    currentFilter.dateAfter = document.getElementById("restrictions__filter-date-after").value;
    currentFilter.dateBefore = document.getElementById("restrictions__filter-date-before").value;
    getUsers();
}

let xhr;
function getUsers() {
    let isID;
    let userExists;
    if(currentFilter.user != undefined && currentFilter.user != null &&  currentFilter.user != "")
        isID = currentFilter.user.match(/^[0-9]{1,8}$/);
    else userExists = false;

    xhr = createXHR("/admin/panel/api/home/restrictions/search");
    xhr.send(`${userExists ? isID ? "nUserID=" + currentFilter.user : "strName=" + encodeURIComponent(currentFilter.user) : ""}${currentFilter.dateAfter ? "&nDateAfter=" + currentFilter.dateAfter : ""}${currentFilter.dateBefore ? "&nDateBefore=" + currentFilter.dateBefore : ""}`);
    xhr.onload = function () {
        if(xhr.responseText != '')
        {
            userData = JSON.parse(xhr.responseText);
            fillList();
        } 
        else 
        {
            list.innerHTML = "No results found";
        }
    }
}

function fillList() {
    list.innerHTML = "";
    list.classList.remove("spinner-container");
    userData.forEach(user => {
        list.appendChild(
            Object.assign(document.createElement('div'),
                {
                    className: "restrictions__item",
                    innerHTML: `<div class="restrictions__item-info">
                        <div class="restrictions__user-section${user.Active ? ' restrictions__active' : ''}">
                         <img src="https://a.ppy.sh/${user.UserID}" class="restrictions__profile-image">
                         <div class="restrictions__user-info">
                            <user id=${user.UserID}>${user.Username}</user>
                            <h3>${TimeAgo.inWords(new Date(user.Time).getTime())} (${user.Time})</h3>
                         </div>
                        </div>
                        <div class="restrictions__item-actions">
                            <a class="button">Edit Restriction Details</a>
                            <a class="button button-danger" onclick="${user.Active ? "submitUnrestrict" : "restrictUser" }(${user.UserID})">${user.Active ? "Unrestrict" : "Restrict"}</a>
                        </div>
                    </div>
                    <div class="restrictions__item-reason">
                        <p>${user.Reason}</p> 
                    </div>`
                }));
    });
    createUserTippys();
}

function submitReport()
{
    document.getElementById("medals__restrict-button").disabled = true;
    document.getElementById("medals__restrict-button").innerHTML = "Submitting...";
    let reportedID = document.getElementById("restrictions__report-user-id").value;
    let reason = document.getElementById("restrictions__report-reason").value;
    xhr = createXHR("/admin/panel/api/home/restrictions/restrict");
    xhr.send(`nUserID=${reportedID}&strReason=${encodeURIComponent(reason)}`);
    xhr.onerror = function () {
        new modalPopup("Error", `The user: ${reportedID} is currently restricted.<br>Please verify you have the correct user.`, `<a class="button">OK</a>`);
        document.getElementById("medals__restrict-button").disabled = false;
        document.getElementById("medals__restrict-button").innerHTML = "Restrict";
    }
    xhr.onload = function () {
        new modalPopup("Success", `The user: ${reportedID} has been restricted.<br>Reason: ${reason}`, `<a class="button">OK</a>`);
        document.getElementById("medals__restrict-button").disabled = false;
        document.getElementById("medals__restrict-button").innerHTML = "Restrict";
    }
}

function submitUnrestrict(id)
{
    document.getElementById("")
    new modalPopup("Unrestrict User", "Currently under construction", `<a class="button">OK</a>`);
}

document.addEventListener("DOMContentLoaded", function () {
    searchRestrictions();
});