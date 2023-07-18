var colMedals = [];
var strCurrentMedalName = "";
var strCurrentMedalMode = "";
var nCurrentMedalID = 0;
let FavMedals = null;

if (!bLoggedIn) {
    document.getElementById("osekai__panel-disabled").querySelector('.osekai__flex_row').classList.add("osekai__input-disabled");
    tippy(document.getElementById("osekai__panel-disabled"), {
        content: "You must be logged in to use this feature.",
    })
}

const tx = document.getElementsByTagName("textarea");
for (let i = 0; i < tx.length; i++) {
    tx[i].addEventListener("input", OnInput, false);
}

function OnInput() {
    this.style.height = "auto";
    this.style.height = (this.scrollHeight) + "px";
}

document.addEventListener("DOMContentLoaded", function () {
    if (localStorage.getItem("medals__unobtained-medals-filter") == null || localStorage.getItem("medals__unobtained-medals-filter") == false || localStorage.getItem("medals__unobtained-medals-filter") == 'false') {
        document.getElementById("styled-checkbox-1").checked = false;
    } else {
        document.getElementById("styled-checkbox-1").checked = true;
    }
    if (new URLSearchParams(window.location.search).get("medal") == null) landingPage();
    requestMedals(true);
});

window.addEventListener('popstate', function (event) {
    let params = new URLSearchParams(window.location.search);
    if (params.get("medal") != null) {
        leaveLandingPage();
        loadMedal(params.get("medal"));
    } else {
        landingPage();
    }
    localStorage.setItem("url", location.href);
});

document.getElementById("txtMedalSearch").addEventListener("input", function () {
    requestMedals(false, document.getElementById("txtMedalSearch").value);
}, false);

document.querySelector(".medals__search__filters-icon").addEventListener("click", () => {
    // document.querySelector(".medals__search__filters").classList.toggle("hidden");
    document.querySelector(".medals__search__area").classList.toggle("medals__search__filters-open");
});

document.getElementById("styled-checkbox-1").addEventListener("change", () => {
    filterAchieved(document.getElementById("styled-checkbox-1").checked, true);
    localStorage.setItem("medals__unobtained-medals-filter", document.getElementById("styled-checkbox-1").checked);
});

document.getElementById("comments__send").addEventListener("click", () => {
    commentsSendClick()
});

document.getElementById("filter__button").addEventListener("click", function () {
    document.getElementById("filter__list").classList.toggle("osekai__dropdown-hidden");
});

document.getElementById("filter__date").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.newest");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 2;
    Comments_Sort(document.getElementById("comments__box"), nCurrentMedalID, -1, -1);
});

document.getElementById("filter__votes").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.votes");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 1;
    Comments_Sort(document.getElementById("comments__box"), nCurrentMedalID, -1, -1);
});

let MedalsAchievedFilterArray = null;
async function getMedalsFilterArray() {
    return new Promise((resolve) => {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/medals/api/filters.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onload = () => {
            if (xhr.status === 200) {
                let oResponse = JSON.parse(xhr.responseText);
                resolve(oResponse);
            }
        };
        xhr.send();
    });
}
async function filterAchieved(on, request) {
    if (MedalsAchievedFilterArray == null && on) {
        MedalsAchievedFilterArray = await getMedalsFilterArray();
    }

    if (request)
        requestMedals(false, document.getElementById("txtMedalSearch").value);


    if (on) {
        for (let i = 0; i < MedalsAchievedFilterArray.length; i++) {
            let medalid = MedalsAchievedFilterArray[i];
            if (document.getElementById('medal_' + medalid)) {
                document.getElementById('medal_' + medalid).classList.add('medals__medal-filtered');
            }
        }
        if (localStorage.getItem("settings_medals__hidemedalswhenunobtainedfilteron") == "true") {
            var filtered = document.getElementsByClassName("medals__medal-filtered");
            for (var x = 0; x < filtered.length; x++) {
                var parent = filtered[x].parentElement;
                parent.classList.add("hidden");
            }
        } else {
            var filtered = document.getElementsByClassName("medals__medal-filtered");
            for (var x = 0; x < filtered.length; x++) {
                var parent = filtered[x].parentElement;
                parent.classList.remove("hidden");
            }
        }
    } else {
        let filteredMedals = document.getElementsByClassName('medals__medal-filtered').length;
        for (let i = 0; i < filteredMedals; i++) {
            document.getElementsByClassName('medals__medal-filtered')[0].classList.remove('medals__medal-filtered');
        }
    }
}


async function initColMedals() {
    return new Promise((resolve) => {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/medals/api/medals.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function () {
            var oResponse = getResponse(xhr);
            if (handleUndefined(oResponse)) return;

            // Sort using medals_grouping_ordering
            oResponse = oResponse.sort((a, b) => medals_grouping_ordering.indexOf(a.Grouping) - medals_grouping_ordering.indexOf(b.Grouping));

            for (medal of oResponse) {
                colMedals[medal.Name] = medal;
            }
            resolve();
        };
        xhr.send('strSearch=');
    });
}

