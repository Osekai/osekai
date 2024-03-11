const MedalPages = {
    Beatmaps: "beatmaps",
    Details: "details"
}
let unsortedList = {};
let medalList = {};
let currentMedalID = 0;
let currentMedal = {};
let currentGroupBy = "categories";
let currentSortBy = "default";
const sortByOptions = {
    "id": `Medal ID <i class="fas fa-arrow-down"></i>`,
    "id-inverse": `Medal ID <i class="fas fa-arrow-up"></i>`,
    "alpha": `A-Z <i class="fas fa-arrow-down"></i>`,
    "alpha-inverse": `A-Z <i class="fas fa-arrow-up"></i>`,
    "rarity": `Rarity <i class="fas fa-arrow-down"></i>`,
    "rarity-inverse": `Rarity <i class="fas fa-arrow-up"></i>`,
    "default": `Default`
}

const medalGroups = {
    "Hush-Hush": "hush-hush",
    "Hush-Hush (Expert)": "hush-hush-expert",
    "Skill & Dedication": "skill-and-dedication",
    "Mod Introduction": "mod-introduction",
    "Beatmap Spotlights": "beatmap-spotlights",
    "Beatmap Challenge Packs": "beatmap-challenge-packs",
    "Beatmap Packs": "beatmap-packs",
}

let currentMedalPage = window.location.pathname.endsWith("/beatmaps") ? MedalPages.Beatmaps : MedalPages.Details;

// https://stackoverflow.com/a/14696535 - need to figure a better way to seperate into seperate arrays...
function groupBy2(xs, prop) {
  var grouped = {};
  for (var i=0; i<xs.length; i++) {
    var p = xs[i][prop];
    if (!grouped[p]) { grouped[p] = []; }
    grouped[p].push(xs[i]);
  }
  return grouped;
}

function createSidebarCollapsables() {
    let sidebarList = document.querySelector(".basic-page-sidebar .basic-page-item-list");
    Object.keys(medalGroups).forEach(medalGroup => {
        let medalgroupdropdown = Object.assign(document.createElement("h2"), {
            innerText: medalGroup
        });
        medalgroupdropdown.setAttribute("collapsible-button", "");
        medalgroupdropdown.addEventListener("click", function () {
            let content = this.nextElementSibling;
            content.classList.toggle("open");
        });
        sidebarList.appendChild(
            medalgroupdropdown
        );
        sidebarList.appendChild(
            Object.assign(document.createElement("div"), {
                id: `medals__${medalGroups[medalGroup]}-list`,
                classList: `collapsible-list${medalGroup == "Hush-Hush" ? " open" : ""}`
            })
        );
    });
}

function openMedalsDropdown(id) {
    var dropdown = document.getElementById(id);
    dropdown.classList.toggle("basic-page-dropdown-hidden");
    // find the sibling of id that is a p elemnt and toggle the i element's class within the p's element
    let dropdownButton = document.getElementById(id).previousElementSibling;
    dropdownButton.children[0].lastElementChild.classList.toggle("fa-chevron-down");
    dropdownButton.children[0].lastElementChild.classList.toggle("fa-chevron-up");
}

