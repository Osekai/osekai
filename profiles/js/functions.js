var colRankChart;
var colRankMedals = [];
var colGroups = [];
var strGoalType = "Medals";
var bTimelineHover = false;
var dJoined;
var oTimeout;

window.addEventListener('popstate', function (event) {
    Initialize();
});

document.addEventListener("DOMContentLoaded", function () {
    Initialize();
});

function Initialize() {
    addEvents();
    if (new URLSearchParams(window.location.search).get("user") == null) {
        loadHome();
        return;
    }
    loadMode(new URLSearchParams(window.location.search).get("mode"));
    document.getElementById("profile").classList.remove("hidden");
}

function addEvents() {
    document.getElementById("mode__list").childNodes.forEach(oNode => {
        oNode.addEventListener("click", function () {
            let url = new URL(window.location);
            url.searchParams.set('mode', this.getAttribute("mode"));
            window.history.pushState({}, '', url);
            loadMode(this.getAttribute("mode"), false);
        })
    });

    document.getElementById("mode__list__home").childNodes.forEach(oNode => {
        oNode.addEventListener("click", function () {
            let url = new URL(window.location);
            url.searchParams.set('mode', this.getAttribute("mode"));
            window.history.pushState({}, '', url);
            loadHomeData();
        })
    });

    document.getElementById("timeline__info").addEventListener("mouseover", () => {
        bTimelineHover = true;
    })

    document.getElementById("timeline__info").addEventListener("mouseout", () => {
        bTimelineHover = false;
    })

    document.getElementById("goals__dropdown__button").addEventListener("click", () => {
        document.getElementById("goals__dropdown").classList.toggle("osekai__dropdown-hidden");
    });

    window.addEventListener('click', function (e) {
        if (!document.getElementById("goals__dropdown__button").contains(e.target)) document.getElementById("goals__dropdown").classList.add("osekai__dropdown-hidden");
    });

    document.querySelectorAll("[id^='btn-goals']").forEach(oButton => {
        oButton.addEventListener("click", function () {
            let oRegExp = new RegExp(/(?<=")(\\.|[^"\\]*)(?=")/g);
            let strImgPath = this.innerHTML.split(oRegExp);
            strGoalType = this.id.replace("btn-goals__", "")
            document.getElementById("current__goaltype").src = strImgPath[1];
            document.getElementById("goals__dropdown").classList.add("osekai__dropdown-hidden");
        })
    })

    document.getElementById("goals__add__button").addEventListener("click", () => {
        if (!document.getElementById("goal__input").value) return;
        let nValue = document.getElementById("goal__input").value;
        var xhr = createXHR("/profiles/api/goals.php");
        xhr.send("Value=" + nValue + "&Gamemode=" + new URLSearchParams(window.location.search).get("mode") + "&Type=" + strGoalType);
        xhr.onreadystatechange = function () {
            var oResponse = getResponse(xhr);
            if (handleUndefined(oResponse)) return;
            if (oResponse.toString() == "Success!") {
                loadMode(new URLSearchParams(window.location.search).get("mode"), false);
            }
        };
    })
}

function HideTimelineInfo() {
    document.querySelectorAll("[selector='timeline__dot']").forEach((oControl) => {
        if (oControl.classList.contains("profiles__timeline-dot-active") && !oControl.mouseIsOver) oControl.classList.remove("profiles__timeline-dot-active");
    });
    if (bTimelineHover) return;
    if (Exists(document.getElementById("timeline__edit"))) return;
    if (!document.getElementById("timeline__info").classList.contains("profiles__info-panel-closed")) document.getElementById("timeline__info").classList.add("profiles__info-panel-closed");
    clearTimeout(oTimeout);
}

function loadHome() {
    let url = new URL(window.location);
    url.searchParams.delete("user");
    if (new URLSearchParams(window.location.search).get("mode") == null) {
        url.searchParams.set('mode', "all");
    }
    window.history.replaceState({}, '', url);
    document.getElementById('profile').classList.add('hidden');
    document.getElementById('home').classList.remove('hidden');
    loadHomeData();
    LoadRecentlyViewed(); // hubz/tanza: put this here, no need to load this whne just on a profile lol
}

function loadHomeData() {
    for (var i = 0; i < document.getElementById("mode__list__home").children.length; i++) {
        if (document.getElementById("mode__list__home").children[i].classList.contains("profiles__gamemode-button-active")) document.getElementById("mode__list__home").children[i].classList.remove("profiles__gamemode-button-active");
        if (document.getElementById("mode__list__home").children[i].getAttribute("mode") == new URLSearchParams(window.location.search).get("mode")) document.getElementById("mode__list__home").children[i].classList.add("profiles__gamemode-button-active");
    }

    var xhr = createXHR("/profiles/api/rankings.php");
    xhr.send("mode=" + new URLSearchParams(window.location.search).get("mode"));
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        document.getElementById("profiles__ranking").innerHTML = "";
        let counter = 0;
        let pp = 0;
        Object.keys(oResponse).forEach(function (index) {
            counter += 1;
            if (new URLSearchParams(window.location.search).get("mode") == "all") pp = oResponse[index].stdev_pp;
            if (new URLSearchParams(window.location.search).get("mode") == "osu") pp = oResponse[index].standard_pp;
            if (new URLSearchParams(window.location.search).get("mode") == "fruits") pp = oResponse[index].ctb_pp;
            if (new URLSearchParams(window.location.search).get("mode") == "taiko") pp = oResponse[index].taiko_pp;
            if (new URLSearchParams(window.location.search).get("mode") == "mania") pp = oResponse[index].mania_pp;

            document.getElementById("profiles__ranking").innerHTML += `<div class="profiles__ranking-user" onclick="loadUser(${oResponse[index].id});">` +
                `<img src="https://a.ppy.sh/${oResponse[index].id}" class="profiles__ranking-pfp">` +
                `<div class="profiles__ranking-texts">` +
                `<p class="profiles__ranking-username">${oResponse[index].name}</p>` +
                `<p class="profiles__ranking-pp">${pp}pp</p>` +
                `</div>` +
                `<p class="profiles__ranking-rank"><span>#</span>${counter}</p>` +
                `</div>`;
        });
    };
}

function loadUser(uid) {
    let url = new URL(window.location);
    url.searchParams.set('user', uid);
    window.history.pushState({}, '', url);
    document.getElementById('profile').classList.remove('hidden');
    document.getElementById('home').classList.add('hidden');
    loadMode(null);
}

function loadCurrentUser() {
    let url = new URL(window.location);
    url.searchParams.set('user', nUserID);
    window.history.pushState({}, '', url);
    document.getElementById('profile').classList.remove('hidden');
    document.getElementById('home').classList.add('hidden');
    loadMode(null);
}

function updateVisitedList(id) {
    // api/record_visit.php?userId
    var xhr = createXHR("/profiles/api/record_visit.php?userId=" + id);
    xhr.onreadystatechange = function () {

    };
    xhr.send();
}

function loadMode(mode, completeReload = true) {
    FillData(new URLSearchParams(window.location.search).get("user"), mode, completeReload);
    document.querySelectorAll(".profiles__gamemode-button-active").forEach(oActiveNode => oActiveNode.classList.remove("profiles__gamemode-button-active"));
    document.querySelectorAll("[mode]").forEach(oDiv => {
        if (oDiv.getAttribute("mode") == mode) oDiv.classList.add("profiles__gamemode-button-active");
    });
}

function getTooltipWording(mode) {
    if (mode == "all") {
        return "This user does not have enough PP across all modes to be ranked." // TODO: reword?
    } else {
        return "This user is not ranked in this mode."
    }
}

function removeTip(el) {
    // simply remove all event listeners
    if (el._tippy != null) {
        el._tippy.destroy();
    }
}