async function requestMedals(init, strValue = '', favsFilter = null) {
    if (init || Object.values(colMedals).length == 0)  // Init the colMedals object
        await initColMedals();

    let query = strValue.trim();
    while (query.includes('  ')) {
        query = query.replace('  ', ' ');
        query = query.trim();
    }

    let filteredMedalsArrayByGroup = [];
    for (let v of Object.values(colMedals)) {
        let medalMatches = false;
        if (query == '') {
            medalMatches = true;
        } else {
            let wordMatches = [];
            for (const word of query.split(' ')) {
                thisWordMatches = false;
                if (v.Mods == null) v.Mods = '';
                thisWordMatches = v.Mods.replace(',', '').toUpperCase().includes(word.toUpperCase());
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }

                thisWordMatches = v.Name.toLowerCase().includes(word.toLowerCase());
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }
                thisWordMatches = v.Solution?.toLowerCase().includes(word.toLowerCase());
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }
                thisWordMatches = v.Description?.toLowerCase().includes(word.toLowerCase());
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }
                thisWordMatches = v.Instructions?.toLowerCase().includes(word.toLowerCase());
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }
                thisWordMatches = v.MedalID == parseInt(word);
                if (thisWordMatches) {
                    wordMatches.push(true);
                    continue;
                }
                wordMatches.push(false);
            }
            medalMatches = !wordMatches.includes(false);
        }

        if (medalMatches) {
            if (filteredMedalsArrayByGroup[v.Grouping] == null) filteredMedalsArrayByGroup[v.Grouping] = [];
            filteredMedalsArrayByGroup[v.Grouping].push(v);
        }
    }
    if (MedalsAchievedFilterArray == null && document.getElementById("styled-checkbox-1").checked) {
        MedalsAchievedFilterArray = await getMedalsFilterArray();
    }
    document.getElementById(favsFilter == null ? 'oMedalSection' : 'favsSection').textContent = '';
    Object.keys(filteredMedalsArrayByGroup).forEach(async (group) => {
        let grids = [];
        filteredMedalsArrayByGroup[group].forEach((medal) => {
            if (favsFilter != null && !favsFilter.includes(parseInt(medal.MedalID)))
                return;

            if (localStorage.getItem("settings_medals__hidemedalswhenunobtainedfilteron") == "true" && document.getElementById("styled-checkbox-1").checked && MedalsAchievedFilterArray.includes(medal.MedalID)) {
                return;
            }

            let medal_grid_i = 0;
            switch (medal.Restriction) {
                case 'NULL': medal_grid_i = 0; break;
                case 'osu': medal_grid_i = 1; break;
                case 'taiko': medal_grid_i = 2; break;
                case 'fruits': medal_grid_i = 3; break;
                case 'mania': medal_grid_i = 4; break;
            }

            let medalDiv = document.createElement('div');
            medalDiv.classList.add('medals__grid-medal-container');

            if (medal.Date != null) { // It has a date!, check if its less than a week old
                let date = new Date(medal.Date);
                let now = new Date();
                let diff = now.getTime() - date.getTime();
                if (diff < 604800000) {
                    // IT IS!, add the new badge 8)
                    let newBadge = document.createElement('div');
                    newBadge.classList.add('new-badge');
                    newBadge.textContent = 'NEW';
                    medalDiv.appendChild(newBadge);
                }
            }
            let medalImg = document.createElement('img');
            medalImg.setAttribute('data-tippy-content', medal.Name);
            medalImg.classList.add('medals__grid-medal');
            medalImg.classList.add('lazy');
            medalImg.src = medal.Link;
            medalImg.alt = medal.Name;
            medalImg.id = `medal_${medal.MedalID}`;
            medalImg.onload = () => {
                medalImg.classList.add("medals__grid-medal-loaded");
            };
            medalImg.onclick = () => {
                changeState(medal.Name);
            };

            medalDiv.appendChild(medalImg);

            if (typeof grids[medal_grid_i] == 'undefined')
                grids[medal_grid_i] = [];
            grids[medal_grid_i].push(medalDiv);
        });

        let section = document.createElement('section');

        section.classList.add('osekai__panel');
        section.classList.add('osekai__panel-collapsable');
        section.classList.add("medals__medal-panel");
        section.classList.add(medals_grouping_classnames[group]);
        // Header
        let sectionHeader = document.createElement('div');
        sectionHeader.classList.add('osekai__panel-header');

        let sectionHeaderP = document.createElement('p');
        sectionHeaderP.textContent = group;

        let collapsableButton = document.createElement('div');
        collapsableButton.classList.add('osekai__panel-header-right')

        let collapsableButtonI = document.createElement('i');
        collapsableButtonI.classList.add('fas');
        collapsableButtonI.classList.add('fa-chevron-down');

        collapsableButton.appendChild(collapsableButtonI);

        sectionHeader.appendChild(sectionHeaderP);
        sectionHeader.appendChild(collapsableButton);
        section.appendChild(sectionHeader);
        // Header end

        // Medal grid
        let panelInner = document.createElement('div');
        panelInner.classList.add('osekai__panel-inner');

        let panelMedalsContainer = document.createElement('div');
        panelMedalsContainer.classList.add('medals__grid-container');

        for (let i = 0; i < grids.length; i++) {
            if (typeof grids[i] == 'undefined') continue;

            let modetxt = '';
            switch (i) {
                case 0: modetxt = "All"; break;
                case 1: modetxt = "Standard"; break;
                case 2: modetxt = "Taiko"; break;
                case 3: modetxt = "Catch"; break;
                case 4: modetxt = "Mania"; break;
            }

            let headerLeftModeDiv = document.createElement('div');
            headerLeftModeDiv.classList.add("osekai__section-header-left")

            let headerLeftMode = document.createElement('h2')
            headerLeftMode.textContent = modetxt;

            headerLeftModeDiv.appendChild(headerLeftMode);

            let headerRightModeDiv = document.createElement('div');
            headerRightModeDiv.classList.add("osekai__section-header-right")

            let headerRightMode = document.createElement('h3');

            if (grids[i].length == 1)
                headerRightMode.innerHTML = GetStringRawNonAsync("medals", "medalCount.singular", [grids[i].length])
            else
                headerRightMode.innerHTML = GetStringRawNonAsync("medals", "medalCount", [grids[i].length])

            headerRightModeDiv.appendChild(headerRightMode);

            let dividerDiv = document.createElement('div');
            dividerDiv.classList.add('osekai__section-header');
            dividerDiv.style.marginBottom = "10px";

            dividerDiv.appendChild(headerLeftModeDiv);
            dividerDiv.appendChild(headerRightModeDiv);

            panelMedalsContainer.appendChild(dividerDiv);

            let medalsContainer = document.createElement('div');
            medalsContainer.classList.add('medals__grid');
            if (i !== grids.length - 1) {
                medalsContainer.style.paddingBottom = "20px";
            }

            for (let j = 0; j < grids[i].length; j++)
                medalsContainer.appendChild(grids[i][j]);
            panelMedalsContainer.appendChild(medalsContainer);
        }
        if (grids.length != 0) {
            panelInner.appendChild(panelMedalsContainer);
            section.appendChild(panelInner);
            // Medal grid end
            document.getElementById(favsFilter == null ? 'oMedalSection' : 'favsSection').appendChild(section);
        }
    });
    setTimeout(() => {
        tippy('[data-tippy-content]', {
            appendTo: document.getElementById(favsFilter == null ? 'oMedalSection' : 'favsSection'),
            arrow: true
        });
    }, 0);
    filterAchieved(document.getElementById('styled-checkbox-1').checked, false);

    if (init && new URLSearchParams(window.location.search).get('medal') !== null) loadMedal(new URLSearchParams(window.location.search).get('medal'));
}
function landingPage() {
    document.getElementById("expandbutton").classList.add("hidden");
    set_breadcrums("{app}");
    document.getElementById("osekai__col1").classList.add("medals__nomedal");
    document.getElementById("3col_arrow").classList.add("medals__arrow-nomedal");
    document.getElementById("osekai__col__right").classList.add("hidden");
}