function displayMedals() {
    if (this.responseText != null) {
        medalList = JSON.parse(this.responseText);
        unsortedList = JSON.parse(this.responseText); // This NEEDS TO BE DONE to create a copy of the object, not a reference to it
        // Why has javascript got to be so weird

        // We need to sort this into their own arrays?
        medalList.sort((a,b) => Object.keys(medalGroups).indexOf(a.Grouping) - Object.keys(medalGroups).indexOf(b.Grouping));
        unsortedList.sort((a,b) => Object.keys(medalGroups).indexOf(a.Grouping) - Object.keys(medalGroups).indexOf(b.Grouping));
        medalList = groupBy2(medalList, 'Grouping');
        unsortedList = groupBy2(unsortedList, 'Grouping');
    }
    if (currentGroupBy == "categories") {
        if (!document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.add("hidden");

        Object.keys(medalList).forEach(medalSection => {
            console.log(medalSection);
            console.log(`medals__${medalGroups[medalSection]}-list`);
            displayMedalList(document.getElementById(`medals__${medalGroups[medalSection]}-list`), medalList[medalSection]);
        });
    }
    if (currentGroupBy == "none") {
        [...document.querySelectorAll(".collapsable-list")].forEach(list => { list.classList.add("hidden"); list.classList.remove("open"); });
        [...document.querySelectorAll("h2[collapsible-button]")].forEach(button => { button.classList.add("hidden"); });
        if (document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.remove("hidden");
        displayMedalList(document.getElementById("medals__none-list"), medalList);
    }
    // Here, we need to check the url for a medal ID, and if it exists, we need to select that medal.
    // placed here due to the fact that the list is loaded asynchronously and that the selection part has to pick something that doesn't exist yet
    if (new URLSearchParams(window.location.search).get("id") != null) {
        // Oh yeah... forgot that if the user has already modified any information in the middle screen. 
        // It would be overwritten.. oh well... shucks.
        retrieveMedal(new URLSearchParams(window.location.search).get("id"));
    }
}

function combineMedalList(medalsList) {
    let combinedMedalList = [];
    Object.keys(medalsList).forEach(medalSection => {
        combinedMedalList = combinedMedalList.concat(medalsList[medalSection]);
    });
    return combinedMedalList;
}



function groupMedals(groupby) {
    currentGroupBy = groupby;
    updateGroupByDropdown();
    clearMedalLists();
    if (currentGroupBy == "categories") {
        [...document.querySelectorAll(".collapsable-list")].forEach(list => { list.classList.remove("hidden"); });
        [...document.querySelectorAll("h2[collapsible-button]")].forEach(button => { button.classList.remove("hidden"); });
        if (!document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.add("hidden");

        Object.keys(medalList).forEach(medalSection => {
            console.log(medalSection);
            console.log(`medals__${medalGroups[medalSection]}-list`);
            displayMedalList(document.getElementById(`medals__${medalGroups[medalSection]}-list`), medalList[medalSection]);
        });
    }
    if (currentGroupBy == "none") {
        [...document.querySelectorAll(".collapsable-list")].forEach(list => { list.classList.add("hidden"); });
        [...document.querySelectorAll("h2[collapsible-button]")].forEach(button => { button.classList.add("hidden"); });
        if (document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.remove("hidden");
        displayMedalList(document.getElementById("medals__none-list"), medalList);
    }
}

function sortMedals(sortby) {
    currentSortBy = sortby;
    updateSortByDropdown();
    clearMedalLists();
    if (currentGroupBy == "categories") {
        [...document.querySelectorAll(".collapsable-list")].forEach(list => { list.classList.remove("hidden"); });
        [...document.querySelectorAll("h2[collapsible-button]")].forEach(button => { button.classList.remove("hidden"); });
        if (!document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.add("hidden");

        Object.keys(medalList).forEach(medalSection => {
            console.log(medalSection);
            console.log(`medals__${medalSection.toLowerCase().replace(" ", "-")}-list`);
            displayMedalList(document.getElementById(`medals__${medalGroups[medalSection]}-list`), medalList[medalSection]);
        });
    }
    if (currentGroupBy == "none") {
        [...document.querySelectorAll(".collapsable-list")].forEach(list => { list.classList.add("hidden"); });
        [...document.querySelectorAll("h2[collapsible-button]")].forEach(button => { button.classList.add("hidden"); });
        if (document.getElementById("medals__none-list").classList.contains("hidden")) document.getElementById("medals__none-list").classList.remove("hidden");
        displayMedalList(document.getElementById("medals__none-list"), medalList);
    }
}

function updateGroupByDropdown() {
    // set text to uppercase first letter
    let text = currentGroupBy.charAt(0).toUpperCase() + currentGroupBy.slice(1);
    document.getElementById("medals__group-by-text").innerHTML = text + `<i class="fas fa-chevron-down basic-page-dropdown-chevron"></i>`;

    var dropdown = document.getElementById("dropdown-groupby");
    for (var i in dropdown.children) {
        try {
            if (dropdown.children[i].innerHTML == text) {
                dropdown.children[i].classList.add("dropdown-item-active");
            } else {
                dropdown.children[i].classList.remove("dropdown-item-active");
            }
        } catch (e) {
            // it's fine, ignore
        }
    }
}

function updateSortByDropdown() {
    // set text to value of sortByOptions with currentSortBy as key
    let text = sortByOptions[currentSortBy];
    document.getElementById("medals__order-by-text").innerHTML = text + `<i class="fas fa-chevron-down basic-page-dropdown-chevron"></i>`;
    var dropdown = document.getElementById("dropdown-sortby");
    for (var i in dropdown.children) {
        try {
            if (dropdown.children[i].innerHTML == text) {
                dropdown.children[i].classList.add("dropdown-item-active");
            } else {
                dropdown.children[i].classList.remove("dropdown-item-active");
            }
        } catch (e) {
            // it's fine, ignore
        }
    }
}

function clearMedalLists() {
    // remove all .detailed-list-item elements
    [...document.querySelectorAll(".detailed-list-item")].forEach(item => { item.remove(); });
}


function displayMedalList(listDiv, medals) {
    let medalsList = currentGroupBy == "none" ? combineMedalList(medals) : medals;
    if (currentSortBy == "default" && unsortedList["Hush-Hush"][0].Name != "Jackpot") {
        throw ("The unsorted list has been modified");
    }
    // Sorting time!!! yay...
    switch (currentSortBy) {
        case "alpha":
            medalsList.sort((a, b) => {
                if (a.Name < b.Name) { return -1; }
                if (a.Name > b.Name) { return 1; }
                return 0;
            });
            break;
        case "alpha-inverse":
            medalsList.sort((a, b) => {
                if (a.Name < b.Name) { return 1; }
                if (a.Name > b.Name) { return -1; }
                return 0;
            });
        case "id-inverse":
            medalsList.sort((a, b) => b.MedalID - a.MedalID);
            break;
        case "id":
            medalsList.sort((a, b) => a.MedalID - b.MedalID);
            break;
        case "rarity":
            medalsList.sort((a, b) => {
                if (a.Rarity < b.Rarity) { return -1; }
                if (a.Rarity > b.Rarity) { return 1; }
                return 0;
            });
            break;
        case "rarity-inverse":
            medalsList.sort((a, b) => {
                if (a.Rarity < b.Rarity) { return 1; }
                if (a.Rarity > b.Rarity) { return -1; }
                return 0;
            });
            break;
        case "default":
            // This is way too much for anyone to understand. I'm sorry.
            // But the medalsList decides to hold ALL medals, not just the ones in the current category.
            medalsList = currentGroupBy == "none" ? combineMedalList(unsortedList) : unsortedList[medals[0].Grouping];
            break;
        default:
            break;
    }
    Object.values(medalsList).forEach(medal => {
        // TODO(?): [Hubz] detailed-list-item should be a medals__-specific class
        // we're not going to use that layout of item anywhere else. all the other
        // lists have wildly different types of info to display. might be worth
        // having a generic "list-item" class, which sets the background colour, and a
        // "list-item-selected" class, which sets the "selected" border

        let medalClass = document.createElement("div");
        Object.assign(
            medalClass,
            {
                className: "detailed-list-item" + (medal.Locked ? " locked" : ""),
                id: "list-item-" + medal.MedalID,
                onclick: function () { retrieveMedal(medal.MedalID); }
            }
        );

        // Medal Top Container //
        let medalTop = medalClass.appendChild(Object.assign(
            document.createElement("span"),
            {
                className: "item-top"
            }
        ));

        // Medal Icon //
        medalTop.appendChild(
            Object.assign(document.createElement("img"),
                {
                    src: medal.Link
                }
            )
        );

        // Medal Texts Container //
        let medalTexts = medalTop.appendChild(Object.assign(
            document.createElement("span"),
            {
                className: "item-texts"
            }
        ));

        // Medal Name //
        medalTexts.appendChild(
            Object.assign(
                document.createElement("span"),
                {
                    className: "item-name",
                    innerText: medal.Name
                }
            )
        );

        // Medal Statistics //

        // TODO: Should be filled in.. please
        medalTexts.appendChild(
            Object.assign(
                document.createElement("span"),
                {
                    className: "item-statistics",
                    innerHTML: `Rarity ${medal.Rarity ?? "not calculated"}`
                }
            )
        );

        // Medal Verification //
        // ? note: you can just, well, not add this element when on lower levels of info display
        // ? should look fine with the css i've done
        medalClass.appendChild(
            Object.assign(
                document.createElement("div"),
                {
                    className: "item-info",
                    innerHTML: `<div class='medals__infobadge medals__infobadge-${medal.Solution != "" ? "complete" : "incomplete"}'>Solution</div>
                     <div class="medals__infobadge ${medal.Mods != "" && medal.Mods != null ? "medals__infobadge-complete" : ""}">Mods</div>
                     <div class="medals__infobadge ${medal.PackID != null && medal.PackID != "0" && medal.PackID != "" ? "medals__infobadge-complete" : ""}">Pack ID</div>
                     <div class="medals__infobadge ${medal.Video ? "medals__infobadge-complete" : ""}">Video</div>
                    <div class='medals__infobadge medals__infobadge-${medal.Date != "0000-00-00" ? "complete" : "incomplete"}'>Date</div>
                    <div class='medals__infobadge medals__infobadge-${medal.FirstAchievedDate ? "complete" : "incomplete"}'>Date Achieved</div>
                    <div class='medals__infobadge medals__infobadge-${medal.FirstAchievedBy ? "complete" : "incomplete"}'>Achieved ID</div>`
                }
            )
        );

        listDiv.appendChild(medalClass);
    });

}

function retrieveMedals(callback) {
    let xhr = new XMLHttpRequest();
    xhr.callback = callback;
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error retrieving medals."); };

    // TODO: Change to relative path.
    xhr.open("GET", "/admin/panel/api/apps/medals/get/medals");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(null);
}

function retrieveMedal(medalId) {
    document.querySelectorAll('.detailed-list-item').forEach(item => { if (item.classList.contains("selected")) item.classList.remove("selected"); });
    currentMedalID = medalId;
    document.getElementById("list-item-" + medalId).classList.toggle("selected");
    // TODO: Add loading system for medal info.
    getMedal(medalId, displayMedalInformation);
    document.querySelector("notes-section").setAttribute("note-id", `medal_${medalId}`);
}

function getMedal(medalId, callback) {
    let xhr = new XMLHttpRequest();
    xhr.callback = callback;
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error retrieving medal."); };

    xhr.open("GET", "/admin/panel/api/apps/medals/get/medal?id=" + medalId);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(null);
}

function saveMedal(medal, callback) {
    let xhr = createXHR("/admin/panel/api/apps/medals/save/medal?id=" + medal.MedalID);
    xhr.callback = callback;
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error saving medal."); };
    xhr.send(
        `strSolutionMods=${medal.Mods}&strNewSolution=${encodeURIComponent(medal.Solution)}&strSolutionPackID=${medal.PackID}&strSolutionVideo=${medal.Video}&strFirstAchievedDate=${medal.FirstAchievedDate}&strFirstAchievedId=${medal.FirstAchievedBy}&nMedalId=${medal.MedalID}&bBeatmapLockState=${medal.Locked}&strSolutionDate=${medal.Date}&strSolutionLazer=${medal.Lazer}`
    );
}

function saveMedalBeatmaps(medal) {
    let oldMedal;
    console.log(medalList);
    for(var _cat in medalList) {
        var cat = medalList[_cat];
        for (let m of cat) {
            if (m.MedalID == medal.MedalID) {
                oldMedal = m;
            }
        }
    }
    if (medal.DeletedBeatmaps.length > oldMedal.DeletedBeatmaps) {
        // We know there are new deleted beatmaps.. gotta find what ones are added and call the "saveDeleteBeatmap" function for each one.
        let newDeletedBeatmaps = medal.DeletedBeatmaps.filter(db => !oldMedal.DeletedBeatmaps.includes(db));
        newDeletedBeatmaps.forEach(db => {
            saveDeleteBeatmap(db);
        });
    }
    if (medal.Beatmaps.length > oldMedal.Beatmaps) {
        // We know there are new beatmaps.. gotta find what ones are added and call the "saveBeatmap" function for each one.
        let newBeatmaps = medal.Beatmaps.filter(b => !oldMedal.Beatmaps.includes(b));
        newBeatmaps.forEach(b => {
            saveRestoreBeatmap(b);
        });
    }
}

function saveDeleteBeatmap(Id) {
    let xhr = createXHR("/admin/panel/api/apps/medals/beatmap/delete");
    xhr.onload = function () { console.log("Deleted beatmap."); };
    xhr.onerror = function () { console.log("Error deleting beatmap."); };
    xhr.send(`strDeletion=${Id}`);
}

function saveRestoreBeatmap(Id) {
    let xhr = createXHR("/admin/panel/api/apps/medals/beatmap/restore");
    xhr.onload = function () { console.log("Restored beatmap."); };
    xhr.onerror = function () { console.log("Error restoring beatmap."); };
    xhr.send(`nId=${Id}`);
}

function displayMedalInformation() {
    let medal = JSON.parse(this.responseText);
    currentMedal = medal;

    let params = new URLSearchParams(window.location.search);
    if (params.get("id") != medal.MedalID) {
        params.set("id", medal.MedalID);
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    }

    document.querySelector(".medals__medal-info").classList.remove("hidden");
    document.querySelector(".medals__medal-info-title").innerText = medal.Name;
    document.querySelector(".medals__medal-info-description").innerText = medal.Description;
    document.querySelector(".medals__medal-info img").src = medal.Link;
    document.querySelector(".medals__medal-info-solution").innerHTML = medal.Solution != null ? medal.Solution.replaceAll("\n", "<br>") : "";

    // Base Details //
    document.getElementById("medals__medal-solution-textarea").value = medal.Solution != null ? medal.Solution.replaceAll("<br>", "\n") : "";
    document.getElementById("medals__medal-solution-video").value = medal.Video;

    // Extra Info //
    document.getElementById("medals__addition-date").value = medal.Date;

    document.getElementById("medals__first-achieved-date").value = medal.FirstAchievedDate;
    document.getElementById("medals__first-achieved-user").value = medal.FirstAchievedBy;

    // Beatmap Options //
    document.getElementById("medals__lock-submissions").checked = medal.Locked === 1 ? "checked" : ""; // TODO: Why no work?!?
console.log(medal);

    document.getElementById("medals__lazer").checked = medal.Lazer === 1 ? "checked" : ""; 

    if (medal.PackID != null && medal.PackID != "" && medal.PackID != "0") // Thrill of Chaos uses "0" for some reason.
    {
        if (document.getElementById("medals__beatmap-packs").checked == false)
            document.getElementById("medals__beatmap-packs").click();
        let packs = medal.PackID.split(",");
        document.getElementById("medals__beatmap-pack-osu").value = packs[0] == undefined ? "" : packs[0];
        document.getElementById("medals__beatmap-pack-taiko").value = packs[1] == undefined ? "" : packs[1];
        document.getElementById("medals__beatmap-pack-catch").value = packs[2] == undefined ? "" : packs[2];
        document.getElementById("medals__beatmap-pack-mania").value = packs[3] == undefined ? "" : packs[3];
    } else {
        if (document.getElementById("medals__beatmap-packs").checked == true)
            document.getElementById("medals__beatmap-packs").click();
        document.getElementById("medals__beatmap-pack-osu").value = "";
        document.getElementById("medals__beatmap-pack-taiko").value = "";
        document.getElementById("medals__beatmap-pack-mania").value = "";
        document.getElementById("medals__beatmap-pack-catch").value = "";
    }

    document.getElementById("medals__medals-link").href = `/medals?medal=${encodeURIComponent(medal.Name)}`;

    // Mods //
    // Clear all mod switches //
    document.querySelectorAll(".medals__modswitch").forEach(modSwitch => {
        modSwitch.classList.remove("active");
    });

    if (medal.Mods != null && medal.Mods != "") {
        // If medal.Mods has no commas, split per 2 characters instead
        if (medal.Mods.indexOf(",") == -1) {
            let mods = medal.Mods.match(/.{2}/g);
            mods.forEach(mod => {
                toggleModSwitch(mod);
            });
        } else {
            medal.Mods.split(",").forEach(mod => {
                toggleModSwitch(mod);
            });
        }
    }
    // Beatmaps
    if (currentMedalPage == MedalPages.Beatmaps) {
        displayBeatmaps();
    }

}

function displayModSwitches() {
    let modSwitches = document.querySelector(".medals__modswitches");
    colMods.forEach(mod => {
        modSwitches.appendChild(
            Object.assign(
                document.createElement("div"),
                {
                    className: "medals__modswitch",
                    id: `medals__modswitch-${mod.toLowerCase()}`,
                    onclick: function () { toggleModSwitch(mod); },
                    innerHTML: `<h1>${mod}</h1><div/>`
                }
            )
        )
    });
}

function toggleModSwitch(mod) {
    let modSwitch = document.getElementById("medals__modswitch-" + mod.toLowerCase());
    modSwitch.classList.toggle("active");
}

function updateMedal() {
    if (currentMedalID == 0 || currentMedal == {}) return;
    document.getElementById("medals__update-button").classList.add("disabled");
    document.getElementById("medals__update-button").innerText = "Updating...";

    document.getElementById("medals__discard-button").classList.add("disabled");

    let medal = currentMedal;
    saveMedalBeatmaps(medal);
    medal.Solution = document.getElementById("medals__medal-solution-textarea").value;
    medal.Video = document.getElementById("medals__medal-solution-video").value;
    let mods = [];
    document.querySelectorAll(".medals__modswitch.active").forEach(modSwitch => {
        mods.push(modSwitch.id.replace("medals__modswitch-", "").toUpperCase());
    });
    medal.Mods = mods.join(",");
    medal.Date = document.getElementById("medals__addition-date").value;
    medal.FirstAchievedDate = document.getElementById("medals__first-achieved-date").value;
    medal.FirstAchievedBy = document.getElementById("medals__first-achieved-user").value;
    medal.Locked = document.getElementById("medals__lock-submissions").checked;
    medal.Lazer = document.getElementById("medals__lazer").checked;
    if (document.getElementById("medals__beatmap-packs").checked == true) {
        medal.PackID = `${document.getElementById("medals__beatmap-pack-osu").value},${document.getElementById("medals__beatmap-pack-taiko").value},${document.getElementById("medals__beatmap-pack-catch").value},${document.getElementById("medals__beatmap-pack-mania").value}`;
    } else {
        medal.PackID = "";
    }

    saveMedal(medal, function () {
        document.getElementById("medals__update-button").classList.remove("disabled");
        document.getElementById("medals__discard-button").classList.remove("disabled");
        document.getElementById("medals__update-button").innerText = "Update";

        // Need to "refresh" the data shown at the top info
        document.querySelectorAll(".medals__medal-info")[0].classList.remove("hidden");
        document.querySelectorAll(".medals__medal-info-title")[0].innerText = medal.Name;
        document.querySelectorAll(".medals__medal-info-description")[0].innerText = medal.Description;
        document.querySelector(".medals__medal-info img").src = medal.Link;
        document.querySelectorAll(".medals__medal-info-solution")[0].innerHTML = medal.Solution.replaceAll("\n", "<br>");
    });
}

function revertMedalInformation() {
    getMedal(currentMedalID, displayMedalInformation);
}

//#region Beatmaps

function displayBeatmaps() {
    // We already have the medal, let's grab that
    if (currentMedalID == null || currentMedal == {}) return;

    let medal = currentMedal;
    let beatmaps = medal.Beatmaps;
    let deletedBeatmaps = medal.DeletedBeatmaps;

    let beatmapList = document.querySelector(".medals__live-beatmaps-list");

    // Live beatmaps //
    beatmapList.innerHTML = "";
    beatmaps.forEach(beatmap => {
        let liveBeatmapOptions = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-options",
                innerHTML: `<a href="#" id="medals__beatmap-option-delete" class="medals__beatmap-option text-danger" onclick="deleteBeatmap(${beatmap.ID})">Delete</a><a href="#" id="medals__beatmap-option-edit" class="medals__beatmap-option" onclick="editBeatmap(${beatmap.ID})">Edit</a>`
            });
        let beatmapDetails = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-details",
                innerHTML: `<h1><strong>${beatmap.Artist}</strong> - ${beatmap.SongTitle} <strong>[${beatmap.DifficultyName}]</strong></h1><p>Submitted ${beatmap.SubmittedBy != 0 ? `by <user id="${beatmap.SubmittedBy}" class="${beatmap.SubmittedByRestrictionStatus ? "restricted" : ""}">${beatmap.SubmittedByUsername}</user> ` : ""} on <strong>${beatmap.SubmissionDate.match(/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/)}</strong></p>`
            });
        let beatmapElement = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-panel",
                id: "medals__beatmap-" + beatmap.ID,
                style: `background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%),url(https://assets.ppy.sh/beatmaps/${beatmap.MapsetID}/covers/cover.jpg),linear-gradient(#240d19,#240d19);`
            });

        beatmapElement.appendChild(beatmapDetails);
        beatmapElement.appendChild(liveBeatmapOptions);
        beatmapList.appendChild(beatmapElement);
    });

    // Deleted beatmaps //
    let deletedBeatmapList = document.querySelector(".medals__deleted-beatmaps-list");

    deletedBeatmapList.innerHTML = "";
    deletedBeatmaps.forEach(beatmap => {
        let deletedBeatmapOptions = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-options",
                innerHTML: `<a href="#" id="medals__beatmap-option-restore" class="medals__beatmap-option text-success" onclick="restoreBeatmap(${beatmap.ID})">Restore</a><a href="#" id="medals__beatmap-option-edit" class="medals__beatmap-option" onclick="editBeatmap(${beatmap.ID})">Edit</a>`
            });
        let beatmapDetails = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-details",
                innerHTML: `<h1><strong>${beatmap.Artist}</strong> - ${beatmap.SongTitle} <strong>[${beatmap.DifficultyName}]</strong></h1><p>Deleted at <strong>${beatmap.DeletionDate.match(/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/)}</strong></p>`
            });


        let beatmapElement = Object.assign(document.createElement("div"),
            {
                className: "medals__beatmap-panel medals__deleted-beatmap",
                id: "medals__beatmap-" + beatmap.ID,
                style: `background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%),url(https://assets.ppy.sh/beatmaps/${beatmap.MapsetID}/covers/cover.jpg),linear-gradient(#240d19,#240d19);`
            });

        beatmapElement.appendChild(beatmapDetails);
        beatmapElement.appendChild(deletedBeatmapOptions);
        deletedBeatmapList.appendChild(beatmapElement);
    });

    // tippy needs to be run here.. why?? don't question the spesifics..
    createUserTippys();
}