async function FillData(uid, mode, completeReload = true) {
    if (completeReload) {
        openLoader(GetStringRawNonAsync("profiles", "loading"));
    }
    let url = new URL(window.location);

    nUserID != new URLSearchParams(window.location.search).get("user") ? document.querySelectorAll("[selector='user__control']").forEach((oControl) => oControl.classList.add("hidden")) : document.querySelectorAll("[selector='user__control']").forEach((oControl) => oControl.classList.remove("hidden"));
    // If mode is null then default to osu as its the most common one
    let oldmode = mode
    if (mode == null) {
        mode = "osu";
        url.searchParams.set('mode', "osu");
        window.history.replaceState({}, '', url);
    }

    let oData = JSON.parse(await API_GetUser(uid, mode));

    // If mode wasn't specified and modes don't match, fetch the correct gamemode
    // Else if mode WAS specified then we already have the correct data
    // Due to the code above that defaults to standard, this makes it so that users that play standard are actually fetched once as we already fetched standard
    if (mode != oData.playmode && oldmode == null) {
        url.searchParams.set('mode', oData['playmode']);
        window.history.replaceState({}, '', url);
        loadMode(new URLSearchParams(window.location.search).get("mode"));
        closeLoader();
        return;
    }

    updateVisitedList(uid);

    //console.log(JSON.stringify(oData)); //uncomment to see all Data
    if (oData == null) alert("No Data Available. Please contact Osekai support.");

    //upper main panel
    if (completeReload) {
        if (oData.avatar_url != null) document.querySelectorAll("[selector='pfp']").forEach((oPfp) => { oPfp.setAttribute("src", oData.avatar_url) });
        /* if (oData.username != null) document.getElementById("name__main").innerHTML = oData.username.endsWith("s") ? oData.username + "'" : oData.username + "'s"; */
        // if (oData.username != null) document.getElementById("name__main").innerHTML = GetStringRawNonAsync("profiles", "profile.bar.user.new", [oData.username]);
        if (oData.username != null && oData.username.endsWith("s")) {
            document.getElementById("name__main").innerHTML = GetStringRawNonAsync("profiles", "profile.bar.user.new-s", [oData.username]);
        } else if (oData.username != null) {
            document.getElementById("name__main").innerHTML = GetStringRawNonAsync("profiles", "profile.bar.user.new", [oData.username]);
        }
        if (oData.username != null) document.getElementById("name__sub").innerHTML = `
        <div class="profiles__cover-info-name">
            <img class="profiles__cover-country" src="https://osu.ppy.sh/images/flags/${oData.country_code}.png" id="country__flag">
            <h1>${oData.username}</h1> 
        </div>
        <div id="user__badges" class="profiles__user-badges"></div>`;
        document.getElementById("osu_link").href = `https://osu.ppy.sh/users/${oData.id}`;

        // <a data-tippy-content="View profile on osu.ppy.sh" href="https://osu.ppy.sh/users/${oData.id}" target="_blank" class="profiles__cover-link"><i class="fas fa-external-link-alt"></i>
        tippy(document.getElementById("name__sub").querySelectorAll("[data-tippy-content]"));
        if (oData.cover_url != null) {
            document.getElementById("cover__img").style.setProperty("background-image", "url('" + oData.cover_url + "')");
            var bi = document.querySelectorAll("[selector='cover_blur_img']");
            for (var x = 0; x < bi.length; x++) {
                bi[x].src = oData.cover_url;
            }
        }
    }
    //if (oData.statistics.global_rank != null) document.getElementById("current__rank__global").innerHTML = "Global #" + FormatNumber(oData.statistics.global_rank);
    if (oData.statistics.global_rank > 0) {
        document.getElementById("current__rank__global").innerHTML = "Global <strong>#" + FormatNumber(oData.statistics.global_rank) + "</strong>";
        // remove tippy tooltip
        removeTip(document.getElementById("current__rank__global"));
    } else {
        document.getElementById("current__rank__global").innerHTML = "Global <strong>#?</strong>";
        tippy(document.getElementById("current__rank__global"), {
            content: getTooltipWording(mode)
        });
    }
    if (Exists(oData.usergroups)) {
        array = [];
        for (var x = 0; x < oData.usergroups.length; x++) {
            array.push(oData.usergroups[x]['GroupId'])
        }
        document.getElementById("user__badges").innerHTML = groupUtils.badgeHtmlFromArray(array);
        document.getElementById("user__badges").classList.remove("hidden");
    } else {
        document.getElementById("user__badges").classList.add("hidden");
    }

    //lower main panel
    if (completeReload) {
        if (oData.location != null) document.getElementById("location").innerHTML = `<i class="fas fa-map-marker-alt"></i> ${unHTML(oData.location)}`;
        if (oData.join_date != null) {
            dJoined = new Date(oData.join_date);
            document.getElementById("arrival__date").innerHTML = GetStringRawNonAsync("profiles", "profile.info.joined", [dJoined.toDateString()]);
        }
        if (oData.playstyle != null) document.getElementById("hardware").innerHTML = GetStringRawNonAsync("profiles", "profile.info.playsWith", [oData.playstyle.join(", ")]);
        if (oData.interests != null) document.getElementById("interests").innerHTML = `<i class="fas fa-heart"></i> ${unHTML(oData.interests)}`;
        if (oData.occupation != null) document.getElementById("occupation").innerHTML = `<i class="fas fa-briefcase"></i> ${unHTML(oData.occupation)}`;
        if (oData.discord != null) document.getElementById("discord").innerHTML = `<a id="discord">${unHTML(oData.discord)}</a>`;
        if (oData.twitter != null) ReplaceWithClickableLink(document.getElementById("twitter"), unHTML(oData.twitter), "https://twitter.com/" + oData.twitter);
        if (oData.website != null) ReplaceWithClickableLink(document.getElementById("website"), unHTML(oData.website), oData.website);
    }

    //Timeline
    LoadTimelineEntries(oData);
    allowAddTimelineEntry(oData); // ? [Hubz] pass in oData here for min/max date calculation 
    if (Exists(oData.join_date)) document.getElementById("timeline__start").innerHTML = new Date(oData.join_date).toDateString().split(' ').slice(1).join(' ');
    document.getElementById("timeline__end").innerHTML = new Date().toDateString().split(' ').slice(1).join(' ');

    //Goals
    document.getElementById("goals__panel").innerHTML = "";
    if (!document.getElementById("goals__welcome").classList.contains("hidden")) document.getElementById("goals__welcome").classList.add("hidden");
    if (oData.goals != null && oData.goals.length > 0) {
        oData.goals.forEach(oGoal => document.getElementById("goals__panel").innerHTML += GetGoal(oData, oGoal));
    } else {
        if (nUserID != new URLSearchParams(window.location.search).get("user")) {
            document.getElementById("goals__panel").innerHTML += `<div class="profiles__goals-nogoals">` +
                `<p>The user has no goals yet!</p>` +
                `</div>`;
        } else {
            if (document.getElementById("goals__welcome").classList.contains("hidden")) document.getElementById("goals__welcome").classList.remove("hidden");
        }
        //document.getElementById("goals__section").classList.add("hidden");
    }

    //stats section
    if (oData.statistics.pp != null) document.getElementById("pp__count").innerHTML = FormatNumber(oData.statistics.pp);
    if (oData.statistics.global_rank > 0) {
        if (oData.statistics.global_rank != null) {
            document.getElementById("current__global__rank").innerHTML = "#" + FormatNumber(oData.statistics.global_rank);
            // remove tooltip
            removeTip(document.getElementById("current__global__rank"));
        }
    } else {
        document.getElementById("current__global__rank").innerHTML = "#?";
        tippy(document.getElementById("current__global__rank"), {
            content: getTooltipWording(mode)
        });
    }
    if (oData.statistics.country_rank > 0) {
        if (oData.statistics.country_rank != null) {
            document.getElementById("current__country__rank").innerHTML = "#" + FormatNumber(oData.statistics.country_rank);
            // remove tooltip
            removeTip(document.getElementById("current__country__rank"));
        }
    } else {
        document.getElementById("current__country__rank").innerHTML = "#?";
        tippy(document.getElementById("current__country__rank"), {
            content: getTooltipWording(mode)
        });
    }
    if (oData.rank_history != null) colRankChart = oData.rank_history.data;
    if (oData.statistics.play_time != null) document.getElementById("play__time").innerHTML = FormatPlaytime(oData.statistics.play_time);
    if (oData.statistics.play_count != null) document.getElementById("play__count").innerHTML = FormatNumber(oData.statistics.play_count);
    if (oData.statistics.grade_counts.ssh != null) document.getElementById("ssh__count").innerHTML = FormatNumber(oData.statistics.grade_counts.ssh);
    if (oData.statistics.grade_counts.ss != null) document.getElementById("ss__count").innerHTML = FormatNumber(oData.statistics.grade_counts.ss);
    if (oData.statistics.grade_counts.sh != null) document.getElementById("sh__count").innerHTML = FormatNumber(oData.statistics.grade_counts.sh);
    if (oData.statistics.grade_counts.s != null) document.getElementById("s__count").innerHTML = FormatNumber(oData.statistics.grade_counts.s);
    if (oData.statistics.grade_counts.a != null) document.getElementById("a__count").innerHTML = FormatNumber(oData.statistics.grade_counts.a);
    if (oData.statistics.hit_accuracy != null) document.getElementById("accuracy").innerHTML = FormatNumber(oData.statistics.hit_accuracy, 2) + "%";

    //stats on null
    if (oData.statistics.pp == null) document.getElementById("pp__count").innerHTML = "-";
    if (oData.statistics.global_rank == null) document.getElementById("current__global__rank").innerHTML = "-";
    if (oData.statistics.country_rank == null) document.getElementById("current__country__rank").innerHTML = "-";
    if (oData.statistics.play_time == null) document.getElementById("play__time").innerHTML = "0m";
    if (oData.statistics.play_count == null) document.getElementById("play__count").innerHTML = "0";
    if (oData.statistics.grade_counts.ssh == null) document.getElementById("ssh__count").innerHTML = "0";
    if (oData.statistics.grade_counts.ss == null) document.getElementById("ss__count").innerHTML = "0";
    if (oData.statistics.grade_counts.sh == null) document.getElementById("sh__count").innerHTML = "0";
    if (oData.statistics.grade_counts.s == null) document.getElementById("s__count").innerHTML = "0";
    if (oData.statistics.grade_counts.a == null) document.getElementById("a__count").innerHTML = "0";
    if (oData.statistics.hit_accuracy == null) document.getElementById("accuracy").innerHTML = "";

    //medal section
    if (oData.user_achievements != null) {
        //upper section
        document.getElementById("medal__count").innerHTML = FormatNumber(Object.keys(oData.user_achievements).length);
        colRankMedals = [];

        for (nIndex = 0; nIndex < Object.keys(oData.user_achievements).length; nIndex++) {
            if (typeof (oData.user_achievements[nIndex]) === 'undefined') continue;
            colRankMedals[nIndex] = oData.user_achievements[nIndex].achieved_at;
        }

        colRankMedals.splice(0, 0, dJoined);

        if (oData.user_achievements_total.global_rank != null) document.getElementById("medal__rank__global").innerHTML = "#" + FormatNumber(oData.user_achievements_total.global_rank);
        if (oData.user_achievements_total.completion != null) {
            document.getElementById("completion__bar").style = "width: " + oData.user_achievements_total.completion + "%;";
            document.getElementById("completion__bar__scheme").classList.forEach((v, k) => {
                if (v == "col95club" ||
                    v == "col90club" ||
                    v == "col80club" ||
                    v == "col60club" ||
                    v == "col40club")
                    document.getElementById("completion__bar__scheme").classList.remove(v)
            });
            document.getElementById("completion__bar__scheme").classList.add(ColorizeBar(oData.user_achievements_total.completion));
            // completion = user_achievements_total.completion (88,12%)
            // medalcount = Object.keys(oData.user_achievements).length (230)
            // 100% = 230 / 88.12 * 100 = 261 = total achievements
            document.getElementById("completion__amount").innerHTML = oData.user_achievements_total.completion + "%";

            let TotalAchievements = GetTotalAchievements(oData.user_achievements_total.completion, Object.keys(oData.user_achievements).length);
            document.getElementById("next__club").innerHTML = GetNextClub(oData.user_achievements_total.completion);
            document.getElementById("medals__to__go").innerHTML = GetStringRawNonAsync("profiles", "profile.medals.togo", [GetMedalsToNextClub(TotalAchievements, Object.keys(oData.user_achievements).length, oData.user_achievements_total.completion)]);
        }

        //rarest medal
        oData.user_achievements.sort((a, b) => {
            return a.frequency - b.frequency;
        })
        var oRarestMedal = oData.user_achievements[0];
        if (oData.user_achievements.length > 0) {
            if (oData.user_achievements[0]['name'] != undefined) {
                document.getElementById("rarest__medal__panel").classList.remove("hidden");
                document.getElementById("rarest__medal__panel__error").classList.add("hidden");

                document.getElementById("rarest__medal__title").innerHTML = oRarestMedal['name'];
                document.getElementById("rarest__medal__description").innerHTML = oRarestMedal['description'];
                document.getElementById("rarest__medal__img").src = oRarestMedal['link'];
                document.getElementById("rarest__medal__bg").src = oRarestMedal['link'];
                document.getElementById("rarest__medal__frequency").innerHTML = GetStringRawNonAsync("profiles", "profile.medals.percentOfPlayers", [Math.round(oRarestMedal['frequency'] * 100) / 100]);
                document.getElementById("rarest__medal__panel").addEventListener("click", () => medalPopupV2.showMedalFromName(oRarestMedal['name']));
            } else {
                document.getElementById("rarest__medal__panel").classList.add("hidden");
                document.getElementById("rarest__medal__panel__error").classList.remove("hidden");
            }
        }

        //lower section
        oData.user_achievements.sort((a, b) => {
            return new Date(b.achieved_at) - new Date(a.achieved_at);
        })

        document.getElementById("medals__history").innerHTML = "";
        oData.user_achievements.forEach(oAchievement => {
            let oYear = new Date(oAchievement.achieved_at).getUTCFullYear();
            let oHistory = document.getElementById("medals__history");
            if (typeof (document.getElementById("medals__" + oYear) == undefined) && document.getElementById("medals__" + oYear) == null) {
                let oSection = document.createElement("div");
                oSection.id = "medals__" + oYear;
                oSection.classList.add("profiles__mh-section");

                let oLeft = document.createElement("div");
                oLeft.classList.add("osekai__section-header");

                let oDivYear = document.createElement("div");
                oDivYear.classList.add("osekai__section-header-left");

                let oParagraphYear = document.createElement("h3");
                oParagraphYear.innerHTML = oYear;

                let oDivYearText = document.createElement("div");
                oDivYearText.classList.add("osekai__section-header-right");

                let oParagraphNew = document.createElement("h3");
                //oParagraphYear.innerHTML = "<span id='span__" + oYear + "' class='big'>0</span><br>new<br>medals";
                oParagraphNew.innerHTML = GetStringRawNonAsync("profiles", "profile.medals.history.new", ['<span id="span__' + oYear + '" class="big">0</span>']);

                let oGrid = document.createElement("div");
                oGrid.classList.add("profiles__mhs-right");
                oGrid.classList.add("profiles__mhs-grid");
                oGrid.id = "grid__" + oYear;

                oDivYear.appendChild(oParagraphYear);
                oDivYearText.appendChild(oParagraphNew);
                oLeft.appendChild(oDivYear);
                oLeft.appendChild(oDivYearText);
                oSection.appendChild(oLeft);
                oSection.appendChild(oGrid);
                oHistory.appendChild(oSection);
            }
            let oImgContainer = document.createElement("a");
            //console.log(oAchievement);
            try {
                oImgContainer.addEventListener("click", () => medalPopupV2.showMedalFromName(oAchievement.name));
                oImgContainer.setAttribute("data-tippy-content-medal-date", new Date(oAchievement.achieved_at).toDateString());
                // on the left
                oImgContainer.setAttribute("data-tippy-placement", "left");

                tippy(oImgContainer, {
                    content: oImgContainer.getAttribute("data-tippy-content-medal-date"),
                });

                let oImg = document.createElement("img");
                oImg.classList.add("profiles__mhs-medal");
                // add medalname attribute
                oImg.setAttribute("medalname", oAchievement.name);
                oImg.src = oAchievement.link;
                document.getElementById("grid__" + oYear).appendChild(oImgContainer);
                oImgContainer.appendChild(oImg);
                document.getElementById("span__" + oYear).innerHTML = parseInt(document.getElementById("span__" + oYear).innerHTML) + 1;
            } catch (error) {
                console.log(error);
            }
        })

        // <mulraf> THIS MIGHT BREAK
        // If new medal groups are released, then the osu_api_functions.php will need to adopt those new medal groups (like 'beatmap spotlights'). You will note they are hardcoded at several places there. Just add the new groups in the SQL and the arrays.
        // Also the last medals for the dedication medals are hardcoded within the function 'GetPreviousAchievement'. In case new ones are released like 100k osu play count or something.

        // unobtained medals
        if (document.getElementById("unachieved_panel")) {
            document.getElementById("unachieved_panel").innerHTML = "";
        }
        if (Exists(oData.unachieved)) {
            oData.unachieved.sort((a, b) => {
                if (a.grouping == b.grouping) {
                    if (a.mode == b.mode) {
                        if (a.ordering == b.ordering) {
                            return parseInt(a.medalid) > parseInt(b.medalid) ? 1 : -1;
                        }
                        return b.ordering > a.ordering ? 1 : -1;
                    }
                    return b.mode > a.mode ? 1 : -1;
                }
                return b.grouping > a.grouping ? 1 : -1;
            })

            let oLastMode = "";
            let oLastModeSection;

            oData.unachieved.forEach(oAchievement => {
                let oSectionID = `unobtained_section_${oAchievement.grouping}`;
                let oGridID = `unobtained_grid_${oAchievement.grouping}`;
                let oProgressID = `unobtained_progress_${oAchievement.grouping}`;
                let oBarID = `unobtained_bar_${oAchievement.grouping}`;
                let oInnerSectionID = `unobtained_inner_${oAchievement.grouping}`;

                if (!document.getElementById(oSectionID)) {
                    let oSection = document.createElement("div");
                    oSection.classList.add("profiles__unachievedmedals-section");
                    oSection.classList.add(oAchievement.grouping.replace(/\s/g, "-").toLowerCase());
                    oSection.id = oSectionID;

                    let oSectionHeader = document.createElement("div");
                    oSectionHeader.classList.add("profiles__unachievedmedals-section-header");

                    let oHeader = document.createElement("p");
                    oHeader.classList.add("profiles__unachievedmedals-section-header-left");
                    oHeader.innerHTML = oAchievement.grouping;

                    let oDivProgress = document.createElement("div");
                    oDivProgress.classList.add("osekai__progress-bar");

                    let oDivBar = document.createElement("div");
                    oDivBar.style.width = "100%";
                    oDivBar.classList.add("osekai__progress-bar-inner");
                    oDivBar.id = oBarID;

                    let oProgressString = document.createElement("p");
                    oProgressString.classList.add("profiles__unachievedmedals-section-header-right");

                    let oProgressCurrent = document.createElement("span");
                    oProgressCurrent.innerHTML = oData.max_medals_group[oAchievement.grouping.replace(/\s/g, "").toLowerCase()];
                    oProgressCurrent.id = oProgressID;

                    let oProgressTotal = document.createElement("light");
                    oProgressTotal.innerHTML = "/" + oData.max_medals_group[oAchievement.grouping.replace(/\s/g, "").toLowerCase()];

                    oProgressString.appendChild(oProgressCurrent);
                    oProgressString.appendChild(oProgressTotal);

                    oDivProgress.appendChild(oDivBar);

                    oSectionHeader.appendChild(oHeader);
                    oSectionHeader.appendChild(oDivProgress);
                    oSectionHeader.appendChild(oProgressString);

                    let oInnerSection = document.createElement("div");
                    oInnerSection.classList.add("profiles__unachievedmedals-section-inner");
                    oInnerSection.id = oInnerSectionID;

                    let oGrid = document.createElement("div");
                    oGrid.classList.add("profiles__unachievedmedals-section-grid");
                    oGrid.id = oGridID;

                    if (oAchievement.grouping == "Dedication" && mode != "all") {
                        let oDivWarning = document.createElement("div");
                        oDivWarning.classList.add("osekai__generic-warning");
                        oDivWarning.classList.add("osekai__generic-warning-info");

                        let oIWarning = document.createElement("i");
                        oIWarning.classList.add("fas");
                        oIWarning.classList.add("fa-info-circle");

                        let oTextWarning = document.createElement("p");
                        oTextWarning.innerHTML = GetStringRawNonAsync("profiles", "profile.unachiededMedals.trackingWarning");

                        oDivWarning.appendChild(oIWarning);
                        oDivWarning.appendChild(oTextWarning);

                        oInnerSection.appendChild(oDivWarning);
                    }

                    oInnerSection.appendChild(oGrid);

                    oSection.appendChild(oSectionHeader);
                    oSection.appendChild(oInnerSection);

                    if (document.getElementById("unachieved_panel")) {
                        document.getElementById("unachieved_panel").appendChild(oSection);
                    }
                }

                let oGrid = document.getElementById(oGridID);

                if (oGrid) {
                    if (oLastMode != oAchievement.mode || !oGrid.firstChild) {
                        if (oGrid.firstChild) {
                            let oDivider = document.createElement("div");
                            oDivider.classList.add("osekai__divider");
                            oGrid.appendChild(oDivider);
                        }

                        let oGroupSection = document.createElement("div");
                        oGroupSection.classList.add("profiles__unachievedmedals-section-grid-inner");
                        oLastModeSection = oGroupSection;
                        oGrid.appendChild(oLastModeSection);
                    }
                    oLastMode = oAchievement.mode;
                }

                if (document.getElementById(oSectionID)) {
                    let oProgressCount = document.getElementById(oProgressID);
                    let oBar = document.getElementById(oBarID);

                    oProgressCount.innerHTML = parseInt(oProgressCount.innerHTML) - 1;
                    oBar.style.width = (parseInt(oProgressCount.innerHTML) / parseInt(oData.max_medals_group[oAchievement.grouping.replace(/\s/g, "").toLowerCase()]) * 100) + "%";

                    let oImg = document.createElement("img");
                    oImg.src = oAchievement.link;
                    oImg.addEventListener("click", () => medalPopupV2.showMedalFromName(oAchievement.name));

                    if (oAchievement.grouping == "Dedication" && (mode == oAchievement.mode || mode == "all")) {
                        let oList = document.createElement("div");
                        oList.classList.add("profiles__unachievedmedals-section-progress-medal");
                        oList.appendChild(oImg.cloneNode());

                        let oDivSectionProgressInner = document.createElement("div");
                        oDivSectionProgressInner.classList.add("profiles__unachievedmedals-section-progress-inner");

                        let oDivSectionProgressInnerTop = document.createElement("div");
                        oDivSectionProgressInnerTop.classList.add("profiles__unachievedmedals-section-progress-inner-top");

                        let oUnachievedMedalHeader = document.createElement("h3");

                        let oUnachievedMedalProgressCurrentText = document.createElement("light");
                        oUnachievedMedalProgressCurrentText.innerHTML = GetPreviousAchievement(oAchievement.name);
                        if (oUnachievedMedalProgressCurrentText.innerHTML != "") oUnachievedMedalProgressCurrentText.innerHTML = oUnachievedMedalProgressCurrentText.innerHTML + " ->";

                        let oUnachievedMedalProgressGoalText = document.createElement("span");
                        oUnachievedMedalProgressGoalText.innerHTML = " " + oAchievement.name;

                        oUnachievedMedalHeader.appendChild(oUnachievedMedalProgressCurrentText);
                        oUnachievedMedalHeader.appendChild(oUnachievedMedalProgressGoalText);

                        let oUnachievedMedalTopLeft = document.createElement("div");
                        oUnachievedMedalTopLeft.classList.add("profiles__unachievedmedals-section-progress-inner-top-left");

                        let nCurrent = GetCurrentHitCount(oData, oAchievement.mode);
                        let nPrevious = GetAmountFromString(GetPreviousAchievement(oAchievement.name));
                        let nDifference = Math.max(nCurrent, 0);

                        let nCurrentAchievement = GetAmountFromString(oAchievement.name);
                        let nDifferenceAchievements = Math.min(nCurrentAchievement, nCurrentAchievement);

                        let oUnachievedPercentage = document.createElement("p");
                        oUnachievedPercentage.classList.add("percentage");
                        oUnachievedPercentage.innerHTML = FormatNumber(nDifference / nDifferenceAchievements * 100, 2) + "%";

                        let oUnachievedProgress = document.createElement("p");
                        oUnachievedProgress.classList.add("progress");
                        oUnachievedProgress.innerHTML = "[" + GetCurrentHitCount(oData, oAchievement.mode) + " / ";

                        let oUnachievedProgressGoal = document.createElement("light");
                        oUnachievedProgressGoal.innerHTML = " " + GetAmountFromString(oAchievement.name);

                        oUnachievedProgress.appendChild(oUnachievedProgressGoal);
                        oUnachievedProgress.innerHTML += "]";

                        oUnachievedMedalTopLeft.appendChild(oUnachievedPercentage);
                        oUnachievedMedalTopLeft.appendChild(oUnachievedProgress);

                        oDivSectionProgressInnerTop.appendChild(oUnachievedMedalHeader);
                        oDivSectionProgressInnerTop.appendChild(oUnachievedMedalTopLeft);

                        let oDivProgressBar = document.createElement("div");
                        oDivProgressBar.classList.add("osekai__progress-bar");

                        let oDivInnerProgressBar = document.createElement("div");
                        oDivInnerProgressBar.classList.add("osekai__progress-bar-inner");
                        oDivInnerProgressBar.style.width = oUnachievedPercentage.innerHTML;

                        oDivProgressBar.appendChild(oDivInnerProgressBar);

                        oDivSectionProgressInner.appendChild(oDivSectionProgressInnerTop);
                        oDivSectionProgressInner.appendChild(oDivProgressBar);
                        oList.appendChild(oDivSectionProgressInner);

                        document.getElementById(oGridID).appendChild(oList);
                    }

                    let oGridMedal = document.createElement("div");
                    oGridMedal.classList.add("profiles__unachievedmedals-section-grid-medal");

                    oGridMedal.appendChild(oImg);
                    oLastModeSection.appendChild(oGridMedal);
                }
            });
        }
    }

    //Toggle Hidden
    ToggleStat(document.getElementById("current__rank__global"), oData.statistics.global_rank);
    if (completeReload) {
        ToggleStat(document.getElementById("location").parentElement, oData.location);
        ToggleStat(document.getElementById("hardware").parentElement, oData.playstyle);
        ToggleStat(document.getElementById("interests").parentElement, oData.interests);
        ToggleStat(document.getElementById("occupation").parentElement, oData.occupation);
        ToggleStat(document.getElementById("discord").parentElement.parentElement, oData.discord);
        ToggleStat(document.getElementById("twitter").parentElement.parentElement, oData.twitter);
        ToggleStat(document.getElementById("website").parentElement.parentElement, oData.website);
    }
    if (oData.rank_history != null) ToggleStat(document.getElementById("stats__graph__area"), oData.rank_history.data);

    //Toggle Entire Bar
    if (completeReload) {
        ToggleBar(document.getElementById("bar__interests"), [document.getElementById("interests").parentElement, document.getElementById("occupation").parentElement]);
        ToggleBar(document.getElementById("bar__social"), [document.getElementById("discord").parentElement, document.getElementById("twitter").parentElement, document.getElementById("website").parentElement]);
    }

    //Final Functions
    FillChartStats();
    FillChartMedals();
    if (completeReload) {
        Comments_Require("", document.getElementById("comments__box"), true, -1, new URLSearchParams(window.location.search).get("user")); // <mulraf> Initial Comment Loading </mulraf>
        closeLoader();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    // <hubz> Snapshots Integration </hubz>
    Snapshots.GetSnapshots(new URLSearchParams(window.location.search).get("user"));
    // <hubz> user banner integration </hubz>
    // this can only run if the user being looked at is the current user
    if (nUserID == new URLSearchParams(window.location.search).get("user")) {
        UserBanner.InitUserBanner();
        document.getElementById("banner-panel").classList.remove("hidden");
    } else {
        console.log("UserBanner: Not the current user");
        document.getElementById("banner-panel").classList.add("hidden");
    }

    if (completeReload) {
        if (oData.username != null) {
            // ! CHROMB EASTER EGG
            // please don't remove this easter egg, its funny ^_^
            if (oData.username == "chromb") {
                document.body.classList.add("chromb");
                document.getElementById("country__flag").src = "https://a.ppy.sh/10238680?1617745761.png";
            } else {
                document.body.classList.remove("chromb");
            }
        }
    }
}

function ToggleStat(oElement, oData) {
    if (!Exists(oData) && !oElement.classList.contains("hidden")) {
        oElement.classList.add("hidden");
    } else if (Exists(oData) && oElement.classList.contains("hidden")) {
        oElement.classList.remove("hidden");
    }
}

function ToggleBar(oParentNode, colChildNodes) {
    let bHide = true;
    colChildNodes.forEach((oInnerChild) => {
        if (!oInnerChild.classList.contains("hidden")) bHide = false;
    });
    if (bHide) oParentNode.classList.add("hidden");
}

function FormatPlaytime(playtime) {
    let strOut = "";
    let nTime = 0;
    if (FormatNumber(playtime / 86400, 1) > 0) {
        nTime = FormatNumber(playtime / 86400, 0);
        strOut += nTime + "d";
        playtime -= (nTime * 86400);
    }
    if (FormatNumber(playtime / 3600, 1) > 0) {
        nTime = FormatNumber(playtime / 3600, 0);
        strOut += " " + nTime + "h";
        playtime -= nTime * 3600;
    }
    if (FormatNumber(playtime / 60, 1) > 0) strOut += " " + FormatNumber(playtime / 60, 0) + "m";
    return strOut;
}

function GetGoalStatus(Data, Goal) {
    if (Goal.Type == "PP" && Exists(Data.statistics.pp)) return Data.statistics.pp;
    if (Goal.Type == "SS Count" && Exists(Data.statistics.grade_counts.ss) && Exists(Data.statistics.grade_counts.ssh)) return parseInt(Data.statistics.grade_counts.ss) + parseInt(Data.statistics.grade_counts.ssh);
    if (Goal.Type == "Medals" && Exists(Data.user_achievements)) return Object.keys(Data.user_achievements).length;
    if (Goal.Type == "% Medals" && Exists(Data.user_achievements)) return Data.user_achievements_total.completion;
    if (Goal.Type == "Badges" && Exists(Data.badges)) return Object.keys(Data.badges).length;
    if (Goal.Type == "Rank" && Exists(Data.statistics.global_rank)) return Data.statistics.global_rank;
    if (Goal.Type == "Country Rank" && Exists(Data.statistics.country_rank)) return Data.statistics.country_rank;
    if (Goal.Type == "Level" && Exists(Data.statistics.level.current)) return Data.statistics.level.current;
    if (Goal.Type == "Ranked Score" && Exists(Data.statistics.ranked_score)) return Data.statistics.ranked_score;
}

function GetGoalProgress(Data, Goal) {
    if (Goal.Claimed !== null) return 100;
    if (Goal.Type == "PP" && Exists(Data.statistics.pp)) return DefaultProgressFormula(Data, Goal);
    if (Goal.Type == "SS Count" && Exists(Data.statistics.grade_counts.ss) && Exists(Data.statistics.grade_counts.ssh)) return DefaultProgressFormula(Data, Goal);
    if (Goal.Type == "Medals" && Exists(Data.user_achievements)) return DefaultProgressFormula(Data, Goal);
    if (Goal.Type == "% Medals" && Exists(Data.user_achievements)) return DefaultProgressFormula(Data, Goal);
    if (Goal.Type == "Badges" && Exists(Data.badges)) return DefaultProgressFormula(Data, Goal);
    if (Goal.Type == "Rank" && Exists(Data.statistics.global_rank)) return GetGoalStatus(Data, Goal) > 0 ? FormatNumber(Math.min(parseInt(Goal.Value) / parseInt(GetGoalStatus(Data, Goal)) * 100, 100), 2) : 0;
    if (Goal.Type == "Country Rank" && Exists(Data.statistics.country_rank)) return GetGoalStatus(Data, Goal) > 0 ? FormatNumber(Math.min(parseInt(Goal.Value) / parseInt(GetGoalStatus(Data, Goal)) * 100, 100), 2) : 0;
    if (Goal.Type == "Level" && Exists(Data.statistics.level.current)) return LevelProgressFormula(Data, Goal.Value);
    if (Goal.Type == "Ranked Score" && Exists(Data.statistics.ranked_score)) return DefaultProgressFormula(Data, Goal);
    return 0;
}

function LevelProgressFormula(Data, Level) {
    if (Data.statistics.level.current >= Level) return 100;
    let nCurrentScore = Data.statistics.total_score;
    let nScoreNextLevel = GetScoreForLevel(Level);
    return FormatNumber(Math.min(nCurrentScore / nScoreNextLevel * 100, 100), 2);
}

function GetScoreForLevel(level) {
    if (level <= 100) {
        return Math.round(5000 / 3 * (4 * Math.pow(level, 3) - 3 * Math.pow(level, 2) - level) + 1.25 * Math.pow(1.8, level - 60));
    } else {
        return 26931190827 + 99999999999 * (level - 100);
    }
}

function DefaultProgressFormula(Data, Goal) {
    return FormatNumber(Math.min(parseInt(GetGoalStatus(Data, Goal)) / parseInt(Goal.Value) * 100, 100), 2);
}

function Exists(nullable) {
    if (nullable === undefined || nullable == null) return false;
    return true;
}

function ReplaceWithClickableLink(oElement, strData, strLink) {
    oElement.innerHTML = oElement.innerHTML = " " + strData;
    oElement.style.textDecoration = "underline";
    oElement.href = strLink;
}

function GetGoal(Data, Goal) {
    if (new URLSearchParams(window.location.search).get("mode") == "all" && Goal.Gamemode !== "all" && Goal.Gamemode !== "null") {
        Data = Data[Goal.Gamemode];
    }
    let strProgress = GetGoalStatus(Data, Goal);
    return `<div id="goal_${Goal.ID}" class="profiles__goal">` +
        `<div class="profiles__goal-container-main">` +
        `<div class="osekai__progress-bar ${GetGoalProgress(Data, Goal) == 100 ? "osekai__progress-bar-gold" : ""}">` +
        `<div class="osekai__progress-bar-inner" style="width: ${GetGoalProgress(Data, Goal)}%;"></div>` +
        `</div>` +
        `<div class="profiles__goal-texts">` +
        `<div class="profiles__goal-left">` +
        `<div class="profiles__goal-large-text">` +
        `<p>Reach ${FormatNumber(parseInt(Goal.Value))} ${Goal.Type}${new URLSearchParams(window.location.search).get("mode") == "all" && Goal.Gamemode !== "null" ? " in " + Goal.Gamemode.replace("fruits", "catch") : ""}</p>` +
        `</div>` +
        `<div class="profiles__goal-small-text">` +
        `<p>${FormatNumber(strProgress)} ${Goal.Type} (${GetGoalProgress(Data, Goal)}%)</p>` +
        `</div>` +
        `</div>` +
        `<div class="profiles__goal-right">` +
        `<div class="profiles__goal-large-text">` +
        `<p>started <strong>${TimeAgo.inWords(new Date(Goal.CreationDate).getTime())}</strong></p>` +
        `</div>` +
        (Goal.Claimed !== null ?
            `<div class="profiles__goal-small-text">` +
            `<p>claimed <strong>${TimeAgo.inWords(new Date(Goal.Claimed).getTime())}</strong></p>` +
            `</div>` : ``) +
        `</div>` +
        `</div>` +
        `</div>` + (nUserID == new URLSearchParams(window.location.search).get("user") ?
            `<div class="profiles__goal-container-buttons">` +
            (GetGoalProgress(Data, Goal) == 100 && Goal.Claimed == null ? `<div onclick="ClaimGoal(${(Goal.ID * GetGoalProgress(Data, Goal)) + parseInt(Goal.Value)});" class="profiles__goal-claim-button profiles__goal-button">` +
                `<i class="fas fa-flag"></i>` +
                `</div>` : ``) +
            `<div onclick="DeleteGoal(${Goal.ID});" class="profiles__goal-delete-button profiles__goal-button">` +
            `<i class="far fa-times-circle"></i>` +
            `</div>` +
            `</div>` : ``) +
        `</div>`;
}

function DeleteGoal(GoalID) {
    var xhr = createXHR("/profiles/api/goals.php");
    xhr.send("GoalID=" + GoalID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            document.getElementById("goal_" + GoalID).remove();
        }
    };
}