function leaveLandingPage() {
    document.getElementById("expandbutton").classList.remove("hidden");

    if (document.getElementById("osekai__col1").classList.contains("medals__nomedal")) {
        document.getElementById("osekai__col1").classList.remove("medals__nomedal");
        document.getElementById("3col_arrow").classList.remove("medals__arrow-nomedal");
        document.getElementById("osekai__col__right").classList.remove("hidden");
    }
}

function changeState(strName) {
    let params = new URLSearchParams(window.location.search);
    if (params.get("medal") == strName) return;
    params.set("medal", encodeURIComponent(strName));
    window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    loadMedal(strName);
}
var beatmapPacksLoaded = false;
async function loadMedal(strMedalName, updateAdminPanel = true) {
    document.getElementById("video_panel").classList.add("hidden");

    if (window.mobile == true &&
        (document.getElementsByClassName("osekai__3col_col1")[0].classList.contains("osekai__3col_col1_hide") == false || landingPage == true)) {
        switch3col();
        console.log("trying to hide the 3col");
    } else {

    }


    leaveLandingPage();
    //if(window.mobile) switch3col();

    strCurrentMedalName = colMedals[strMedalName].Name;
    strCurrentMedalMode = colMedals[strMedalName].Restriction;
    nCurrentMedalID = colMedals[strMedalName].MedalID;

    {
        if (FavMedals != null && nUserID != -1)
            if (FavMedals.includes(parseInt(nCurrentMedalID))) {
                document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Unfavourite'; // TODO: translate
                document.getElementById('favButton').classList.add('osekai__button-on');
            } else {
                document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Favourite'; // TODO: translate
                document.getElementById('favButton').classList.remove('osekai__button-on');
            }

        // i want this shit isolated
        if (FavMedals == null && nUserID != -1) {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', "/medals/api/favourite.php")
            xhr.onload = () => {
                let oResponse = getResponse(xhr);
                console.log(`Fav medals: ${oResponse}`);
                FavMedals = oResponse;
                if (FavMedals.includes(parseInt(nCurrentMedalID))) {
                    document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Unfavourite'; // TODO: translate
                    document.getElementById('favButton').classList.add('osekai__button-on');
                } else {
                    document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Favourite'; // TODO: translate
                    document.getElementById('favButton').classList.remove('osekai__button-on');
                }
            }
            xhr.send();
        }
    }
    if (document.getElementById("edit_button")) {
        document.getElementById("edit_button").href = "/admin/panel/apps/medals?id=" + nCurrentMedalID;
    }
    document.getElementById("strMedalTitle").innerHTML = "Loading...";
    document.getElementById("strMedalDesc").innerHTML = "Loading...";
    document.getElementById("strMedalHint").innerHTML = "Loading...";
    document.getElementById("strMedalSolution").innerHTML = "Loading...";
    document.getElementById("strMedalTitle").innerHTML = strCurrentMedalName;
    document.getElementById("strMedalDesc").innerHTML = colMedals[strMedalName].Description;
    document.getElementById("strMedalHint").innerHTML = colMedals[strMedalName].Instructions;
    try {
        document.getElementById("oMedalIcon").src = colMedals[strMedalName].Link;
    } catch (e) {

    }
    // get medal icon by selector
    for (let i = 0; i < document.querySelectorAll("[selector='oMedalIcon']").length; i++) {
        document.querySelectorAll("[selector='oMedalIcon']")[i].src = colMedals[strMedalName].Link;
    }
    document.getElementById("strMedalSolution").innerHTML = colMedals[strMedalName].Solution;
    document.getElementById("strMedalSolution").innerHTML = document.getElementById("strMedalSolution").innerHTML.replace(/\n/g, "<br />")
    var rarity = parseFloat(colMedals[strMedalName].Rarity);
    //console.log(rarity);
    document.getElementById("strMedalRarity").innerHTML = "<light>rarity</light> " + Math.round(rarity * 100) / 100 + "%";
    document.getElementById("strMedalRarity").innerHTML = await GetStringRaw("medals", "solution.rarity", [Math.round(rarity * 100) / 100 + "%"])
    //console.log(colMedals[strMedalName]);
    document.getElementById("strMedalGroup").innerHTML = colMedals[strMedalName].Grouping;
    if (updateAdminPanel) {
        if (document.getElementById("solution__editor")) document.getElementById("solution__editor").value = colMedals[strMedalName].Solution;
        if (document.getElementById("solution__mods")) document.getElementById("solution__mods").value = colMedals[strMedalName].Mods;
        if (document.getElementById("solution__packid")) document.getElementById("solution__packid").value = colMedals[strMedalName].PackID;
        if (document.getElementById("solution__video")) document.getElementById("solution__video").value = colMedals[strMedalName].Video;

        if (colMedals[strMedalName].FirstAchievedDate != "0000-00-00" && colMedals[strMedalName].FirstAchievedDate != null) {
            if (document.getElementById("solution__dateachieved")) document.getElementById("solution__dateachieved").value = new Date(colMedals[strMedalName].FirstAchievedDate).toISOString().substr(0, 10);
        } else {
            if (document.getElementById("solution__dateachieved")) document.getElementById("solution__dateachieved").value = null;
        }
        if (document.getElementById("solution__achievedid")) document.getElementById("solution__achievedid").value = colMedals[strMedalName].FirstAchievedBy;
        if (colMedals[strMedalName].Date != "0000-00-00" && colMedals[strMedalName].Date != null) {
            if (document.getElementById("solution__date")) document.getElementById("solution__date").value = new Date(colMedals[strMedalName].Date).toISOString().substr(0, 10);
        } else {
            if (document.getElementById("solution__date")) document.getElementById("solution__date").value = null;
        }
    }
    // <mulraf> / Get Gamemode Icon
    //if (document.getElementById("gamemodeImg")) document.getElementById("gamemodeImg").remove();
    let oImg = document.getElementById("gamemodeImg")
    let strImgMode = strCurrentMedalMode.replace("osu", "standard");
    if (strImgMode != "NULL") {
        document.getElementsByClassName("medals__sol-header-gamemode-tag")[0].classList.remove("hidden");
        oImg.src = '/global/img/gamemodes/' + strImgMode + '.svg';
        oImg.classList.add("medals__sol-gamemode");
        oImg.id = "gamemodeImg";
        oImg.alt = strImgMode;
    } else {
        document.getElementsByClassName("medals__sol-header-gamemode-tag")[0].classList.add("hidden");
    }

    if (strImgMode != "NULL") {
        document.getElementById("gamemodeText").innerHTML = "<b>" + strImgMode + "</b> only";
    } else {
        document.getElementById("gamemodeText").innerHTML = "";
    }

    // </mulraf> / End of getting Gamemode Icon

    // <Hubz> / Meta Generation
    var _desc = "Osekai Medals • " + colMedals[strMedalName].Description;
    var _title = "Osekai Medals • The solution to the osu! medal " + strMedalName + "!";

    document.querySelector('meta[name="description"]').setAttribute("content", _desc);
    document.querySelector('meta[name="twitter:description"]').setAttribute("content", _desc);
    document.querySelector('meta[property="og:description"]').setAttribute("content", _desc);

    document.querySelector('title[name="title"]').innerHTML = _title;
    document.querySelector('meta[name="twitter:title"]').setAttribute("content", _title);
    document.querySelector('meta[property="og:title"]').setAttribute("content", _title);
    // </Hubz> / End of Meta Generation

    // <mulraf> / remove old activities
    let colActive = document.getElementsByClassName("medals__grid-medal__active")
    Array.prototype.forEach.call(colActive, (oHighlighted) => {
        oHighlighted.classList.remove("medals__grid-medal__active");
    });
    if (document.getElementById("medal_" + colMedals[strMedalName].MedalID)) {
        document.getElementById("medal_" + colMedals[strMedalName].MedalID).classList.add("medals__grid-medal__active");
    }
    /* document.querySelectorAll(".osekai__replace__loader").forEach(oLoader => oLoader.remove()); */
    // </mulraf> / End of removal

    getMods(colMedals[strMedalName].Mods);

    for (var classname in medals_grouping_classnames) {
        document.getElementsByClassName("medals__solution-panel-outer")[0].classList.remove(medals_grouping_classnames[classname]);
    }
    document.getElementsByClassName("medals__solution-panel-outer")[0].classList.add(medals_grouping_classnames[colMedals[strMedalName].Grouping])

    if (colMedals[strMedalName].PackID != null && colMedals[strMedalName].PackID != 0) {
        document.getElementById("oBeatmapContainer").classList.add("hidden");
        //document.getElementById("oBeatmapContainer_GetFromOsu_Button").href = "https://osu.ppy.sh/beatmaps/packs/" + colMedals[strMedalName].PackID;
        document.getElementById("oBeatmapContainer_GetFromOsu").classList.remove("hidden");

        // split by ,
        var individual = colMedals[strMedalName].PackID.split(",");
        var gamemodes = ["standard", "taiko", "catch", "mania"];
        //(individual);
        var html = ``;
        document.getElementById("oBeatmapContainer_GetFromOsu").innerHTML = loader;
        let xhr = new XMLHttpRequest();
        //console.log("/medals/api/get_beatmap_pack_count.php?id=" + colMedals[strMedalName].PackID);
        xhr.open('GET', "/medals/api/get_beatmap_pack_count.php?id=" + colMedals[strMedalName].PackID)
        xhr.onload = function () {

            resp = xhr.response;
            //console.log(xhr.response);
            resp = JSON.parse(resp);
            console.log(resp);
            for (var i = 0; i < individual.length; i++) {
                if (individual[i] == 0) continue;
                best = true;
                var calcInvalid = false;

                var length = 0;
                for (var beatmap of resp[i]) {
                    length += beatmap.Length;
                }

                if (length != 0) {
                    for (var j = 0; j < individual.length; j++) {
                        // note: packs with no pack for specific gamemode will still
                        // return that gamemode array, just it'll be empty, so we have
                        // to check, Just In Case (it fixes this entire routine on
                        // every beatmap with 1 or more missing gamemodes)
                        if (resp[j].length != 0) {
                            let test_length = 0;
                            for (var beatmap of resp[j]) {
                                test_length += beatmap.Length;
                            }
                            console.log(test_length + "TIME");
                            if (test_length != 0) {
                                if (test_length < length) {
                                    best = false;
                                }
                            } else {
                                console.log("CALC INVALID");
                                calcInvalid = true;
                            }
                        }
                    }
                } else {
                    calcInvalid = true;
                }

                if (calcInvalid == true) {
                    console.log("doing count check instead of length...");
                    best = true;
                    for (var j = 0; j < individual.length; j++) {
                        if (resp[j].length != 0) {
                            if (resp[j].length < resp[i].length) {
                                best = false;
                            }
                        }
                    }
                }

                var gamemode = gamemodes[i];
                var extraClasses = "";

                if (best) { extraClasses += "medals__viewpack-best" };

                html += `<a class="medals__viewpack ` + extraClasses + `" href="https://osu.ppy.sh/beatmaps/packs/${individual[i]}" style="--maincol: var(--${gamemode})" target="_blank">
                <i class="oif-gamemode-${gamemode}"></i>
                <div class="medals__viewpack-textarea-left">
                    <div class="medals__viewpack-top">` + GetStringRawNonAsync("medals", "beatmap.viewOnOsu") + `</div>
                    <div class="medals__viewpack-bottom">${GetStringRawNonAsync("medals", "beatmapPacks.mapCount", [resp[i].length])}</div>
                </div>
                `;

                if (length != 0) {
                    html += `<div class="medals__viewpack-textarea-right">
                    <div class="medals__viewpack-top">${fancyTimeFormat(length)}</div>
                    <div class="medals__viewpack-bottom">${GetStringRawNonAsync("medals", "beatmapPacks.withDT.inline", [fancyTimeFormat(length / 1.5)])}</div>
                </div>`;
                } else {
                    html += `<div class="medals__viewpack-textarea-right">
                    <div class="medals__viewpack-bottom">Pack length not<br>yet calculated.</div>
                </div>`
                }

                html += `
                <i class="fas fa-clock"></i>
            </a>`;
            }
            document.getElementById("oBeatmapContainer_GetFromOsu").innerHTML = html;
        }
        xhr.send();

        if (beatmapPacksLoaded == true) {
            for (var el of document.getElementsByClassName("medals__beatmapPack")) {
                el.classList.remove("medals__beatmapPack-active");
            }
            document.querySelector("[m-bmp-medal-name=\"" + strMedalName + "\"]").classList.add("medals__beatmapPack-active");
        }
    }
    else {
        document.getElementById("oBeatmapContainer").classList.remove("hidden");
        document.getElementById("oBeatmapContainer_GetFromOsu").classList.add("hidden");
        requestBeatmaps("strSearch", colMedals[strMedalName].Name, "/medals/api/beatmaps.php");
    }
    if (colMedals[strMedalName].Video != null && colMedals[strMedalName].Video != "") {
        document.getElementById("video_panel").classList.remove("hidden");
        // though youtube-nocookie.com seems suspicious
        // it is in fact an official youtube domain.
        // see: https://support.google.com/youtube/answer/171780?hl=en-GB#zippy=%2Cturn-on-privacy-enhanced-mode

        // update: it broke

        // update: it's fixed
        document.getElementById("video").src = "https://www.youtube-nocookie.com/embed/" + colMedals[strMedalName].Video;
    } else {
        document.getElementById("video_panel").classList.add("hidden");
        document.getElementById("video").src = "";
    }
    loadExtraInfo(nCurrentMedalID);
    addLock();
    checkLock(allowAddMap);
    checkLock(changeLockIcon);
    Comments_Require(colMedals[strMedalName].MedalID, document.getElementById("comments__box"), true);
    window.scrollTo({
        top: 0,
    })

    set_breadcrums("{app}/" + strMedalName);
}

