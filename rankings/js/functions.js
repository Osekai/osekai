// constants
const APP_MAIN = "Osekai Rankings / ";
const APP_SHORT = "rankings";
const ENTRIES_PER_PAGE = 50;
const MAX_PAGES = 7;
const API_URL = "/rankings/api/api.php";

// variables
var colParentApps = [];
var currentApp;
var bInitialized;

// constructors
function parentApp(name, langdefault) {
    this.name = name;
    this.langdefault = langdefault;
    this.path = APP_MAIN + this.name + " /";
    this.children = [];
    colParentApps.push(this);

    this.Initialize = function () {
        this.SetNavigation();
    }

    this.AddChild = function (name, filters, langdefault) {
        let oApp = new app(name, filters, this, langdefault);
        this.children.push(oApp);
        return oApp;
    }

    this.SetNavigation = function () {
        document.querySelectorAll("[selector='apps']").forEach((oNav) => {
            oNav.innerHTML = "";
            colParentApps.forEach((oChildApp) => {
                let oOption = document.createElement("div");
                oOption.classList.add("rankings__option");
                oOption.innerHTML = GetStringRawNonAsync(APP_SHORT, oChildApp.langdefault);
                if (oChildApp.name == this.name) oOption.classList.add("rankings__active");
                oOption.addEventListener("click", () => {
                    currentApp.paginator.RemoveListeners();
                    document.querySelectorAll("[selector='dropdown__filters']").forEach((oFilter) => oFilter.removeEventListener("click", currentApp.FilterClickEvent));
                    document.querySelectorAll("[selector='search__input']").forEach((oInput) => oInput.removeEventListener("keydown", currentApp.FilterSearchEvent));
                    document.querySelectorAll(".osekai__replace__loader").forEach(oLoader => oLoader.remove());
                    oChildApp.children[0].Initialize();
                })
                oNav.appendChild(oOption);
            });
        });
    }
}

function app(name, filters, parent, langdefault) {
    this.name = name;
    this.langdefault = langdefault;
    this.filters = filters;
    this.parent = parent;
    this.resultset = [];
    this.dbhandler = new dbhandler(this);
    this.paginator = new paginator(this);
    this.activeEvents = [];

    this.Initialize = async function () {
        await loadSource("rankings");
        this.setCurrentApp();
        this.parent.Initialize();
        this.Cleanup();
        this.SetState();
        this.SetBreadCrumb();
        this.SetNavigation();
        this.dbhandler.AskDB();
    }

    this.setCurrentApp = function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        RemoveHome();
        currentApp = this;
    }

    this.ContinueInitialization = function () {
        this.paginator.InitPagination();
        this.SetFilters();
        this.SetSearch();
        this.dbhandler.CreateRanking(0);
        if (!bInitialized) bInitialized = true;
    }

    this.Cleanup = function () {
        document.querySelectorAll("[selector='rankings__main']").forEach(oRanking => oRanking.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>");
        document.querySelectorAll("[selector='filter__items']").forEach((colItems) => {
            if (!colItems.classList.contains("osekai__dropdown-hidden")) colItems.classList.add("osekai__dropdown-hidden");
        });
        document.querySelectorAll("[selector='search__input']").forEach((oInput) => {
            oInput.value = "";
        });
        if (this.dbhandler.filterValue !== "") {
            this.dbhandler.filterValue = "";
            this.resultset = [];
        }
    }

    this.SetState = function () {
        let params = new URLSearchParams(window.location.search);
        if (params.get("type") == this.name) return;
        params.set("ranking", this.parent.name);
        params.set("type", this.name);
        window.history.pushState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    }

    this.SetBreadCrumb = function () {
        document.querySelectorAll("[selector='breadcrumb']").forEach((oCrumb) => oCrumb.innerHTML = this.parent.path);
        document.querySelectorAll("[selector='subcrumb']").forEach((oCrumb) => oCrumb.innerHTML = GetStringRawNonAsync(APP_SHORT, this.langdefault));
    }

    this.SetNavigation = function () {
        document.querySelectorAll("[selector='typecrumb']").forEach((oNav) => {
            oNav.innerHTML = "";
            this.parent.children.forEach((oChildApp) => {
                let oOption = document.createElement("div");
                oOption.classList.add("rankings__option");
                oOption.innerHTML = GetStringRawNonAsync(APP_SHORT, oChildApp.langdefault);
                if (oChildApp.name == this.name) oOption.classList.add("rankings__active");
                oOption.addEventListener("click", () => {
                    this.paginator.RemoveListeners();
                    document.querySelectorAll("[selector='dropdown__filters']").forEach((oFilter) => oFilter.removeEventListener("click", this.FilterClickEvent));
                    document.querySelectorAll("[selector='search__input']").forEach((oInput) => {
                        oInput.removeEventListener("keydown", this.FilterSearchEvent)
                        oInput.value = "";
                    });
                    document.querySelectorAll(".osekai__replace__loader").forEach(oLoader => oLoader.remove());
                    oChildApp.Initialize();
                })
                oNav.appendChild(oOption);
            });
        });
    }

    this.SetFilters = function () {
        document.querySelectorAll("[selector='filter__items']").forEach((colItems) => {
            colItems.innerHTML = "";
            document.querySelectorAll("[selector='filter__activeItem']").forEach((oSelectedFilter) => {
                oSelectedFilter.innerHTML = this.filters[0];
            });

            this.filters.forEach(strFilter => {
                let oFilter = document.createElement("div");
                oFilter.classList.add("osekai__dropdown-item");
                oFilter.innerHTML = strFilter;
                colItems.appendChild(oFilter);
            })

            document.querySelectorAll("[selector='dropdown__filters']").forEach((oFilter) => oFilter.addEventListener("click", this.FilterClickEvent))
            document.querySelectorAll("[selector='search__input']").forEach((oInput) => oInput.addEventListener("keyup", this.FilterSearchEvent));
        });
    }

    this.SetSearch = function () {
        let oSearchDiv = document.querySelector(".osekai__panel-header-input__sizer");
        oSearchDiv.querySelector(":scope > input").addEventListener("input", function () { this.parentNode.dataset.value = this.value });
    }

    this.FilterClickEvent = function () {
        document.querySelectorAll("[selector='filter__items']").forEach((colItems) => {
            colItems.classList.toggle("osekai__dropdown-hidden");
            colItems.querySelectorAll(":scope > div").forEach(oOption => {
                oOption.classList.remove("osekai__dropdown-item-active");
                document.querySelectorAll("[selector='filter__activeItem']").forEach((oSelectedFilter) => {
                    if (oOption.innerHTML.replace(/\s+/g, '') == oSelectedFilter.innerHTML.replace(/\s+/g, '')) oOption.classList.add("osekai__dropdown-item-active");
                    oOption.addEventListener("click", () => {
                        oSelectedFilter.innerHTML = oOption.innerHTML;
                        currentApp.dbhandler.filterCondition = oOption.innerHTML.toLowerCase().replace(" ", "");
                        currentApp.dbhandler.Filter();
                    });
                });
            });
        });
    }

    this.FilterSearchEvent = function () {
        let oValue = this.value;
        if (currentApp.dbhandler.filterCondition == "") currentApp.dbhandler.filterCondition = document.querySelectorAll("[selector='filter__activeItem']")[0].innerHTML.toLowerCase().replace(" ", "");
        if (currentApp.dbhandler.filterCondition == "country") {
            if (oValue.length == 2) oValue = (getCountryNameByShortCode(oValue.toUpperCase()));
            if (oValue.length == 3) oValue = (getCountryNameByLongCode(oValue.toUpperCase()));
        }
        currentApp.dbhandler.filterValue = oValue;
        currentApp.dbhandler.Filter();
    }
}