function ClaimGoal(GoalID) {
    var xhr = createXHR("/profiles/api/goals.php");
    xhr.send("ClaimID=" + GoalID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            loadMode(new URLSearchParams(window.location.search).get("mode"), false);
        }
    };
}

function ColorizeBar(Value) {
    if (Value >= 95) return "col95club";
    if (Value >= 90) return "col90club";
    if (Value >= 80) return "col80club";
    if (Value >= 60) return "col60club";
    if (Value >= 40) return "col40club";
    return "colnoclub";
}

function GetMedalsToNextClub(TotalAchievements, Achievements, Completion) {
    if (Completion >= 95) return TotalAchievements - Achievements;
    if (Completion >= 90) return Math.ceil(TotalAchievements * 0.95) - Achievements;
    if (Completion >= 80) return Math.ceil(TotalAchievements * 0.90) - Achievements;
    if (Completion >= 60) return Math.ceil(TotalAchievements * 0.80) - Achievements;
    if (Completion >= 40) return Math.ceil(TotalAchievements * 0.60) - Achievements;
    return Math.ceil(TotalAchievements * 0.40) - Achievements;
}

function GetNextClub(Value) {
    if (Value >= 95) return "Completion";
    if (Value >= 90) return "95% club";
    if (Value >= 80) return "90% club";
    if (Value >= 60) return "80% club";
    if (Value >= 40) return "60% club";
    return "40% club";
}