function requestBeatmaps(strKey, strValue, strUrl) {
    var xhr = createXHR(strUrl);
    xhr.send(strKey + "=" + strValue);
    var bmcount = 0;
    if (document.getElementById("beatmap_count")) {
        document.getElementById("beatmap_count").innerHTML = bmcount;
    }
    document.getElementById("oBeatmapContainer").innerHTML = loader
    /* document.getElementById("oBeatmapContainer").classList.add("hidden"); */

    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        document.getElementById("oBeatmapContainer").innerHTML = ""
        Object.keys(oResponse).forEach(function (obj) {
            loadBeatmap(oResponse[obj]);
            bmcount += 1;
        });
        if (document.getElementById("beatmap_count")) {
            document.getElementById("beatmap_count").innerHTML = bmcount;
        }
        document.getElementById("oBeatmapContainer").classList.remove("hidden");
        /* document.querySelectorAll(".osekai__replace__loader").forEach(oLoader => oLoader.remove()); */
    };
}

function reportBeatmap(beatmapId) {

    doReport("beatmap", beatmapId);
}

var colNotes = {};
function loadBeatmap(oBeatmap) {
    let CoverID = "https://assets.ppy.sh/beatmaps/" + oBeatmap.MapsetID + "/covers/cover.jpg";
    let MapUrl = "https://osu.ppy.sh/beatmapsets/" + oBeatmap.MapsetID + "#" + oBeatmap.Gamemode + "/" + oBeatmap.BeatmapID;
    let bCanDelete = false;
    if (typeof nUserID !== 'undefined' && (nUserID.toString() == oBeatmap.SubmittedBy || nRights > 0)) bCanDelete = true;
    colNotes[oBeatmap.BeatmapID] = oBeatmap.Note;



    beatmap_panel = `<div id="desktop" class="medals__bmp3-panel-outer">
        <div class="medals__bmp3-panel"style="background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%), url(` + CoverID + `), linear-gradient(#240d19,#240d19);">
            <a class="medals__bmp3-top" href="` + MapUrl + `" target="_blank">
                ` + `<div class="medals__bmp3-top-left">` +
        `<p class="medals__bmp3-tl-bmname">` + escapeHtml(oBeatmap.SongTitle) + `</p>` +
        `<p class="medals__bmp3-tl-artist translatable">??medals.beatmap.by?? <span class="medals__bmp3-bold">` + escapeHtml(oBeatmap.Artist) + `</span></p>` +
        `</div>` +
        `<div class="medals__bmp3-top-right">` +
        `<p class="medals__bmp3-tr-difficulty">` + escapeHtml(oBeatmap.DifficultyName) + `</p>` +
        `<p class="medals__bmp3-tr-mapper translatable">??medals.beatmap.mappedBy?? <span class="medals__bmp3-bold">` + escapeHtml(oBeatmap.Mapper) + `</span></p>` +
        `</div>` + `</a>
            <div class="medals__bmp3-bottom">
                <p class="medals__bmp2-submitter translatable">
                    ??general.date_submitted?? <span class="medals__bmp3-bold tooltip-v2" tooltip-content="` + new Date(oBeatmap.SubmissionDate).toDateString() + `">` + TimeAgo.inWords(new Date(oBeatmap.SubmissionDate).getTime()) + `</span>` +
        `</p>
                <div class="medals__bmp3-right" id="subcontainer_` + oBeatmap.BeatmapID + `">` +
        (((oBeatmap.Note !== null && oBeatmap.Note.toString().replace(" ", "") !== "") || bCanDelete) ?
            `<div class="medals__bmp3-r-note" onclick="notePanel(` + oBeatmap.BeatmapID + `, ` + bCanDelete + `);"> ` + (oBeatmap.Note == null || oBeatmap.Note.toString().replace(" ", "") == "" ? GetStringRawNonAsync("medals", "beatmaps.note.add") : GetStringRawNonAsync("medals", "beatmaps.note.view")) + `<i class="fas fa-sticky-note medals__bmp3-r-note-icon"></i></div>`
            :
            ``) +
        `<div id="` + oBeatmap.ObjectID + `"` + (bLoggedIn ? ` onclick="vote(` + oBeatmap.ObjectID + `, this);"` : ``) + ` class="medals__bmp3-r-vote` + (oBeatmap.HasVoted ? ` medals__bmp3-r-vote-voted` : ` `) + `">+` + oBeatmap.VoteSum + `</div>
                </div>
            </div>
        </div>
        <div class="medals__bmp3-hover-area">
            <a href="osu://b/` + oBeatmap.BeatmapID + `" class="medals__bmp3-hover-button translatable">
            ??medals.beatmap.downloadWithDirect??
            </a>` +
        (nUserID.toString() != oBeatmap.SubmittedBy ?
            `<div class="medals__bmp3-hover-button translatable" onclick="reportBeatmap(` + oBeatmap.BeatmapID + `);">
            ??medals.beatmap.report??
            </div>` : ``) +
        (bCanDelete ?
            `<div class="medals__bmp3-hover-button translatable" onclick="deleteMap(` + oBeatmap.BeatmapID + `);">` +
            `??medals.beatmap.delete??` +
            `</div>`
            : ``) +
        `</div>
    </div>`





    beatmap_panel += `<div id="mobile" class="medals__bmp3_mobile" style="background: linear-gradient(146deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%), url(` + CoverID + `);">
        <div class="medals__bmp3_mobile-inner">
            <a class="medals__bmp3_mobile-title" href="` + MapUrl + `">` + escapeHtml(oBeatmap.SongTitle) + `</a>
            <p class="medals__bmp3_mobile-artist">by <strong>` + escapeHtml(oBeatmap.Artist) + `</strong></p>
            <p class="medals__bmp3_mobile-difficulty">` + escapeHtml(oBeatmap.DifficultyName) + `</p>
            <p class="medals__bmp3_mobile-mapper">mapped by <strong>` + escapeHtml(oBeatmap.Mapper) + `</strong></p>
            <p class="medals__bmp3_mobile-submission-date">submitted <strong>` + TimeAgo.inWords(new Date(oBeatmap.SubmissionDate).getTime()) + `</strong></p>
        </div>
        <div class="medals__bmp3_mobile-buttons">` +
        `<div id="` + oBeatmap.ObjectID + `"` + (bLoggedIn ? ` onclick="vote(` + oBeatmap.ObjectID + `, this);"` : ``) + ` class="medals__bmp3_mobile-vote` + (oBeatmap.HasVoted ? ` medals__bmp3-r-vote-voted` : ` `) + `">+` + oBeatmap.VoteSum + `</div>            
        <!--<div class="medals__bmp3_mobile-vote">+9</div>-->` +
        (nUserID.toString() != oBeatmap.SubmittedBy ?
            `<div class="medals__bmp3_mobile-report" onclick="reportBeatmap(` + oBeatmap.BeatmapID + `, ` + `'` + escapeHtml(oBeatmap.SongTitle) + `'` + `);"><i class="fas fa-exclamation-triangle"></i></div>
        </div>` : ``) +
        `</div>`;



    if (oBeatmap.MedalName == strCurrentMedalName && !document.body.contains(document.getElementById("subcontainer_" + oBeatmap.BeatmapID))) document.getElementById("oBeatmapContainer").innerHTML += beatmap_panel;
}