function paginator(app) {
    this.app = app;
    let nCurrentPage = 1;
    let nPages;

    this.InitPagination = function () {
        this.RefreshPagination();
        this.Navigate(1);

        document.querySelectorAll("[selector='button__next__page']").forEach((oNext) => oNext.addEventListener("click", this.NavigateNext));
        document.querySelectorAll("[selector='button__prev__page']").forEach((oPrev) => oPrev.addEventListener("click", this.NavigatePrev));
    }

    this.RemoveListeners = function () {
        document.querySelectorAll("[selector='button__next__page']").forEach((oNext) => oNext.removeEventListener("click", this.NavigateNext));
        document.querySelectorAll("[selector='button__prev__page']").forEach((oPrev) => oPrev.removeEventListener("click", this.NavigatePrev));
    }

    this.RefreshPagination = function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        nPages = Math.ceil(this.app.resultset.filter((oResult) => !oResult['hidden']).length / ENTRIES_PER_PAGE);
        let oNext = document.getElementById("button__next__page");
        let nLookAhead = Math.floor(MAX_PAGES / 2);
        document.querySelectorAll("[id^='page__']").forEach(oPage => oPage.remove());
        for (let nCount = 1; nCount <= nPages; nCount++) {
            if ((nCurrentPage + nLookAhead >= nCount) && (nCurrentPage - nLookAhead <= nCount) || (nLookAhead + nCurrentPage > nPages && nCount + MAX_PAGES > nPages) || (nCurrentPage - nLookAhead <= 0 && nCount - MAX_PAGES <= 0)) {
                let oPage = document.createElement("p");
                oPage.classList.add("osekai__pagination_item");
                oPage.innerHTML = nCount;
                oPage.id = "page__" + nCount;
                oPage.addEventListener("click", () => this.Navigate(nCount));
                oNext.parentNode.insertBefore(oPage, oNext);
                let oMobilePage = oPage.cloneNode(true);
                oMobilePage.id = oMobilePage.id + "__mobile";
                oMobilePage.addEventListener("click", () => this.Navigate(nCount));
                document.querySelector("[selector='pagelist']").appendChild(oMobilePage);
            }
        }
    }

    this.Navigate = function (page) {
        nCurrentPage = page;
        this.RefreshPagination();
        document.querySelectorAll(".osekai__pagination_item-active").forEach(oSelected => oSelected.classList.remove("osekai__pagination_item-active"));
        if (document.getElementById("page__" + page)) document.getElementById("page__" + page).classList.add("osekai__pagination_item-active");
        if (document.getElementById("page__" + page + "__mobile")) document.getElementById("page__" + page + "__mobile").classList.add("osekai__pagination_item-active");
        this.app.dbhandler.CreateRanking((page - 1) * 50);
    }

    this.NavigateNext = function () {
        if (nCurrentPage < nPages) currentApp.paginator.Navigate(nCurrentPage + 1);
    }

    this.NavigatePrev = function () {
        if (nCurrentPage > 1) currentApp.paginator.Navigate(nCurrentPage - 1);
    }
}

function dbhandler(app) {
    this.app = app;
    this.creator = new elementCreator(this);
    this.failed = false;
    this.filterCondition = "";
    this.filterValue = "";

    this.FillData = function (oResponse) {
        Object.keys(oResponse).forEach(function (obj) {
            app.resultset.push(oResponse[obj]);
        });
        currentApp.ContinueInitialization();
    }

    this.CreateRanking = function (nFirstRank) {
        let nLayout = 0;
        document.querySelectorAll("[selector='rankings__main']").forEach(oRanking => {
            nLayout += 1;
            oRanking.innerHTML = "";
            let oRelevantResults = app.resultset.filter((oResult) => !oResult['hidden']);
            for (i = nFirstRank; i < nFirstRank + ENTRIES_PER_PAGE; i++) {
                if (this.failed || typeof (oRelevantResults[i]) === 'undefined') return;
                switch (this.app.name) {
                    case "Users": oRanking.appendChild(this.creator.CreateMedalUsers(oRelevantResults[i], oRanking)); break;
                    case "Rarity":
                        if (!document.getElementById(`Warning_Inaccuracy_${nLayout}`)) oRanking.appendChild(this.creator.CreateInaccuracyWarning("Due to the amount of players being processed in our database, the following data can be inaccurate.", nLayout));
                        oRanking.appendChild(this.creator.CreateMedalRarity(oRelevantResults[i], oRanking));
                        break;
                    case "Standard Deviation": oRanking.appendChild(this.creator.CreateStandardDeviation(oRelevantResults[i], oRanking)); break;
                    case "Total pp": oRanking.appendChild(this.creator.CreateTPP(oRelevantResults[i], oRanking)); break;
                    case "Total Level": oRanking.appendChild(this.creator.CreateTotalLevel(oRelevantResults[i], oRanking)); break;
                    case "Stdev Level": oRanking.appendChild(this.creator.CreateStdevLevel(oRelevantResults[i], oRanking)); break;
                    case "Total Accuracy": oRanking.appendChild(this.creator.CreateTotalAcc(oRelevantResults[i], oRanking)); break;
                    case "Stdev Accuracy": oRanking.appendChild(this.creator.CreateStdevAcc(oRelevantResults[i], oRanking)); break;
                    case "Replays": oRanking.appendChild(this.creator.CreateReplays(oRelevantResults[i], oRanking)); break;
                    case "Ranked Mapsets": oRanking.appendChild(this.creator.CreateRankedMaps(oRelevantResults[i], oRanking)); break;
                    case "Loved Mapsets": oRanking.appendChild(this.creator.CreateLovedMaps(oRelevantResults[i], oRanking)); break;
                    case "Subscribers": oRanking.appendChild(this.creator.CreateSubscribers(oRelevantResults[i], oRanking)); break;
                    case "Kudosu": oRanking.appendChild(this.creator.CreateKudosu(oRelevantResults[i], oRanking)); break;
                    case "Badges": oRanking.appendChild(this.creator.CreateBadges(oRelevantResults[i], oRanking)); break;
                }
                this.creator.addTopBarEventListeners();
            }
            document.querySelectorAll(".osekai__replace__loader").forEach(oLoader => oLoader.remove());
        });
        this.creator.addCountryImgEventListeners();
    }

    this.AskDB = function () {
        if (app.resultset.length > 0) {
            currentApp.ContinueInitialization();
        } else {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", API_URL, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.send("App=" + this.app.name);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var oResponse = JSON.parse(xhr.responseText);
                    if (oResponse !== undefined && oResponse !== null) currentApp.dbhandler.FillData(oResponse);
                };
            };
        }
    }

    this.Filter = function () {
        this.app.resultset.forEach(oResult => {
            if (!oResult[this.filterCondition].toLowerCase().includes(this.filterValue.toLowerCase())) {
                oResult['hidden'] = true;
            } else {
                oResult['hidden'] = false;
            }
        })
        this.app.paginator.Navigate(1);
    }

    this.Fail = function () {
        console.log("Database has failed | Restarting");
        this.failed = true;
        document.querySelectorAll("[selector='typecrumb']").forEach((oNav) => oNav.innerHTML = "");
        document.querySelectorAll("[selector='apps']").forEach((oNav) => oNav.innerHTML = "");
        setTimeout(function () {
            console.log("Database has failed | Commencing");
            currentApp.dbhandler.failed = false;
            currentApp.resultset = [];
            currentApp.Initialize();
        }, 3000);
    }
}