function GetCurrentHitCount(data, mode) {
    if (mode == "osu") {
        if (data["osu"] !== undefined) {
            return data["osu"].statistics.play_count;
        } else {
            return data.statistics.play_count;
        }
    } else if (data[mode] !== undefined) {
        return data[mode].statistics.total_hits;
    } else {
        return data.statistics.total_hits;
    }
}

function GetAmountFromString(AchievementString) {
    return Number(AchievementString.replace(/[^0-9\.]+/g, ""));
}

function GetPreviousAchievement(CurrentAchievement) {
    let oAmount = GetAmountFromString(CurrentAchievement);
    //osu
    if (oAmount == 5000) return "";
    if (oAmount == 15000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "5,000");
    if (oAmount == 25000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "15,000");
    if (oAmount == 50000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "25,000");
    //taiko
    if (oAmount == 30000) return "";
    if (oAmount == 300000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "30,000");
    if (oAmount == 3000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "300,000");
    if (oAmount == 30000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+,[0-9]+,[0-9]+/g, "3,000,000");
    // catch
    if (oAmount == 20000) return "";
    if (oAmount == 200000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "20,000");
    if (oAmount == 2000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "200,000");
    if (oAmount == 20000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+\,[0-9]+/g, "2,000,000");
    // mania
    if (oAmount == 40000) return "";
    if (oAmount == 400000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "40,000");
    if (oAmount == 4000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+/g, "400,000");
    if (oAmount == 40000000) return CurrentAchievement.replace(/[0-9]+\,[0-9]+\,[0-9]+/g, "4,000,000");
}