function notePanel(strBeatmapID, bCanChange) {
    document.getElementById("oBeatmapInput").innerHTML = '<div class="osekai__overlay"> ' +
        '<section class="osekai__panel osekai__overlay__panel"> ' +
        '<div class="osekai__panel-header"> ' +
        '<p>Note</p> ' +
        '</div> ' +
        '<div class="osekai__panel-inner"> ' +
        (bCanChange ?
            '<input id="txtNote" class="medals__bmid osekai__input osekai__fullwidth" type="text" value="' + (colNotes[strBeatmapID] ?? "") + '">'
            : '<p id="txtNote" class="medals__bmid osekai__fullwidth">' + (strip(colNotes[strBeatmapID]) ?? "") + '</p>') +
        '<div class="osekai__flex_row"> ' +
        '<a class="osekai__button" onclick="closeBeatmapPanel();">Cancel</a> ' +
        (bCanChange ?
            '<a class="osekai__button osekai__left" onclick="changeNote(' + strBeatmapID + ');">Update</a> ' : '') +
        '</div> ' +
        '</div> ' +
        '</section> ' +
        '</div>';
}

function changeNote(strBeatmapID) {
    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("strNoteChange=" + document.getElementById("txtNote").value + "&strMapID=" + strBeatmapID + "&strMedalName=" + strCurrentMedalName);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            closeBeatmapPanel();
            requestBeatmaps("strSearch", strCurrentMedalName, "/medals/api/beatmaps.php");
        }
    };
}