function deleteBeatmap(Id) {
    if (currentMedal.DeletedBeatmaps.find(beatmap => beatmap.ID == Id) != undefined) {
        console.log("This beatmap has already been deleted.");
        return;
    }
    currentMedal.DeletedBeatmaps.push(currentMedal.Beatmaps.find(beatmap => beatmap.ID == Id));
    currentMedal.DeletedBeatmaps.find(beatmap => beatmap.ID == Id).DeletionDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
    currentMedal.Beatmaps = currentMedal.Beatmaps.filter(beatmap => beatmap.ID != Id);
    displayBeatmaps();
}

function restoreBeatmap(Id) {
    if (currentMedal.Beatmaps.find(beatmap => beatmap.ID == Id) != undefined) {
        console.log("This beatmap has already been restored.");
        return;
    }
    currentMedal.Beatmaps.push(currentMedal.DeletedBeatmaps.find(beatmap => beatmap.ID == Id));
    currentMedal.DeletedBeatmaps = currentMedal.DeletedBeatmaps.filter(beatmap => beatmap.ID != Id);
    currentMedal.Beatmaps.find(beatmap => beatmap.ID == Id).DeletionDate = null;
    displayBeatmaps();
}

function editBeatmap(Id) {
    let beatmap = currentMedal.Beatmaps.find(beatmap => beatmap.ID == Id);
    if(beatmap == undefined) beatmap = currentMedal.DeletedBeatmaps.find(beatmap => beatmap.ID == Id);

    let editBeatmapModal = new modalPopup("Edit beatmap",
        `<div class="medals__modal-beatmap-info">
            <div>
                <p>Mapset ID</p>
                <input type="text" class="input input-pattern" id="medals__modal-mapset-id-field" value="${beatmap.MapsetID}" placeholder="None" pattern="[0-9]+">
            </div>
            <div>
                <p>Beatmap ID</p>
                <input type="text" class="input input-pattern" id="medals__modal-beatmap-id-field" value="${beatmap.BeatmapID}" placeholder="None" pattern="[0-9]+">
            </div>
        </div>
        <div class="medals__modal-beatmap-notes">
            <p>Notes</p>
            <textarea class="input input-pattern" id="medals__modal-notes-field" placeholder="None">${beatmap.Note ?? ""}</textarea>
        </div>`,
        `<a class="button button-danger">Delete Beatmap</a><a class="button">Save Changes</a>`);
}
//#endregion