function GetTotalAchievements(Completion, MedalAmount) {
    return Math.round(MedalAmount / Completion * 100);
}

function LoadTimelineEntries(Data) {
    if (!Exists(Data.timeline)) return;
    let oRoot = document.getElementById("timeline__dots");
    oRoot.innerHTML = "";
    colEntries = [];

    Data.timeline.forEach(oEntry => {
        let strGroup = new Date(oEntry.Date).getFullYear() + "." + new Date(oEntry.Date).getMonth();
        if (document.querySelector("[date='" + strGroup + "']") !== null) {
            colGroups.forEach(oGroup => {
                if (oGroup.date == strGroup) oGroup.values.push(oEntry);
            })
        } else {
            let colEntries = {
                date: strGroup,
                values: [oEntry]
            }
            colGroups.push(colEntries);

            let oDot = document.createElement("div");
            oDot.setAttribute("selector", "timeline__dot");
            oDot.setAttribute("date", strGroup)
            oDot.classList.add("profiles__timeline-dot");
            oDot.style.setProperty("--pos", CalculateRelativeTime(Data, oEntry.Date) + "%");

            oDot.addEventListener("mouseover", function (e) {
                e.target.mouseIsOver = true;
                bTimelineHover = true;
                if (!this.classList.contains("profiles__timeline-dot-active")) this.classList.add("profiles__timeline-dot-active");

                let oInfo = document.getElementById("timeline__info");
                oInfo.innerHTML = "";

                let oHeader = document.createElement("div");
                oHeader.classList.add("profiles__info-panel-header");
                oHeader.innerHTML = new Date(oEntry.Date).toLocaleDateString('en-US', { month: 'long' }) + " " + new Date(oEntry.Date).getFullYear();

                oInfo.appendChild(oHeader);

                let strInnerGroup = new Date(oEntry.Date).getFullYear() + "." + new Date(oEntry.Date).getMonth();
                let oGroup = colGroups.filter(obj => { return obj.date == strInnerGroup });

                oGroup[0].values.sort((a, b) => {
                    return new Date(a.Date) > new Date(b.Date);
                })

                oGroup[0].values.forEach(oGroupEntry => {
                    if (document.getElementById(oGroupEntry.ID) !== null) return;
                    let oRow = document.createElement("div");
                    oRow.id = oGroupEntry.ID;
                    oRow.classList.add("profiles__info-panel-row");

                    let oSpanDate = document.createElement("span");
                    oSpanDate.innerHTML = getOrdinalNum(new Date(oGroupEntry.Date).getDate());
                    let oParagraphDate = document.createElement("p");
                    //oParagraphDate.innerHTML = new Date(oGroupEntry.Date).toDateString('en-US', { month: 'short' }) + " ";
                    oParagraphDate.innerHTML = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(new Date(oGroupEntry.Date)) + " ";
                    oParagraphDate.appendChild(oSpanDate);

                    let oNoteTitle = document.createElement("h3");
                    oNoteTitle.innerHTML = strip(oGroupEntry.Note);

                    let oIconWrapper = document.createElement("div");

                    let oIconEdit = document.createElement("i");
                    oIconEdit.classList.add("fas");
                    oIconEdit.classList.add("fa-pencil-alt");

                    oIconWrapper.appendChild(oIconEdit);

                    oIconEdit.addEventListener("click", () => {
                        if (document.getElementById("timeline__edit") !== undefined && document.getElementById("timeline__edit") !== null) {
                            closeTimelinePanel();
                        } else {
                            let oDivEdit = document.createElement("div");
                            oDivEdit.id = "timeline__edit";
                            oDivEdit.classList.add("osekai__panel-header-inputdropdown");

                            oDivEdit.innerHTML = `<input id="new__date" type="date" class="osekai__input" value="${oGroupEntry.Date}">` +
                                `<input id="new__text" type="text" class="osekai__input" placeholder="something">` +
                                `<div class="osekai__button osekai__button-highlighted" onclick="TimelineEditPoint(${oGroupEntry.ID});">Save Changes</div>` +
                                `<div class="osekai__button-row">` +
                                `<div class="osekai__button osekai__button-alert" onclick="RemoveTimelinePoint(${oGroupEntry.ID});">Remove</div>` +
                                `<div class="osekai__button" onclick="closeTimelinePanel()">Cancel</div>` +
                                `</div>`;

                            oIconWrapper.appendChild(oDivEdit);

                            document.getElementById("new__date").setAttribute("max", new Date().toISOString().split("T")[0]);
                            document.getElementById("new__date").setAttribute("min", new Date(Data.join_date).toISOString().split("T")[0]);
                        }
                    })

                    let oEditPanel = document.createElement("div");
                    oEditPanel.classList.add("profiles__info-panel-edit");
                    oEditPanel.appendChild(oIconWrapper);

                    oRow.appendChild(oParagraphDate);
                    oRow.appendChild(oNoteTitle);
                    if (!Exists(oGroupEntry.Changeable) && typeof nUserID !== 'undefined' && nUserID.toString() !== "-1" && nUserID == new URLSearchParams(window.location.search).get("user")) oRow.appendChild(oEditPanel);

                    oInfo.appendChild(oRow);
                    if (oInfo.classList.contains("profiles__info-panel-closed")) oInfo.classList.remove("profiles__info-panel-closed");
                })
            })

            oDot.addEventListener("mouseout", function (e) {
                e.target.mouseIsOver = false;
                oTimeout = setTimeout(HideTimelineInfo, 100);
                bTimelineHover = false;
            });

            oRoot.appendChild(oDot);
        };
    });
}