function deleteMap(strID) {
    var bContinue = confirm("Do you really want to delete map " + strID + " for Medal " + strCurrentMedalName + "?");
    if (!bContinue) return;

    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("strDeletion=" + strID + "&strMedalName=" + strCurrentMedalName);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            requestBeatmaps("strSearch", strCurrentMedalName, "/medals/api/beatmaps.php");
        }
    };
}

function getMods(strMods) {
    document.getElementById("colMods").innerHTML = "";
    if (!strMods) return;
    for (let i = 0; i < colLinks.length; i++) {
        if (strMods.includes(colMods[i])) document.getElementById("colMods").innerHTML += '<img alt="' + colRealNames[i] + '" class="osekai__mi-mod tooltip-v2" tooltip-content="' + colRealNames[i] + '" src="https://osu.ppy.sh/images/badges/mods/mod_' + colLinks[i] + '@2x.png">';
    }
}

function vote(nBeatmapID, element) {
    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("nObject=" + nBeatmapID);

    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        Object.keys(oResponse).forEach(function (obj) {
            if (oResponse[obj].HasVoted == 1) {
                element.innerHTML = "+" + (parseInt(element.innerHTML) - 1);
                element.classList.remove("medals__bmp3-r-vote-voted");
            } else {
                element.innerHTML = "+" + (parseInt(element.innerHTML) + 1);
                element.classList.add("medals__bmp3-r-vote-voted");
            }
            document.getElementById(nBeatmapID).classList.toggle("medals__bmp2-r-vote-voted");
        });
    };
}

function openBeatmapPanel() {
    document.getElementById("oBeatmapInput").innerHTML = '<div class="osekai__overlay"> ' +
        '<section class="osekai__panel osekai__overlay__panel"> ' +
        '<div class="osekai__panel-header"> ' +
        '<p>' + GetStringRawNonAsync("medals", "beatmaps.add.title") + '</p> ' +
        '</div> ' +
        '<div class="osekai__panel-inner"> ' +
        '<p class="medals__addbeatmap-ifo1"> ' +
        GetStringRawNonAsync("medals", "beatmaps.add.body", [strCurrentMedalName]) +
        '</p> ' +
        '<p class="medals__addbeatmap-ifo2"> ' +
        GetStringRawNonAsync("medals", "beatmaps.add.p1") +
        '</p> ' +
        '<input id="txtBeatmap" class="medals__bmid osekai__input osekai__fullwidth" type="text" placeholder="' + GetStringRawNonAsync("medals", "beatmaps.add.url.placeholder") + '"> ' +
        '<input id="txtNote" class="medals__bmid osekai__input osekai__fullwidth" type="text" placeholder="' + GetStringRawNonAsync("medals", "beatmaps.add.note.placeholder") + '">' +
        '<div class="osekai__flex_row"> ' +
        '<a class="osekai__button" onclick="closeBeatmapPanel();">' + GetStringRawNonAsync("general", "cancel") + '</a> ' +
        '<p style="margin-left: 10px; align-self: center; color: red;" id="strFeedbackNegative"></p> ' +
        '<a class="osekai__button osekai__left" onclick="addBeatmap();">' + GetStringRawNonAsync("general", "add") + '</a> ' +
        '</div> ' +
        '</div> ' +
        '</section> ' + // LINE 727 WYSI OMG WHEN YOU SEE IT ITS THE FUNNY NUMBER NO FUCKING WAY
        '</div>';
}