function openMedalsTab(page) {
    if (page == currentMedalPage) return;

    if (page == MedalPages.Beatmaps) {
        console.log("switching to beatmaps");
        currentMedalPage = MedalPages.Beatmaps;
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}/beatmaps${window.location.search ? "?" + window.location.search.substring(1) : ""}`));
        document.getElementById("medals__details-tab").classList.remove("basic-page-tab-active");
        document.getElementById("medals__beatmaps-tab").classList.add("basic-page-tab-active");
        document.getElementById("medals__medal-beatmap-content").classList.add("basic-page-inner-content-shown");
        document.getElementById("medals__medal-details-content").classList.remove("basic-page-inner-content-shown");

        if (currentMedalID != null) {
            displayBeatmaps();
        }
    }
    if (page == MedalPages.Details) {
        console.log("switching to details");
        currentMedalPage = MedalPages.Details;
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname.replace("/beatmaps", "")}${window.location.search ? "?" + window.location.search.substring(1) : ""}`));
        document.getElementById("medals__details-tab").classList.add("basic-page-tab-active");
        document.getElementById("medals__beatmaps-tab").classList.remove("basic-page-tab-active");
        document.getElementById("medals__medal-beatmap-content").classList.remove("basic-page-inner-content-shown");
        document.getElementById("medals__medal-details-content").classList.add("basic-page-inner-content-shown");
    }
}