function TimelineEditPoint(id) {
    if (!Exists(document.getElementById("new__date").value) || document.getElementById("new__date").value == "" || new Date(document.getElementById("new__date").value) > Date.now() || new Date(document.getElementById("new__date").value) < dJoined) return;
    if (!Exists(document.getElementById("new__text").value) || document.getElementById("new__text").value == "") return;
    var xhr = createXHR("/profiles/api/timeline.php");
    xhr.send("NewDate=" + document.getElementById("new__date").value + "&NewNote=" + document.getElementById("new__text").value + "&ItemId=" + id);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            closeTimelinePanel();
            loadMode(new URLSearchParams(window.location.search).get("mode"), false);
        }
    };
}

function RemoveTimelinePoint(id) {
    if (!confirm("Do you really want to remove the entry from your timeline?")) return;
    var xhr = createXHR("/profiles/api/timeline.php");
    xhr.send("Remove=" + id);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            closeTimelinePanel();
            loadMode(new URLSearchParams(window.location.search).get("mode"), false);
        }
    };
}

function getOrdinalNum(n) {
    return n + (n > 0 ? ['th', 'st', 'nd', 'rd'][(n > 3 && n < 21) || n % 10 > 3 ? 0 : n % 10] : '');
}

function CalculateRelativeTime(Data, EntryDate) {
    let oStart = new Date(Data.join_date);
    let oMid = new Date(EntryDate);
    let oNow = new Date();

    let nTotal = oNow.getTime() - oStart.getTime();
    let nOffset = oMid.getTime() - oStart.getTime();

    return nOffset / nTotal * 100;
}

function allowAddTimelineEntry(Data) {
    //osekai__input-disabled
    if (document.contains(document.getElementById("AddEntryButton"))) document.getElementById("AddEntryButton").remove();
    let oElem = document.createElement("div");
    oElem.classList.add("osekai__panel-hwb-right");
    oElem.id = "AddEntryButton";
    oElem.innerHTML =
        '<div id="timeline__input" class="osekai__panel-header-inputdropdown hidden">' +
        '<input id="txtDate" type="date" class="osekai__input" placeholder="Date">' +
        '<input id="txtNote" type="text" class="osekai__input" placeholder="something">' +
        '<div class="osekai__button-row">' +
        '<div class="osekai__button osekai__button-highlighted" onclick="addTimelineEntry();">' + GetStringRawNonAsync("general", "add") + '</div>' +
        '<div class="osekai__button" onclick="closeTimelinePanel();">' + GetStringRawNonAsync("general", "cancel") + '</div>' +
        '</div>' +
        '</div>' +
        '<div onclick="openTimelinePanel();" class="osekai__panel-header-button">' +
        '<i class="fas fa-plus-circle osekai__panel-header-button-icon"></i>' +
        '<p class="osekai__panel-header-button-text">' + GetStringRawNonAsync("profiles", "profile.timeline.addEntry") + '</p>' +
        '</div>';
    if (typeof nUserID !== 'undefined' && nUserID.toString() !== "-1" && nUserID == new URLSearchParams(window.location.search).get("user")) {
        oElem.classList.remove("osekai__input-disabled");
    } else {
        oElem.classList.add("osekai__input-disabled");
    }
    document.getElementById("AddEntryPanel").appendChild(oElem);
    document.getElementById("txtDate").setAttribute("max", new Date().toISOString().split("T")[0]);
    document.getElementById("txtDate").setAttribute("min", new Date(Data.join_date).toISOString().split("T")[0]);
}

function openTimelinePanel() {
    document.getElementById("timeline__input").classList.toggle("hidden");
}

function closeTimelinePanel() {
    document.getElementById("timeline__input").classList.add("hidden");
    if (Exists(document.getElementById("timeline__edit"))) {
        document.getElementById("timeline__edit").classList.add("osekai__panel-header-inputdropdown-hidden");
        setTimeout(document.getElementById("timeline__edit").remove(), 1000);
    }
}

function addTimelineEntry() {
    if (!Exists(document.getElementById("txtDate").value) || document.getElementById("txtDate").value == "" || new Date(document.getElementById("txtDate").value) > Date.now() || new Date(document.getElementById("txtDate").value) < dJoined) return;
    if (!Exists(document.getElementById("txtNote").value) || document.getElementById("txtNote").value == "") return;
    var xhr = createXHR("/profiles/api/timeline.php");
    xhr.send("Date=" + document.getElementById("txtDate").value + "&Note=" + document.getElementById("txtNote").value + "&Mode=" + new URLSearchParams(window.location.search).get("mode"));
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            closeTimelinePanel();
            loadMode(new URLSearchParams(window.location.search).get("mode"), false);
        }
    };
}

function unHTML(data) {
    return data.replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function FillChartMedals() {
    document.getElementById("medals__chart").remove();
    let oMedals = document.createElement("canvas");
    oMedals.id = "medals__chart";
    document.getElementById("medals__chart__wrapper").appendChild(oMedals);
    new Chart(document.getElementById("medals__chart"), {
        type: 'line',
        data: {
            labels: colRankMedals,
            datasets: [{
                data: Array.from(colRankMedals.keys()),
                label: "Medals",
                borderColor: "#fff",
                fill: false
            }
            ]
        },
        options: {
            // custom tooltip text
            elements: {
                point: {
                    radius: 0
                }
            },
            title: {
                display: true,
                text: 'testing'
            },
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: tooltipItems => {
                            title = tooltipItems[0].parsed.x
                            if (title !== null) {
                                //console.dir(title)
                                title = new Date(title).toDateString()
                            }
                            return title
                        },
                    }
                },
            },
            interaction: {
                mode: 'index',
                pointHitDetectionRadius: 1,
                snap: 1,
                intersect: false
            },

            tooltip: {
                snap: 1,
            },
            pointHitDetectionRadius: 1,
            scales: {
                x: {
                    type: 'time',
                    distribution: 'linear',
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                },
                y:
                {
                    grid: {
                        display: false
                    },
                    ticks: {
                        mirror: true,
                        display: false
                    }
                }
            }
        }
    });
}