function closeBeatmapPanel() {
    document.getElementById("oBeatmapInput").innerHTML = "";
}

function addBeatmap() {
    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("strBeatmap=" + document.getElementById("txtBeatmap").value + "&strMedalName=" + strCurrentMedalName + "&strMedalMode=" + strCurrentMedalMode + "&strNote=" + document.getElementById("txtNote").value);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            closeBeatmapPanel();
            requestBeatmaps("strSearch", strCurrentMedalName, "/medals/api/beatmaps.php");
        } else {
            document.getElementById("strFeedbackNegative").innerHTML = oResponse.toString();
        }
    };
}

function addLock() {
    if (document.contains(document.getElementById("lock"))) document.getElementById("lock").remove();
    if (nRights > 0) {
        let oElem = document.createElement("div");
        oElem.id = "lock";
        oElem.classList.add("osekai__mi-lock");
        oElem.innerHTML = '<span class="medals-ico-lock" id="lock-icon"></span>';
        oElem.addEventListener('click', switchLock);
        document.getElementById("medal__info").appendChild(oElem);
    }
}

function switchLock() {
    checkLock(changeLockState);
}

function changeLockIcon(bIsLocked) {
    let oLock = document.getElementById("lock-icon");
    if (document.body.contains(oLock)) {
        if (oLock.classList.contains("medals-ico-lock-open") && bIsLocked) oLock.classList.remove("medals-ico-lock-open");
        if (!oLock.classList.contains("medals-ico-lock-open") && !bIsLocked) oLock.classList.add("medals-ico-lock-open");
    }
}

function changeLockState(bCurrentlyLocked) {
    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("bCurrentlyLocked=" + bCurrentlyLocked + "&nMedalID=" + nCurrentMedalID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "true") checkLock(changeLockIcon);
    };
}

function checkLock(callback) {
    var xhr = createXHR("/medals/api/beatmaps.php");
    xhr.send("bCheckLock=true&nMedalID=" + nCurrentMedalID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse == 1)
            callback(true);
        else
            callback(false);
    };
}

function allowAddMap(bLocked) {
    //osekai__input-disabled
    if (document.contains(document.getElementById("AddMapButton"))) document.getElementById("AddMapButton").remove();
    let oElem = document.createElement("div");
    oElem.classList.add("osekai__panel-hwb-right");
    oElem.id = "AddMapButton";
    oElem.innerHTML =
        '<div onclick="openBeatmapPanel();" class="osekai__panel-header-button">' +
        '<i class="fas fa-plus-circle osekai__panel-header-button-icon"></i>' +
        '<p class="osekai__panel-header-button-text">' + GetStringRawNonAsync("medals", "beatmaps.add") + '</p>' +
        '</div>';
    if (!bLocked && typeof nUserID !== 'undefined' && nUserID.toString() !== "-1") {
        oElem.classList.remove("osekai__input-disabled");
    } else {
        oElem.classList.add("osekai__input-disabled");
    }
    document.getElementById("AddMapPanel").appendChild(oElem);
}

function updateSolution() {
    let strText = encodeURIComponent(document.getElementById("solution__editor").value);
    let strMods = document.getElementById("solution__mods").value;
    let strPackID = document.getElementById("solution__packid").value;
    let strVideo = document.getElementById("solution__video").value;
    let strDate = document.getElementById("solution__date").value;
    let strAchievedDate = document.getElementById("solution__dateachieved").value;
    let strAchievedId = document.getElementById("solution__achievedid").value;
    var xhr = createXHR("/medals/api/medals.php");
    xhr.send("strNewSolution=" + strText + "&nSolutionMedal=" + nCurrentMedalID + "&strSolutionMods=" + strMods + "&strSolutionPackID=" + strPackID + "&strSolutionVideo=" + strVideo + "&strSolutionDate=" + strDate + "&strFirstAchievedDate=" + strAchievedDate + "&strFirstAchievedId=" + strAchievedId);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            loadMedal(strCurrentMedalName, false);
        }
    }
}



function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };

    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

function generateExtraInfoBar(icon, text, data, image, link) {
    var datatext = "Unknown";
    if (data != null) datatext = data;
    var datetext = new Date(datatext).toDateString();
    if (datetext != "Invalid Date") datatext = datetext; // dumb way to cehck if it's a valid date but it works
    var right = "";
    //if(image exists do <a> and image right bar else dont)
    if (image != null) {
        right += `<a target="_blank" class="medals__extrainfo-bar-right medals__extrainfo-bar-right-with-image" href="${link}">
            <p>${datatext}</p><img src="${image}">
        </a>`;
    } else {
        right += `<div class="medals__extrainfo-bar-right">
            <p>${datatext}</p>
        </div>`
    }
    var html = `<div class="medals__extrainfo-bar">
    <div class="medals__extrainfo-bar-left">
        <i class="fas ${icon}"></i>
        <p>${text}</p>
    </div>
    ${right}

    </div>`;
    return html;
}

function loadExtraInfo(medalid) {
    var container = document.getElementById("oExtraInfoContainer");
    container.innerHTML = loader;
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "/medals/api/extrainfo.php?id=" + medalid)
    xhr.onload = function () {
        var resp = JSON.parse(xhr.response);
        container.innerHTML = "";
        console.log(resp);
        var any = false;
        if (resp.date != null && resp.date != "0000-00-00") {
            any = true;
            container.innerHTML += generateExtraInfoBar("fa-calendar-day", GetStringRawNonAsync("medals", "extraInfo.dateReleased"), resp.date);
        }
        if (resp.firstachieveddate != null && resp.firstachieveddate != "0000-00-00") {
            any = true;
            container.innerHTML += generateExtraInfoBar("fa-calendar-check", GetStringRawNonAsync("medals", "extraInfo.dateAchieved"), resp.firstachieveddate);
        }
        if (resp.firstachievedby != null && resp.firstachievedby.id != 0) {
            any = true;
            container.innerHTML += generateExtraInfoBar("fa-user", GetStringRawNonAsync("medals", "extraInfo.achievedBy"), resp.firstachievedby['username'], "https://a.ppy.sh/" + resp.firstachievedby['id'], "/profiles/?user=" + resp.firstachievedby['id'])
        }
        if (any == false) {
            container.innerHTML = GetStringRawNonAsync("medals", "extraInfo.none");
        }
    }
    xhr.send();
}