function elementCreator(dbhandler) {
    this.dbhandler = dbhandler;

    this.CreateInaccuracyWarning = function (strWarning, nLayout) {
        console.log(strWarning + nLayout);
        let oDivWarning = document.createElement("div");
        oDivWarning.classList.add("osekai__generic-warning");
        oDivWarning.id = `Warning_Inaccuracy_${nLayout}`;

        let iWarning = document.createElement("i");
        iWarning.classList.add("fas");
        iWarning.classList.add("fa-exclamation-triangle");

        let pWarning = document.createElement("p");
        pWarning.innerHTML = strWarning;

        oDivWarning.appendChild(iWarning);
        oDivWarning.appendChild(pWarning);
        return oDivWarning;
    }

    this.CreateMedalUsers = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("completion")) this.dbhandler.Fail();
        let oWrapper = this.CreateMedalUserWrapper(oEntry, oRanking);

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.medalCount, "medals"));
            let oBottomBar = this.createMedalUsersBottomBar(oEntry);

            oWrapper = this.appendChildren(oWrapper, [oTopBar, oBottomBar]);

            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainMedalUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oRarity = this.CreateUserRarestMedalContent(oEntry);
            let oRarityCascade = this.CreateCascade(false, "400px", oRarity, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oRarityCascade, this.CreateProgressBar(oEntry)]);
            return oWrapper;
        }
    }

    this.CreateMedalRarity = function (oEntry, oRanking) {
        if (oEntry.hasOwnProperty("username")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileMedalTopBar(oEntry);
            let oBottomBar = this.createMobileMedalBottomBar(oEntry);

            oWrapper = this.appendChildren(oWrapper, [oTopBar, oBottomBar]);

            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainMedal = this.CreateMedalContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "295px", oMainMedal, oRankCascade);

            let oDescription = this.createParagraph(oEntry.description);
            let oDescriptionCascade = this.CreateCascade(false, "32vw", oDescription, oMainCascade);

            let oPosession = this.CreateMedalPossession(oEntry);
            let oPosessionCascade = this.CreateCascade(false, "180px", oPosession, oDescriptionCascade);

            let oMode = this.createMedalMode(oEntry);
            let oModeCascade = this.CreateCascade(false, "auto", oMode, oPosessionCascade, true);

            oWrapper.appendChild(oModeCascade);
            return oWrapper;
        }
    }

    this.CreateStandardDeviation = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("spp")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.spp, "spp"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true)]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oWrapper = this.CreateDefaultWrapper();

            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.spp, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviation.standardDeviationPP"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry)]);
            
            return oWrapper;
        }
    }

    this.CreateStdevLevel = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("slevel")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.slevel, "slevel"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true, "level")]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oWrapper = this.CreateDefaultWrapper();

            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.slevel, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviatedLevel.standardDeviatedLevel"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry, false, "level")]);
            
            return oWrapper;
        }
    }
    this.CreateTotalLevel = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("tlevel")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.tlevel, "tlevel"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true, "level")]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oWrapper = this.CreateDefaultWrapper();

            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.tlevel, GetStringRawNonAsync(APP_SHORT, "bar.allmode.totalLevel.totalLevel"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry, false, "level")]);
            
            return oWrapper;
        }
    }

    this.CreateStdevAcc = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("sacc")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.sacc, "sacc"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true, "acc")]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oWrapper = this.CreateDefaultWrapper();

            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.sacc, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviatedAccuracy.standardDeviatedAccuracy"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry, false, "acc")]);
            
            return oWrapper;
        }
    }
    this.CreateTotalAcc = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("tacc")) this.dbhandler.Fail();
        console.log(oEntry);
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.tacc, "tacc"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true, "acc")]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oWrapper = this.CreateDefaultWrapper();

            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.tacc, GetStringRawNonAsync(APP_SHORT, "bar.allmode.totalAccuracy.totalAccuracy"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry, false, "acc")]);
            
            return oWrapper;
        }
    }

    this.CreateTPP = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("tpp")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.tpp, "tpp"), true);
            oWrapper = this.appendChildren(oWrapper, [oTopBar, this.createSplitBar(oEntry, true)]);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "370px", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.tpp, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviation.totalPP"));
            let oAmountCascade = this.CreateCascade(false, "250px", oAmount, oMainCascade);

            oWrapper = this.appendChildren(oWrapper, [oAmountCascade, this.createSplitBar(oEntry)]);

            return oWrapper;
        }
    }

    this.CreateReplays = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("replays")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.replays, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviation.replaysWatched")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.replays, GetStringRawNonAsync(APP_SHORT, "bar.allmode.standardDeviation.replaysWatched"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateRankedMaps = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("ranked")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.ranked, GetStringRawNonAsync(APP_SHORT, "bar.mappers.ranked.rankedMaps")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.ranked, GetStringRawNonAsync(APP_SHORT, "bar.mappers.ranked.rankedMaps"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateLovedMaps = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("loved")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.loved, GetStringRawNonAsync(APP_SHORT, "bar.mappers.loved.lovedMaps")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.loved, GetStringRawNonAsync(APP_SHORT, "bar.mappers.loved.lovedMaps"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateSubscribers = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("subscribers")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.subscribers, GetStringRawNonAsync(APP_SHORT, "bar.mappers.subscribers.subscribers")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.subscribers, GetStringRawNonAsync(APP_SHORT, "bar.mappers.subscribers.subscribers"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateKudosu = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("kudosu")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");
        console.log(oEntry);

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.kudosu, GetStringRawNonAsync(APP_SHORT, "bar.mappers.kudosu.kudosu")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.kudosu, GetStringRawNonAsync(APP_SHORT, "bar.mappers.kudosu.kudosu"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateBadges = function (oEntry, oRanking) {
        if (!oEntry.hasOwnProperty("badges")) this.dbhandler.Fail();
        let oWrapper = this.CreateDefaultWrapper(oRanking.parentNode.parentNode.parentNode.id == "mobile");

        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            let oTopBar = this.createMobileTopBar(oEntry, this.createMobileCount(oEntry.badges, GetStringRawNonAsync(APP_SHORT, "bar.badges.badges.badges")), false);
            oWrapper.appendChild(oTopBar);
            let oMobileWrapper = this.createMobileWrapper(oWrapper);
            return oMobileWrapper;
        } else {
            let oRank = this.CreateRankContent(oEntry);
            let oRankCascade = this.CreateCascade(true, "60px", oRank);

            let oMainUser = this.CreateMainUsersContent(oEntry);
            let oMainCascade = this.CreateCascade(false, "40vw", oMainUser, oRankCascade);

            let oAmount = this.createAmount(oEntry.badges, GetStringRawNonAsync(APP_SHORT, "bar.badges.badges.badges"));
            let oAmountCascade = this.CreateCascade(false, "auto", oAmount, oMainCascade, true);

            oWrapper.appendChild(oAmountCascade);
            return oWrapper;
        }
    }

    this.CreateCascade = function (bIsCentered, nWidth, oInnerNode, oInnerCascade = null, bNoBackground = false) {
        let oCascade = document.createElement("div");
        oCascade.classList.add("rankings__cascade");
        let oContent = document.createElement("div");
        oContent.classList.add("rankings__cascade__content");
        if (bIsCentered) oContent.classList.add("centered");
        if (bNoBackground) oCascade.classList.add("rankings__cascade-nobg");
        oContent.style.setProperty("width", nWidth);
        if (oInnerCascade !== null) oCascade.appendChild(oInnerCascade);
        oContent.appendChild(oInnerNode);
        oCascade.appendChild(oContent);
        return oCascade;
    };

    this.CreateRankContent = function (oEntry) {
        let oWrapper = document.createElement("div");
        let oHashtag = document.createElement("span");
        let oNumber = this.createSpan(true, oEntry.rank);

        oHashtag.classList.add("light");
        oHashtag.innerHTML = "# ";

        oWrapper = this.appendChildren(oWrapper, [oHashtag, oNumber]);
        return oWrapper;
    }

    this.CreateMainMedalUsersContent = function (oEntry) {
        let oParagraph = document.createElement("p");
        let oSpanName = this.createSpan(true, oEntry.username);
        let oSpanDivider = document.createElement("span");
        let oSpanAmount = this.createSpan(true, oEntry.medalCount);
        let oATag = document.createElement("a");

        oATag.appendChild(oSpanName);
        oATag.href = "/profiles/?user=" + oEntry.userid;

        this.addClasslist(oSpanDivider, ['transparent', 'light']);
        oSpanDivider.innerHTML = ` ${GetStringRawNonAsync(APP_SHORT, "bar.general.with")} `;

        oParagraph = this.appendChildren(oParagraph, [this.createCountrySpan(oEntry, true), oATag, oSpanDivider, oSpanAmount]);
        oParagraph.innerHTML += " medals";
        return oParagraph;
    }

    this.CreateMainUsersContent = function (oEntry) {
        let oParagraph = document.createElement("p");
        let oATag = document.createElement("a");
        let oSpanName = this.createSpan(true, oEntry.username);

        oATag.appendChild(oSpanName);
        oATag.href = "/profiles/?user=" + oEntry.userid;

        oParagraph = this.appendChildren(oParagraph, [this.createCountrySpan(oEntry, true), oATag]);
        return oParagraph;
    }

    this.CreateUserRarestMedalContent = function (oEntry) {
        let oLink = document.createElement("a");
        let oParagraph = document.createElement("p");
        let oSpanTitle = document.createElement("span");
        let oSpanImg = document.createElement("span");
        let oSpanName = this.createSpan(true, oEntry.rarestmedal);
        let oImg = this.createImg(oEntry.link);

        oSpanImg.classList.add("rankings__inline-medal");
        oSpanImg.appendChild(oImg);

        //oSpanName.classList.add("medal_hover");
        //oSpanName.setAttribute("medalname", oEntry.rarestmedal);

        //oLink.setAttribute("href", "/medals?medal=" + oEntry.rarestmedal);
        oLink.classList.add("pointer");
        oLink.addEventListener("click", () => medalPopupV2.showMedalFromName(oEntry.rarestmedal));
        oLink.appendChild(oSpanName);

        this.addClasslist(oSpanTitle, ['transparent', 'light']);
        oSpanTitle.innerHTML = GetStringRawNonAsync(APP_SHORT, "bar.medals.users.rarestMedal");

        oParagraph = this.appendChildren(oParagraph, [oSpanTitle, oSpanImg, oLink]);
        return oParagraph;
    }

    this.CreateProgressBar = function (oEntry) {
        let oWrapper = document.createElement("div");
        let oBarName = document.createElement("div");
        let oBarMain = document.createElement("div");
        let oBarFilled = document.createElement("div");
        let oParagraphCompletion = document.createElement("p");
        let oInnerBar = document.createElement("div");
        let oParagraphFilled = document.createElement("p");
        let oSpanFilled = this.createSpan(true, oEntry.completion + "%")

        oParagraphCompletion.innerHTML = GetStringRawNonAsync(APP_SHORT, "bar.medals.users.medalCompletion");
        oBarName.classList.add("rankings__pb-name");
        oBarName.appendChild(oParagraphCompletion);

        oInnerBar.classList.add("rankings__pb-innerbar");
        oInnerBar.style.setProperty("width", oEntry.completion + "%");
        oBarMain.classList.add("rankings__pb-bar");
        oBarMain.appendChild(oInnerBar);

        oParagraphFilled.appendChild(oSpanFilled);
        oBarFilled.classList.add("rankings__pb-percentage");
        oBarFilled.appendChild(oParagraphFilled);

        oWrapper.classList.add("rankings__progress-bar");
        oWrapper = this.appendChildren(oWrapper, [oBarName, oBarMain, oBarFilled]);
        return oWrapper;
    }

    this.CreateMedalUserWrapper = function (oEntry, oRanking) {
        let oWrapper = document.createElement("div");
        if (oRanking.parentNode.parentNode.parentNode.id == "mobile") {
            oWrapper.classList.add("rankings__mobile__bar");
        } else {
            oWrapper.classList.add("rankings__bar");
        }
        if (oEntry.completion >= 95) {
            oWrapper.classList.add("col95club");
        } else if (oEntry.completion >= 90) {
            oWrapper.classList.add("col90club");
        } else if (oEntry.completion >= 80) {
            oWrapper.classList.add("col80club");
        } else if (oEntry.completion >= 60) {
            oWrapper.classList.add("col60club");
        } else if (oEntry.completion >= 40) {
            oWrapper.classList.add("col40club");
        } else {
            oWrapper.classList.add("colnoclub");
        }
        return oWrapper;
    }

    this.CreateDefaultWrapper = function (bMobile = false) {
        let oWrapper = document.createElement("div");
        if (bMobile) {
            oWrapper.classList.add("rankings__mobile__bar");
        } else {
            oWrapper.classList.add("rankings__bar");
        }
        return oWrapper;
    }

    this.CreateMedalContent = function (oEntry) {
        let oLink = document.createElement("a");
        let oParagraph = document.createElement("p");
        let oSpanMedalImg = document.createElement("span");
        let oSpanName = this.createSpan(true, oEntry.medalname);
        let oImg = this.createImg(oEntry.link);

        oSpanMedalImg.classList.add("rankings__inline-medal");
        oSpanMedalImg.appendChild(oImg);

        // oSpanName.classList.add("medal_hover");
        // oSpanName.setAttribute("medalname", oEntry.medalname);

        // oLink.setAttribute("href", "/medals?medal=" + oEntry.medalname);
        oLink.classList.add("pointer");
        oLink.addEventListener("click", () => medalPopupV2.showMedalFromName(oEntry.medalname));
        oLink.appendChild(oSpanName);

        oParagraph = this.appendChildren(oParagraph, [oSpanMedalImg, oLink]);
        return oParagraph;
    }

    this.CreateMedalPossession = function (oEntry) {
        let oPosessionSpan = this.createSpan(true, oEntry.possessionRate);
        let oSpanPreword = this.createSpan(false, GetStringRawNonAsync(APP_SHORT, "bar.medals.rarity.heldBy") + " ");
        let oParagraph = document.createElement("p");

        oParagraph = this.appendChildren(oParagraph, [oSpanPreword, oPosessionSpan]);
        oParagraph.innerHTML += "%";
        return oParagraph
    }

    this.createMedalMode = function (oEntry) {
        if (oEntry.gameMode == "NULL") return document.createElement("span");
        let oImg = this.createImg("/global/img/gamemodes/" + oEntry.gameMode + ".svg");
        let oSpanMode = document.createElement("span");
        let oSpanModeName = this.createSpan(true, oEntry.gameMode);
        let oParagraph = document.createElement("p");

        oSpanMode.classList.add("rankings__inline-medal");
        oSpanMode.appendChild(oImg);

        oParagraph = this.appendChildren(oParagraph, [oSpanMode, oSpanModeName]);
        oParagraph.innerHTML += " " + GetStringRawNonAsync(APP_SHORT, "bar.medals.rarity.only") + " ";

        return oParagraph;
    }

    this.createSplitBar = function (oEntry, bMobile = false, prefix = "pp") {
        let oDivBar = document.createElement("div");
        oDivBar.classList.add("rankings__progress-bar");
        
        if (bMobile) {
            oDivBar.classList.add("hidden");
            oDivBar.classList.add("rankings__mobile__bottom-content");
        }

        let oSegmentedBar = document.createElement("div");
        oSegmentedBar.classList.add("osekai__gamemode-pp-progress-bar");

        let oSegmentOsu = this.createSegment("standard");
        oSegmentOsu.style.setProperty("--width", (parseInt(oEntry["osu" + prefix]) / parseInt(oEntry["t" + prefix]) * 100) + "%");
        oSegmentOsu.setAttribute("tooltip-content", oEntry["osu" + prefix].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " standard " + prefix);

        let oSegmentTaiko = this.createSegment("taiko");
        oSegmentTaiko.style.setProperty("--width", (parseInt(oEntry["taiko" + prefix]) / parseInt(oEntry["t" + prefix]) * 100) + "%");
        oSegmentTaiko.setAttribute("tooltip-content", oEntry["taiko" + prefix].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " taiko " + prefix);

        let oSegmentCatch = this.createSegment("catch");
        oSegmentCatch.style.setProperty("--width",  (parseInt(oEntry["catch" + prefix]) / parseInt(oEntry["t" + prefix]) * 100) + "%");
        oSegmentCatch.setAttribute("tooltip-content", oEntry["catch" + prefix].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " catch " + prefix);

        let oSegmentMania = this.createSegment("mania");
        oSegmentMania.style.setProperty("--width",  (parseInt(oEntry["mania" + prefix]) / parseInt(oEntry["t" + prefix]) * 100) + "%");
        oSegmentMania.setAttribute("tooltip-content", oEntry["mania" + prefix].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " mania " + prefix);

        if(parseInt(oEntry["osu" + prefix]) > 0) oSegmentedBar.appendChild(oSegmentOsu);
        if(parseInt(oEntry["taiko" + prefix]) > 0) oSegmentedBar.appendChild(oSegmentTaiko);
        if(parseInt(oEntry["catch" + prefix]) > 0) oSegmentedBar.appendChild(oSegmentCatch);
        if(parseInt(oEntry["mania" + prefix]) > 0) oSegmentedBar.appendChild(oSegmentMania);

        oDivBar.appendChild(oSegmentedBar);
        return oDivBar;
    }

    this.createSegment = function (oMode) {
        let oSegment = document.createElement("div");
        oSegment.classList.add("osekai__gamemode-pp-progress-bar-segment");
        oSegment.classList.add("tooltip-v2");
        oSegment.classList.add(oMode);
        return oSegment;
    }

    this.createAmount = function (Amount, Name) {
        let oAmount = this.createSpan(true, Amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        let oSpanDivider = this.createSpan(false, " " + Name);
        let oParagraph = document.createElement("p");

        oSpanDivider.classList.add("transparent");
        oParagraph = this.appendChildren(oParagraph, [oAmount, oSpanDivider]);

        return oParagraph;
    }

    this.createMobileWrapper = function (oInner) {
        let oWrapper = document.createElement("div");
        oWrapper.classList.add("rankings__mobile-area");
        oWrapper.appendChild(oInner);
        return oWrapper;
    }

    this.createMobileTopBar = function (oEntry, oRightContent, bContainsBottomBar = true) {
        let oTopBar = document.createElement("div");
        oTopBar.classList.add("rankings__mobile__top-bar");

        let oRank = this.createParagraph("#" + oEntry.rank);
        let oPlayer = this.createParagraph(oEntry.username);

        let oDropdown = document.createElement("div");
        this.addClasslist(oDropdown, ['osekai__left', 'osekai__center-flex-row']);

        oDropdown.appendChild(oRightContent);

        if (bContainsBottomBar) {
            let oDropdownSymbol = document.createElement("i");
            oDropdownSymbol.setAttribute("selector", "mobile__dropdown__button");
            this.addClasslist(oDropdownSymbol, ['fas', 'fa-angle-up', 'snapshots__mobile__info', 'snapshots__mobile__info-closed']);
            oDropdown.appendChild(oDropdownSymbol);
        }

        oTopBar = this.appendChildren(oTopBar, [oRank, this.createCountrySpan(oEntry, false), oPlayer, oDropdown]);
        return oTopBar;
    }

    this.createMobileMedalTopBar = function (oEntry) {
        let oTopBar = document.createElement("div");
        oTopBar = this.addClasslist(oTopBar, ['rankings__mobile__top-bar', 'rankings__mobile__top-bar-truncate']);

        let oRank = this.createParagraph("#" + oEntry.rank);
        let oName = this.createParagraph(oEntry.medalname);

        oName.classList.add("pointer");
        oName.addEventListener("click", () => medalPopupV2.showMedalFromName(oEntry.medalname));
        oName.classList.add("rankings__mobile__top-bar-primary-text");

        let oDropdown = document.createElement("div");
        this.addClasslist(oDropdown, ['osekai__left', 'osekai__center-flex-row']);

        let oPosessionSpan = this.createSpan(false, oEntry.possessionRate + "%");
        oDropdown.appendChild(oPosessionSpan);

        let oDropdownSymbol = document.createElement("i");
        oDropdownSymbol.setAttribute("selector", "mobile__dropdown__button");
        this.addClasslist(oDropdownSymbol, ['fas', 'fa-angle-up', 'snapshots__mobile__info', 'snapshots__mobile__info-closed']);
        oDropdown.appendChild(oDropdownSymbol);

        let oSpanMedalImg = document.createElement("span");
        let oMedalImg = this.createImg(oEntry.link);
        oSpanMedalImg.classList.add("rankings__mobile__inline-medal");
        oSpanMedalImg.appendChild(oMedalImg);

        oTopBar = this.appendChildren(oTopBar, [oRank, oSpanMedalImg, oName, oDropdown]);
        return oTopBar;
    }

    this.createMedalUsersBottomBar = function (oEntry) {
        let oDivBottom = document.createElement("div");
        this.addClasslist(oDivBottom, ['hidden', 'rankings__mobile__bottom-content']);

        let oInfoDiv = document.createElement("div");
        this.addClasslist(oInfoDiv, ["osekai__flex_row", "rankings__mobile__header"]);

        let oParagraph = document.createElement("p");

        let oMedalSpan = document.createElement("span");
        oMedalSpan.classList.add("light");
        oMedalSpan.innerHTML = "medal";

        let oCompletionSpan = this.createSpan(true, "completion");
        let oParagraphCompletion = this.createParagraph(oEntry.completion + "%");
        oParagraphCompletion.classList.add("osekai__left");

        oParagraph = this.appendChildren(oParagraph, [oMedalSpan, oCompletionSpan]);

        oInfoDiv = this.appendChildren(oInfoDiv, [oParagraph, oParagraphCompletion]);

        let oBar = document.createElement("div");
        oBar.classList.add("rankings__pb-bar");

        let oInnerBar = document.createElement("div");
        oInnerBar.classList.add("rankings__pb-innerbar");
        oInnerBar.style.setProperty("width", oEntry.completion + "%");
        oBar.appendChild(oInnerBar);

        oDivRarestMedal = document.createElement("div");
        oDivRarestMedal = this.addClasslist(oDivRarestMedal, ["osekai__flex_row", "rankings__mobile__header"]);

        oParagraphInfoRarestMedal = document.createElement("p");
        oSpanRarest = this.createSpan(true, "rarest");
        oSpanMedal = this.createSpan(false, "medal");
        oSpanMedal.classList.add("light");

        oParagraphInfoRarestMedal = this.appendChildren(oParagraphInfoRarestMedal, [oSpanRarest, oSpanMedal]);
        oDivRarestMedal = this.appendChildren(oDivRarestMedal, [oParagraphInfoRarestMedal]);

        oParagraphMedal = document.createElement("p");
        oSpanMedalImg = document.createElement("span");
        oMedalImg = this.createImg(oEntry.link);
        oSpanRarestMedal = this.createSpan(true, oEntry.rarestmedal);
        oSpanRarestMedal.classList.add("pointer");
        oSpanRarestMedal.addEventListener("click", () => medalPopupV2.showMedalFromName(oEntry.rarestmedal));

        oSpanMedalImg.classList.add("rankings__mobile__inline-medal");
        oSpanMedalImg.appendChild(oMedalImg);

        oParagraphMedal = this.appendChildren(oParagraphMedal, [oSpanMedalImg, oSpanRarestMedal]);

        oDivBottom = this.appendChildren(oDivBottom, [oInfoDiv, oBar, oDivRarestMedal, oParagraphMedal]);
        return oDivBottom;
    }

    this.createMobileMedalBottomBar = function (oEntry) {
        let oDivBottom = document.createElement("div");
        this.addClasslist(oDivBottom, ['hidden', 'rankings__mobile__bottom-content']);

        oDivDescription = document.createElement("div");
        oDivDescription = this.addClasslist(oDivDescription, ["osekai__flex_row", "rankings__mobile__header"]);

        oParagraphDescriptionHeader = document.createElement("p");

        oSpanMedal = document.createElement("span");
        oSpanMedal.classList.add("light");
        oSpanMedal.innerHTML = "medal";

        oSpanDescription = this.createSpan(true, "description");
        oParagraphDescriptionHeader = this.appendChildren(oParagraphDescriptionHeader, [oSpanMedal, oSpanDescription]);

        oDivDescription = this.appendChildren(oDivDescription, [oParagraphDescriptionHeader])

        oParagraphDescription = document.createElement("p");
        oParagraphDescription.classList.add("rankings__mobile__header-content");
        oParagraphDescription.innerHTML = oEntry.description;

        if (oEntry.gameMode == "NULL") {
            oDivBottom = this.appendChildren(oDivBottom, [oDivDescription, oParagraphDescription]);
            return oDivBottom;
        }

        oDivMode = document.createElement("div");
        oDivMode = this.addClasslist(oDivMode, ["osekai__flex_row", "rankings__mobile__header", "rankings__mobile__header-nocontent"]);

        oParagraphModeWrapper = document.createElement("p");

        oSpanMode = document.createElement("span");
        oSpanMode.classList.add("light");
        oSpanMode.innerHTML = oEntry.gameMode;

        oSpanOnly = this.createSpan(true, "only");
        oPargraphModeWrapper = this.appendChildren(oParagraphModeWrapper, [oSpanMode, oSpanOnly]);

        oDivMode = this.appendChildren(oDivMode, [oParagraphModeWrapper])

        oDivBottom = this.appendChildren(oDivBottom, [oDivDescription, oParagraphDescription, oDivMode]);
        return oDivBottom;
    }

    this.addTopBarEventListeners = function () {
        document.querySelectorAll("[selector='mobile__dropdown__button']").forEach((oButton) => {
            oButton.addEventListener("click", this.TopBarClickEvent)
        });
    }

    this.addCountryImgEventListeners = function () {
        document.querySelectorAll(("[selector='img_country']")).forEach((oImg) => {
            let strCountry = oImg.getAttribute("countrycode");
            let strCode = getCountryNameByShortCode(strCountry.toUpperCase());
            oImg.addEventListener("click", () => {
                document.querySelectorAll("[selector='search__input']").forEach((oFilter) => {
                    oFilter.value = strCountry;
                })
                document.querySelectorAll("[selector='filter__activeItem']").forEach((oSelectedFilter) => {
                    oSelectedFilter.innerHTML = "Country";
                });
                currentApp.dbhandler.filterValue = strCode;
                currentApp.dbhandler.filterCondition = "country";
                currentApp.dbhandler.Filter();
            });
        });
    }

    this.TopBarClickEvent = function () {
        Array.prototype.slice.call(this.parentNode.parentNode.parentNode.children).forEach((oChild) => {
            if (oChild.classList.contains("rankings__mobile__bottom-content")) {
                oChild.classList.toggle("hidden");
                if (this.classList.contains("fa-angle-down")) {
                    this.classList.remove("fa-angle-down");
                    this.classList.add("fa-angle-up");
                } else {
                    this.classList.remove("fa-angle-up");
                    this.classList.add("fa-angle-down");
                }
            }
        })
    }

    this.createMobileCount = function (oValue, strText) {
        let oParagraph = document.createElement("p");
        let oSpan = this.createSpan(true, oValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

        oParagraph.appendChild(oSpan);
        oParagraph.innerHTML += " " + strText;

        return oParagraph;
    }

    this.createCountrySpan = function (oEntry, bHasTooltip) {
        let oSpanCountry = document.createElement("span");
        let oImg = this.createImg("https://osu.ppy.sh/images/flags/" + oEntry.countrycode + ".png");
        oImg.setAttribute("countrycode", oEntry.countrycode);
        oImg.setAttribute("selector", "img_country")

        if (bHasTooltip) {
            oImg.classList.add("tooltip-v2");
            oImg.setAttribute("tooltip-content", oEntry.country);
        }

        oSpanCountry.classList.add("rankings__inline-country");
        oSpanCountry.appendChild(oImg);
        return oSpanCountry;
    }

    this.createSpan = function (bIsStrong, oInnerHtml) {
        let oSpan = document.createElement("span");
        oSpan.innerHTML = oInnerHtml;
        if (bIsStrong) oSpan.classList.add("strong");
        return oSpan;
    }

    this.createImg = function (strPath) {
        let oImg = document.createElement("img");
        oImg.setAttribute("src", strPath);
        return oImg;
    }

    this.createParagraph = function (oInnerHtml) {
        let oParagraph = document.createElement("p");
        oParagraph.innerHTML = oInnerHtml;
        return oParagraph;
    }

    this.addClasslist = function (oElement, colClasses) {
        colClasses.forEach((oClass) => {
            oElement.classList.add(oClass);
        });
        return oElement;
    }

    this.appendChildren = function (oElement, colChildren) {
        colChildren.forEach((oChild) => {
            oElement.appendChild(oChild);
        })
        return oElement;
    }
}

// app-collections
var medalApps = new parentApp("Medals", "general.medals.title");
var modeApps = new parentApp("All Mode", "general.allmode.title");
var mapperApps = new parentApp("Mappers", "general.mappers.title");
var badgeApps = new parentApp("Badges", "general.badges.title");

// apps
var appUsers = medalApps.AddChild("Users", ["Username", "User ID", "Country", "Rarest Medal"], "general.medals.users");
var appRarity = medalApps.AddChild("Rarity", ["Medal Name", "Medal ID", "Description", "Mode", "Group"], "general.medals.rarity");
var appStdev = modeApps.AddChild("Standard Deviation", ["Username", "User ID", "Country"], "general.allmode.standardDeviation.short");
var appTPP = modeApps.AddChild("Total pp", ["Username", "User ID", "Country"], "general.allmode.total");

var appTotalLevel = modeApps.AddChild("Total Level", ["Username", "User ID", "Country"], "general.allmode.totalLevel");
var apStdevLevel = modeApps.AddChild("Stdev Level", ["Username", "User ID", "Country"], "general.allmode.standardDeviatedLevel.short");
var appTotalAcc = modeApps.AddChild("Total Accuracy", ["Username", "User ID", "Country"], "general.allmode.totalAccuracy");
var appStdevAcc = modeApps.AddChild("Stdev Accuracy", ["Username", "User ID", "Country"], "general.allmode.standardDeviatedAccuracy.short");

var appReplays = modeApps.AddChild("Replays", ["Username", "User ID", "Country"], "general.allmode.replays");
var appRanked = mapperApps.AddChild("Ranked Mapsets", ["Username", "User ID", "Country"], "general.mappers.ranked");
var appLoved = mapperApps.AddChild("Loved Mapsets", ["Username", "User ID", "Country"], "general.mappers.loved");
var appSubscribers = mapperApps.AddChild("Subscribers", ["Username", "User ID", "Country"], "general.mappers.subscribers");
var appKudosu = mapperApps.AddChild("Kudosu", ["Username", "User ID", "Country"], "general.mappers.kudosu");
var appBadges = badgeApps.AddChild("Badges", ["Username", "User ID", "Country"], "general.badges.badges");


// initial function calls
bInitialized = false;
colParentApps.forEach((oParent) => {
    oParent.children.forEach((oChild) => {
        if (new URLSearchParams(window.location.search).get("type") == oChild.name) {
            oChild.Initialize();
            bInitialized = true;
        }
    })
})
if (!bInitialized) InitializeHome();

window.addEventListener('popstate', function (event) {
    bInitialized = false;
    colParentApps.forEach((oParent) => {
        oParent.children.forEach((oChild) => {
            if (new URLSearchParams(window.location.search).get("type") == oChild.name) {
                oChild.Initialize();
                bInitialized = true;
            }
        })
    })
    if (!bInitialized) InitializeHome();
});

window.addEventListener('click', function (e) {
    let bHide = true;
    document.querySelectorAll("[selector='dropdown__filters']").forEach((colFilters) => {
        if (colFilters.contains(e.target)) bHide = false;
    });
    document.querySelectorAll("[selector='filter__items']").forEach((colItems) => {
        if (bHide) colItems.classList.add("osekai__dropdown-hidden");
    });
});

// functions
function InitializeHome() {
    document.getElementById("home").classList.remove("hidden");
    document.getElementById("mobile").classList.add("hidden");
    document.getElementById("desktop").classList.add("hidden");
    currentApp = "home";

    document.querySelectorAll("[selector='home__button']").forEach((oButton) => {
        oButton.addEventListener("click", () => {
            window[oButton.getAttribute("app")].Initialize();
        })
    });

    document.getElementById("osekai__button-add").addEventListener("click", () => {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", API_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.send("Member=" + document.getElementById("osekai__input-id").value);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var oResponse = JSON.parse(xhr.responseText);
                if (oResponse !== undefined && oResponse !== null) {
                    if (oResponse == "Success!") {
                        document.getElementById("osekai__input-id").value = "";
                        generatenotification("normal", "This user has been added to Osekai Rankings! They will be processed within the next 3 days.");
                    } else {
                        generatenotification("error", "This user could not be added: " + oResponse);
                    }
                }
            };
        };
    });

   
    LoadCurrentState();
    LoadStateHistory();
    UpdateTaskTimer();
}