function FillChartStats() {
    document.getElementById("stats__chart").remove();
    let oStats = document.createElement("canvas");
    oStats.id = "stats__chart";
    document.getElementById("stats__chart__wrapper").appendChild(oStats);
    new Chart(document.getElementById("stats__chart"), {
        type: 'line',
        data: {
            labels: colRankChart,
            datasets: [{
                data: colRankChart,
                label: "Global Rank",
                borderColor: "#fff",
                fill: false
            }
            ]
        },
        options: {
            // custom tooltip text
            elements: {
                point: {
                    radius: 0
                }
            },
            title: {
                display: true,
                text: 'testing'
            },
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: tooltipItems => {
                            if (tooltipItems[0] !== null) {
                                let oDate = new Date();
                                title = new Date(oDate.setDate(oDate.getDate() - 89 + tooltipItems[0].dataIndex)).toDateString();
                            }
                            return title
                        },
                    }
                },
            },
            interaction: {
                mode: 'index',
                pointHitDetectionRadius: 1,
                snap: 1,
                intersect: false
            },

            tooltip: {
                snap: 1,
            },
            pointHitDetectionRadius: 1,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                },
                y:
                {
                    reverse: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        mirror: true,
                        display: false
                    }
                }
            }
        }
    });
}

function FormatNumber(number, mode = 0) {
    if (mode == 0) return (Math.round((parseFloat(number) + Number.EPSILON) * 100) / 100).toLocaleString();
    if (mode == 1) return Math.floor(parseFloat(number) + Number.EPSILON).toLocaleString();
    if (mode == 2) return parseFloat(number).toFixed(2);
    if (mode == 3) return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// <comment system>
document.getElementById("comments__send").addEventListener("click", () => {
    commentsSendClick(-1, new URLSearchParams(window.location.search).get("user"))
});

document.getElementById("filter__button").addEventListener("click", function () {
    document.getElementById("filter__list").classList.toggle("osekai__dropdown-hidden");
});

document.getElementById("filter__date").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.newest");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 2;
    Comments_Sort(document.getElementById("comments__box"), "", -1, new URLSearchParams(window.location.search).get("user"));
});

document.getElementById("filter__votes").addEventListener("click", function () {
    document.getElementById("filter__selected").innerHTML = GetStringRawNonAsync("comments", "sorting.votes");
    document.getElementById("filter__list").classList.remove("osekai__dropdown-hidden");
    COMMENTS_mode = 1;
    Comments_Sort(document.getElementById("comments__box"), "", -1, new URLSearchParams(window.location.search).get("user"));
});
// </comment system>

// <recently viewed (by hubz)>

function LoadRecentlyViewed() {
    mostPopular = false;
    // if the user is logged out, set mostPopular to true
    if (nUserID == -1) {
        mostPopular = true;
    }

    mostPopularURL = "/profiles/api/most_visited";
    recentlyViewedURL = "/profiles/api/recent_visits";

    let xhr = new XMLHttpRequest();

    if (mostPopular) {
        xhr.open("GET", mostPopularURL, true);
    }
    else {
        xhr.open("GET", recentlyViewedURL, true);
    }

    xhr.onload = function () {
        if (this.status == 200) {
            let json = JSON.parse(this.responseText);
            let html = "";

            for (let i = 0; i < json.length; i++) {
                html += `<div class="profiles__ranking-user" onclick="loadUser(${json[i].UserID});"><img src="https://a.ppy.sh/${json[i].UserID}" class="profiles__ranking-pfp">
          <div class="profiles__ranking-texts">
            <p class="profiles__ranking-username">${json[i].Username}</p>
          </div>
        </div>`;
            }

            document.getElementById("recentlyviewed").innerHTML = html;
        } else {
            console.log("error");
        }
    };

    xhr.send();
}

// </recently viewed (by hubz)>


// <snapshots integration (by hubz)>
var Snapshots = {
    Loaded: false,
    LoadedID: 0,
    GetSnapshots: function (user) {
        if (Snapshots.Loaded == true && user == Snapshots.LoadedID) return;

        // we can send a request to api/snapshots_integration.php?userId=<userId>
        // and get back a json array of snapshots
        // don't need to put this data anywhere for now
        var snapshots = [];

        // load the data
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "api/snapshots_integration.php?userId=" + user, true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                // parse the json
                if (xhr.responseText != "No results found") {
                    document.getElementById("snapshots--panel").classList.remove("hidden");
                    snapshots = JSON.parse(xhr.responseText);
                    Snapshots.Loaded = true;
                    //console.log("got " + snapshots.length + " versions from osekai snapshots");
                    var container = document.getElementById("snapshots--container");
                    var html = "";
                    for (var i = 0; i < snapshots.length; i++) {
                        var thumbnail = `/snapshots/versions/` + snapshots[i].version_info.version + `/thumbnail.jpg`;
                        html += `<a class="profiles__snapshots-version" style="background-image: url('${thumbnail}')" href="/snapshots?version=${snapshots[i].version_info.id}">
              <h3><strong>${snapshots[i].version_info.name}</strong> ${snapshots[i].version_info.version}</h3>`

                        if (snapshots[i].version_info.upload_date != null) {
                            html += `<p>` + GetStringRawNonAsync("profiles", "profile.snapshots.uploadDate", [TimeAgo.inWords(Date.parse(snapshots[i].version_info.upload_date))]) + `</p>`;
                        } else {
                            html += `<p>` + GetStringRawNonAsync("profiles", "profile.snapshots.uploadDateUnknown") + `</p>`
                        }

                        html += `</a>`;
                    }
                    container.innerHTML = html;
                } else {
                    Snapshots.Loaded = true;
                    //console.log("No snapshots found for user " + user);
                    document.getElementById("snapshots--panel").classList.add("hidden");
                }
            }
        }
        xhr.send();
    }
}
// </snapshots integration>

// <user banner (hubz)>