function changeMedalFavState() {
    const shouldDelete = FavMedals.find((e) => e == parseInt(nCurrentMedalID));

    if (shouldDelete)
        FavMedals.splice(FavMedals.indexOf(parseInt(nCurrentMedalID)), 1);
    else
        FavMedals.push(parseInt(nCurrentMedalID));
    requestMedals(false, document.getElementById("txtMedalSearch").value, FavMedals);

    const xhr = new XMLHttpRequest();
    xhr.open(shouldDelete ? 'DELETE' : 'PUT', '/medals/api/favourite.php', true);
    xhr.send(JSON.stringify({ medal_id: parseInt(nCurrentMedalID) }));

    if (FavMedals.includes(parseInt(nCurrentMedalID))) {
        document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Unfavourite'; // TODO: translate
        document.getElementById('favButton').classList.add('osekai__button-on');
    } else {
        document.getElementById('favButton').innerHTML = '<i class="fas fa-star"></i>Favourite'; // TODO: translate
        document.getElementById('favButton').classList.remove('osekai__button-on');
    }
}

function loadFavMedals() {
    if (userInfo == null) {
        console.log("waiting for userinfo")
        setTimeout(loadBeatmapPacks, 100);
        return;
    }
    console.log("loading favs medals...");

    var container = document.getElementById("favsSection");
    container.innerHTML = loader;
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "/medals/api/favourite.php");
    xhr.onload = function () {
        var resp = JSON.parse(xhr.response);
        container.innerHTML = "";
        requestMedals(false, document.getElementById("txtMedalSearch").value, resp);
    }
    xhr.send();
}

function loadBeatmapPacks() {
    if (userInfo == null && bLoggedIn) {
        console.log("waiting for userinfo")
        setTimeout(loadBeatmapPacks, 100);
        return;
    }
    console.log("loading beatmap packs...");

    var container = document.getElementById("beatmapPackList");
    container.innerHTML = loader;
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "/medals/api/beatmap_packs.php")
    xhr.onload = function () {
        var resp = JSON.parse(xhr.response);
        container.innerHTML = "";


        for (let medal of resp) {
            let medalContainer = Object.assign(document.createElement("div"), { className: "medals__beatmapPack" });
            medalContainer.setAttribute("m-bmp-medal-name", medal.name);
            var medalContainerLeft = Object.assign(document.createElement("div"), { className: "medals__beatmapPack-left" });
            var medalContainerRight = Object.assign(document.createElement("div"), { className: "medals__beatmapPack-right" });

            var medalImage = Object.assign(document.createElement("img"), { src: medal.link });
            var medalName = Object.assign(document.createElement("p"), { innerText: medal.name });

            var packLength = Object.assign(document.createElement("p"), { innerHTML: "<i class=\"oif-gamemode-" + medal.fastest_gamemode + "\"></i> " + fancyTimeFormat(medal.fastest_time / 1.5) });
            var packLengthSmall = Object.assign(document.createElement("small"), { innerText: GetStringRawNonAsync("medals", "beatmapPacks.withDT") });

            medalContainerLeft.appendChild(medalImage);

            if (bLoggedIn) {
                for (var usermedal of userInfo['user_achievements']) {
                    if (usermedal.achievement_id == medal.medalid) {
                        console.log("achieved");
                        medalContainer.classList.add("medals__beatmapPack-obtained");
                        var checkmark = Object.assign(document.createElement("i"), { className: "fas fa-check" });
                        medalContainerLeft.appendChild(checkmark);
                    }
                }
            }

            medalContainerLeft.appendChild(medalName);

            medalContainerRight.appendChild(packLength);

            if (medal.fastest_time == -1) {
                packLength.innerHTML = "unknown"
            } else {
                medalContainerRight.appendChild(packLengthSmall);
            }


            medalContainer.appendChild(medalContainerLeft);
            medalContainer.appendChild(medalContainerRight);

            container.appendChild(medalContainer);

            medalContainer.addEventListener("click", function () {
                changeState(medal.name);
            })
        }

        beatmapPacksLoaded = true;
    }
    xhr.send();
}


var particles = [];
function runParticleAnim() {
    particles = [];
    var count = 0;
    function addParticles() {
        if (count > 200) {
            return;
        }
        count++;
        particles.push(particle.create(width / 2, height / 2, (Math.random() * 10) + 1, Math.random() * 10 * Math.PI * 20))
        setTimeout(() => {

            addParticles();
        }, 0.2);
    }
    addParticles();
    canvasupdate();
}
function resetParticleAnim() {
    particles = [];
}

canvas = document.getElementById("particle_canvas");
context = canvas.getContext("2d");
width = canvas.width = window.innerWidth;
height = canvas.height = window.innerHeight;

function canvasupdate() {
    if (particles == []) return;
    context.clearRect(0, 0, width, height);
    context.fillStyle = "#ffffff";
    for (var i = 0; i < particles.length; i++) {
        particles[i].update();
        context.beginPath();
        context.arc(particles[i].position.getX(), particles[i].position.getY(), 3, 0, 2 * Math.PI, false);
        context.fill();
    }

    requestAnimationFrame(canvasupdate);
}

var original = "";
function randomMedal() {
    document.getElementById("randommedal").classList.remove("medals__randommedal-finished");
    document.getElementById("randommedal").offsetHeight;
    document.getElementById("randommedal_img_blur").offsetHeight;
    document.getElementById("randommedal_img").offsetHeight;
    document.getElementById("randommedal_img_glow").offsetHeight;
    document.getElementById("randommedal_img_blur").src = "";

    document.getElementById("randommedal").classList.remove("medals__randommedal-hidden");
    function selectRandom() {
        var keys = Object.keys(colMedals);
        return colMedals[keys[keys.length * Math.random() << 0]];
    }

    function run(time, count) {
        console.log(selectRandom());
        var medal = selectRandom();
        document.getElementById("randommedal_img").src = medal.Link;
        document.getElementById("randommedal_img_glow").src = medal.Link;
        if (count == 50) {
            loadMedal(medal.Name);
            document.getElementById("randommedal_img_blur").src = medal.Link;
            document.getElementById("randommedal").classList.add("medals__randommedal-finished");
            console.log("finished with count " + count + " and time " + time + "!")
            runParticleAnim();
            setTimeout(() => {
                document.getElementById("randommedal").classList.add("medals__randommedal-hidden");
            }, 3000);
            setTimeout(() => {
                resetParticleAnim();
            }, 4000);
            return;
        }
        console.log("continuing with time " + time + " and count " + count);
        setTimeout(() => {
            run(time + 6 * (count / 45), count + 1);
        }, time);
    }
    run(4, 0);
}