document.getElementById("medals__searchbar").addEventListener("input", function () {
    let medalListDivs = document.querySelectorAll(".basic-page-item-list .detailed-list-item");


    [].forEach.call(medalListDivs, listDiv => {
        listDiv.classList.remove("hidden");
    });

    let medalSearchQuery = document.getElementById("medals__searchbar").value.toLowerCase();
    console.log(medalSearchQuery);

    [].forEach.call(medalListDivs, listDiv => {
        // Check if search query (found in #txtMedalSearch's value) is contained within .item-top > .item-texts > .item-name innerText
        if (!listDiv.querySelector(".item-top .item-texts .item-name").innerText.toLowerCase().includes(medalSearchQuery)) {
            listDiv.classList.add("hidden");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // So for some reason, eph decided groups change.. and that's cool.. if this website was able to do so.. but it can't..
    createSidebarCollapsables();

    if (currentMedalPage == MedalPages.Beatmaps) {
        console.log("Beatmaps tab");
        document.getElementById("medals__details-tab").classList.remove("basic-page-tab-active");
        document.getElementById("medals__beatmaps-tab").classList.add("basic-page-tab-active");
        document.getElementById("medals__medal-beatmap-content").classList.add("basic-page-inner-content-shown");
        document.getElementById("medals__medal-details-content").classList.remove("basic-page-inner-content-shown");
    }
    retrieveMedals(displayMedals);
    displayModSwitches();
});

let beatmapPacksCheckbox = document.getElementById("medals__beatmap-packs");
beatmapPacksCheckbox.addEventListener("click", function handleClick() {
    if (beatmapPacksCheckbox.checked) {
        document.querySelector(".medals__beatmap-packs-ids").classList.add("open");
        document.getElementById("medals__lock-submissions").setAttribute("disabled", '');
        document.getElementById("medals__lock-submissions-label").classList.add("checkbox-disabled");
    } else {
        document.querySelector(".medals__beatmap-packs-ids").classList.remove("open");
        document.getElementById("medals__lock-submissions-label").classList.remove("checkbox-disabled");
        document.getElementById("medals__lock-submissions").removeAttribute("disabled");
    }
});