var UserBanner = {
    CopyUrl: function () {
        copy(document.getElementById("banner-copy-placeholder").innerHTML);
        generatenotification("normal", "Copied Image URL!");
    },
    SwitchUrl: function (type) {
        let host = window.location.origin;
        document.getElementById("banner-toggle-type_bbcode").classList.remove("profiles__userbanner-top-toggle-item-active");
        document.getElementById("banner-toggle-type_raw").classList.remove("profiles__userbanner-top-toggle-item-active");

        if (type == "raw") {
            document.getElementById("banner-toggle-type_raw").classList.add("profiles__userbanner-top-toggle-item-active");
            document.getElementById("banner-copy-placeholder").innerHTML = `${host}/profiles/img/banner.svg?id=${nUserID}`;
        }
        if (type == "bbcode") {
            document.getElementById("banner-toggle-type_bbcode").classList.add("profiles__userbanner-top-toggle-item-active");
            document.getElementById("banner-copy-placeholder").innerHTML = `[url=${host}/profiles?user=${nUserID}][img]${host}/profiles/img/banner.svg?id=${nUserID}[/img][/url]`
        }
    },

    BackgroundStyle: "custom",
    ForegroundStyle: "medal-oriented",
    CustomBackground: {
        Type: "gradient",
        Colour1: [0, 0, 0],
        Colour2: [0, 0, 0],
        Angle: 180
    },
    GetRotation: function (angle) {
        var pi = angle * Math.PI / 180;
        var coords = {
            x1: Math.round(50 + Math.sin(pi) * 50),
            y1: Math.round(50 + Math.cos(pi) * 50),
            x2: Math.round(50 + Math.sin(pi + Math.PI) * 50),
            y2: Math.round(50 + Math.cos(pi + Math.PI) * 50)
        }
        return coords;
    },
    svgString: "",
    PopulateDropdown: function (dropdown, variable, function_ = "null", prefix = "null_") {
        var html = "";
        for (var prop in variable) {
            html += '<div id="' + prefix + prop + '" class="osekai__dropdown-item" onclick="' + function_ + '(\'' + prop + '\', this)">' + variable[prop]["name"] + '</div>';
        }
        dropdown.innerHTML = html;
    },
    OpenDropdowns: [],
    OpenDropdown: function (dropdown) {
        var dropdown = document.getElementById(dropdown);
        for (index = 0; index < UserBanner.OpenDropdowns.length; ++index) {
            if (UserBanner.OpenDropdowns[index] != dropdown) {
                UserBanner.OpenDropdowns[index].classList.add("osekai__dropdown-hidden");
            }
        }
        UserBanner.OpenDropdowns = [dropdown];
        dropdown.classList.toggle("osekai__dropdown-hidden");
    },
    CloseAllDropdowns: function () {
        for (index = 0; index < UserBanner.OpenDropdowns.length; ++index) {
            UserBanner.OpenDropdowns[index].classList.add("osekai__dropdown-hidden");
        }
    },
    SetActiveItemInDropdown: function (id, dropdown) {
        // TODO
    },
    UpdateBackground: function () {
        var img = document.getElementById("banner-image");
        var tmp = UserBanner.svgString;
        var temp_angle = 180;

        if (UserBanner.BackgroundStyle == "custom") {
            tmp = tmp.replace("</svg", "");
            tmp += "<style> * {";
            var col1 = [0, 0, 0];
            var col2 = [0, 0, 0];

            if (UserBanner.CustomBackground.Type == "gradient") {
                col1 = UserBanner.CustomBackground.Colour1;
                col2 = UserBanner.CustomBackground.Colour2;
                temp_angle = UserBanner.CustomBackground.Angle;
            } else if (UserBanner.CustomBackground.Type == "solid") {
                col1 = UserBanner.CustomBackground.Colour1;
                col2 = UserBanner.CustomBackground.Colour1;
            }
            tmp += `--accent1: ${col1[0]}, ${col1[1]}, ${col1[2]};
              --accent2: ${col2[0]}, ${col2[1]}, ${col2[2]};
              --accent3: ${col1[0]}, ${col1[1]}, ${col1[2]};
              --accent4: ${col2[0]}, ${col2[1]}, ${col2[2]};`
            tmp += "}</style></svg>";
            var coords = UserBanner.GetRotation(temp_angle);
            tmp = tmp.replace("{GX1}", coords.x1 + "%");
            tmp = tmp.replace("{GY1}", coords.y1 + "%");
            tmp = tmp.replace("{GX2}", coords.x2 + "%");
            tmp = tmp.replace("{GY2}", coords.y2 + "%");
        }

        var b64 = btoa(tmp);
        img.src = "data:image/svg+xml;base64," + b64;
    },
    CancelCustomBackgroundChanges: function () {
        UserBanner.InitUserBanner();
    },
    SaveSettings: async function (popup = false) {
        var data = new FormData();
        data.append('background', UserBanner.BackgroundStyle);
        data.append('foreground', UserBanner.ForegroundStyle);
        data.append('custom_style', UserBanner.CustomBackground.Type);
        if (UserBanner.BackgroundStyle == "custom") {

            if (UserBanner.CustomBackground.Type == "gradient") {
                data.append('custom_col1', UserBanner.CustomBackground.Colour1);
                data.append('custom_col2', UserBanner.CustomBackground.Colour2);
                data.append('custom_angle', UserBanner.CustomBackground.Angle);
            }
            if (UserBanner.CustomBackground.Type == "solid") {
                data.append('custom_col1', UserBanner.CustomBackground.Colour1);
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/profiles/api/banner/save.php', true);
        xhr.onload = function () {
            // do something to response
            if (popup) {
                generatenotification("normal", "Saved custom banner settings!")
            }
        };
        xhr.send(data);
    },
    FetchNewBanner: function () {
        document.getElementById("banner-loader").classList.remove("hidden");
        if (UserBanner.BackgroundStyle == "custom") {
            var url = "/profiles/img/banner.svg?id=" + nUserID + "&no-angle&norounding";
        } else {
            var url = "/profiles/img/banner.svg?id=" + nUserID + "&norounding";
        }
        var xhr = createXHR(url);
        xhr.onload = function () {
            if (xhr.status == 200) {
                var svg = xhr.responseText;
                UserBanner.svgString = svg;
                UserBanner.UpdateBackground();
                document.getElementById("banner-loader").classList.add("hidden");
            }
            else {
                console.log("error");
            }
        }
        xhr.send();
    },

    CommaSeperatedRGBToArray: function (rcol) {
        var col = rcol.replace(" ", "");
        col = col.split(",");
        var col_t = [parseInt(col[0]), parseInt(col[1]), parseInt(col[2])];
        return col_t;
    },
    SetDropdownText: function (type, name) {
        var dropdown = null;
        var text = "";
        if (type == 'backdrop') {
            dropdown = document.getElementById('dropdown-section-backdrop')
            text = UserBanner.AvailableBackgroundStyles[name].name;
        }
        if (type == 'foreground') {
            dropdown = document.getElementById('dropdown-section-foreground')
            text = UserBanner.AvailableForegroundStyles[name].name;
        }
        if (type == 'customstyle') {
            dropdown = document.getElementById('dropdown-section-customstyle')
            console.log(name);
            text = UserBanner.AvailableCustomBackgroundStyles[name].name;
        }
        console.log("setting " + type + " to " + text)
        console.log(dropdown);
        dropdown.querySelector("#dropdown__themes-text").innerHTML = text;
    },
    cb: null,
    cp: null,
    SetBackgroundStyle: function (prop, el = null, reload = true) {
        UserBanner.BackgroundStyle = prop
        UserBanner.CloseAllDropdowns()
        if (reload == true) {
            UserBanner.SaveSettings().then(() => {
                UserBanner.FetchNewBanner()
            })
        }
        UserBanner.UpdateBackground();
        UserBanner.SetDropdownText('backdrop', prop)
        if (prop == "custom") {
            document.getElementById("custom-background-settings").classList.remove("profiles__userbanner-bottom-bottom-hidden");
        } else {
            document.getElementById("custom-background-settings").classList.add("profiles__userbanner-bottom-bottom-hidden");
            if (reload == false) {
                // we have to reload in this case!! no matter how much you think we don't need to reload we really do have to
                UserBanner.SaveSettings().then(() => {
                    UserBanner.FetchNewBanner()
                })
            }
        }
    },
    SetForegroundStyle: function (prop, el = null, reload = true) {
        UserBanner.ForegroundStyle = prop
        UserBanner.CloseAllDropdowns()
        if (reload == true) {
            UserBanner.SaveSettings().then(() => {
                UserBanner.FetchNewBanner()
            })
        }
        UserBanner.SetDropdownText('foreground', prop)
    },
    SetCustomBackgroundStyle: function (prop, el = null, reload = true, fromloader = false) {
        UserBanner.CustomBackground.Type = prop
        UserBanner.CloseAllDropdowns()
        UserBanner.SetDropdownText('customstyle', prop);
        var gradient_picker = document.getElementById("banner-gradient-picker");
        var solid_picker = document.getElementById("banner-solid-picker");
        var angle_picker = document.getElementById("banner-angle-picker");
        if (prop == "solid") {
            gradient_picker.classList.add("hidden");
            solid_picker.classList.remove("hidden");
            angle_picker.classList.add("hidden");

            UserBanner.CustomBackground.Colour1 = [72, 102, 161];

        } else if (prop == "gradient") {
            gradient_picker.classList.remove("hidden");
            solid_picker.classList.add("hidden");
            angle_picker.classList.remove("hidden");

            UserBanner.CustomBackground.Colour1 = [72, 102, 161];
            UserBanner.CustomBackground.Colour2 = [20, 37, 71];

        }

        cp.setColour(UserBanner.CustomBackground.Colour1);
        cb.setColour(UserBanner.CustomBackground.Colour1, UserBanner.CustomBackground.Colour2);

        UserBanner.UpdateBackground();
    },
    UpdateAngle: function (from) {
        var value = 0;
        var slider = document.getElementById("angle-slider");
        var input = document.getElementById("angle-input");
        if (from == "slider") {
            value = slider.value;
            input.value = value;
        }
        if (from == "input") {
            value = input.value;
            slider.value = value;
        }
        UserBanner.CustomBackground.Angle = value;
        UserBanner.UpdateBackground();
    },
    UpdateAngleSlider: function () {
        document.getElementById("angle-input").value = UserBanner.CustomBackground.Angle;
        document.getElementById("angle-slider").value = UserBanner.CustomBackground.Angle;
    },
    InitUserBanner: function () {
        UserBanner.AvailableBackgroundStyles = {
            "custom": {
                "name": GetStringRawNonAsync("profiles", "profile.banner.backdropStyle.custom")
            },
            "clubglows": {
                "name": GetStringRawNonAsync("profiles", "profile.banner.backdropStyle.clubGlows")
            }
        }
        UserBanner.AvailableForegroundStyles = {
            "medal-oriented": {
                "name": GetStringRawNonAsync("profiles", "profile.banner.foregroundStyle.medalOriented")
            }
        }
        UserBanner.AvailableCustomBackgroundStyles = {
            "gradient": {
                "name": GetStringRawNonAsync("profiles", "profile.banner.customBackground.style.gradient")
            },
            "solid": {
                "name": GetStringRawNonAsync("profiles", "profile.banner.customBackground.style.solid")
            }
        }

        UserBanner.PopulateDropdown(document.getElementById("banner-dropdown-background-style"), UserBanner.AvailableBackgroundStyles, "UserBanner.SetBackgroundStyle", "banner-dropdown-background-style-");
        UserBanner.PopulateDropdown(document.getElementById("banner-dropdown-foreground-style"), UserBanner.AvailableForegroundStyles, "UserBanner.SetForegroundStyle", "banner-dropdown-foreground-style-");
        UserBanner.PopulateDropdown(document.getElementById("banner-dropdown-custom-style"), UserBanner.AvailableCustomBackgroundStyles, "UserBanner.SetCustomBackgroundStyle", "banner-dropdown-custom-style-");

        var url = "/profiles/api/banner/get.php";
        var xhr = createXHR(url)

        cb = new newColourBar("colourbar", function (col1, col2) {
            UserBanner.CustomBackground.Colour1 = col1;
            UserBanner.CustomBackground.Colour2 = col2;
            UserBanner.UpdateBackground();
        }, UserBanner.CustomBackground.Colour1, UserBanner.CustomBackground.Colour2);

        cp = new newColourPicker("colour-solid-picker", function (col) {
            UserBanner.CustomBackground.Colour1 = col;
            UserBanner.UpdateBackground();
        }, UserBanner.CustomBackground.Colour2);

        xhr.onload = function () {
            if (xhr.status == 200) {
                var json = JSON.parse(xhr.responseText);
                UserBanner.SetBackgroundStyle(json["Background"], null, false);
                UserBanner.SetForegroundStyle(json["Foreground"], null, false);
                if (json["Background"] == "custom") {
                    UserBanner.SetCustomBackgroundStyle(json["CustomStyle"], null, false, true);
                    if (json["CustomGradient"] != "" && json["CustomStyle"] == "gradient") {
                        var customGradient = JSON.parse(json['CustomGradient']);
                        // "r,g,b", "r,g,b", angle
                        if (customGradient != null) {
                            UserBanner.CustomBackground.Angle = customGradient[2];
                            UserBanner.CustomBackground.Colour1 = UserBanner.CommaSeperatedRGBToArray(customGradient[0]);
                            UserBanner.CustomBackground.Colour2 = UserBanner.CommaSeperatedRGBToArray(customGradient[1]);
                            cb.setColour(UserBanner.CustomBackground.Colour1, UserBanner.CustomBackground.Colour2);
                            UserBanner.UpdateAngleSlider();
                        }
                    }
                    if (json["CustomStyle"] == "solid") {
                        if (json['CustomSolid'] != null) {
                            UserBanner.CustomBackground.Colour1 = UserBanner.CommaSeperatedRGBToArray(json["CustomSolid"]);
                        } else {
                            UserBanner.CustomBackground.Colour1 = [72, 102, 161];
                        }
                        cp.setColour(UserBanner.CustomBackground.Colour1);
                    }
                }
                UserBanner.FetchNewBanner();
                UserBanner.UpdateBackground();
                document.getElementById("banner-full-loader").classList.add("hidden");
            }
        }
        xhr.send("userid=" + nUserID);
        UserBanner.SwitchUrl("bbcode");
    }
}

// </user banner>