var temporarySeconds = 0;
var lastSeconds = 0;
var justRunning = false;

function LoadCurrentState() {
    // note: updates every minute
    var xhr = new XMLHttpRequest();
    xhr.open("POST", API_URL, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send("State=");
    xhr.onload = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var oResponse = JSON.parse(xhr.responseText);
            console.log(oResponse);
            document.getElementById("currenttask_name").innerHTML = oResponse['CurrentLoop'];   
            var percentage = Math.round((oResponse['CurrentCount'] / oResponse['TotalCount'] * 100)*100) / 100;
            if(lastSeconds != oResponse['EtaSeconds']) {
                temporarySeconds = oResponse['EtaSeconds'];
            }
            document.getElementById("currenttask_status").innerHTML = `<strong>${percentage}%</strong> - ${oResponse['CurrentCount']}/${oResponse['TotalCount']}`;;   
            document.getElementById("currenttask_progress").style.width = percentage + "%";

            if(oResponse['CurrentLoop'] == "Complete") {
                document.getElementById("currenttask").classList.add("hidden")
                document.getElementById("currenttask-text").innerHTML = "No running tasks.";
                if(justRunning == true) {
                    LoadStateHistory();
                }
                justRunning = false;
            } else {
                justRunning = true;
                document.getElementById("currenttask").classList.remove("hidden")
                document.getElementById("currenttask-text").innerHTML = "Current Tasks";
            }
        }
    };
    setTimeout(() => {
        if(!document.getElementById("home").classList.contains("hidden")) {
            // homepage is visible, update
            LoadCurrentState();
        }
    }, 30000);
}
function UpdateTaskTimer() {
    setTimeout(() => {
        temporarySeconds = temporarySeconds - 1;
        if(!document.getElementById("home").classList.contains("hidden")) {
            document.getElementById("currenttask_eta").innerHTML =  GetStringRawNonAsync(APP_SHORT, "tasks.task.eta", [new Date(temporarySeconds * 1000).toISOString().substring(11, 19)])

            
        }
        UpdateTaskTimer();
    }, 1000);
}
function LoadStateHistory() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", API_URL, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send("StateHistory=");
    xhr.onload = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var oResponse = JSON.parse(xhr.responseText);
            console.log(oResponse);
            var html = ``;
            for(var x = 0; x < oResponse.length; x++) {
                html += `<div class="rankings__task rankings__task-finished">
                <div class="rankings__task-accent">
                    <i class="fas fa-check"></i>
                </div>
                <div class="rankings__task-content-small">
                    <div class="rankings__task-text-left">
                        <h2>${GetStringRawNonAsync(APP_SHORT, "tasks.type." + oResponse[x]['LoopType'].toLowerCase())}</h2>
                        <h3>${GetStringRawNonAsync(APP_SHORT, "tasks.task.usersProcessed", [oResponse[x]['Amount']])}</h3>
                    </div>
                    <div class="rankings__task-text-right">
                        <h2>${TimeAgo.inWords(new Date(oResponse[x]['Time']).getTime())}</h2>
                    </div>
                </div>
            </div>`;
            }
            document.getElementById("completedtasks_list").innerHTML = html;
        }
    }
}

function RemoveHome() {
    document.getElementById("home").classList.add("hidden");
    document.getElementById("mobile").classList.remove("hidden");
    document.getElementById("desktop").classList.remove("hidden");
}