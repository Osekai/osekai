function getMostVisibleElement(els) {

    var viewportHeight = window.innerHeight

    var maxtop = 999999999;
    var mostVisibleEl = null

    for (var el of Array.from(els)) {
        if (!el.classList.contains("azelia__versions-listing-group-year")) {
            continue;
        }
        var viewportOffset = el.getBoundingClientRect();
        // these are relative to the viewport, i.e. the window
        var top = viewportOffset.top;
        /* console.log("top: " + top); */
        /* console.log("offsetHeight: " + el.offsetHeight) */
        var left = viewportOffset.left;
        if (top < maxtop && top > (0 - (el.offsetHeight - (viewportHeight / 3)))) {
            /* console.log("smaller"); */
            mostVisibleEl = el;
            maxtop = top;
        }
    }

    return mostVisibleEl;
}



const pages = {
    'home': {
        'title': 'Home',
        'url': '?page=home',
        'element': document.getElementById('home')
    },
    'versionlisting': {
        'title': 'Version Listing',
        'url': '?page=versionlisting',
        'element': document.getElementById('versionlisting')
    }
}

activePage = null;

function goToPage(pageName) {
    for (page in pages) {
        if (page == pageName) {
            pages[page].element.classList.remove('hidden');
            document.title = pages[page].title;
            if (activePage != page) {
                window.history.pushState(null, pages[page].title, pages[page].url);
                activePage = page;
            }
        }
        else {
            pages[page].element.classList.add('hidden');
        }
    }
}

function getActivePageFromURL() {
    const params = (new URL(location)).searchParams;
    console.log(params);
    if (params.get("page") == null) {
        console.log("page null lol");
        return "home";
    } else {
        return params.get("page");
    }
    return page;
}

window.onpopstate = function (event) {
    goToPage(getActivePageFromURL());
    module_version.toggleOverlay();
    module_version.loadVersionFromUrl(); // just in case it's changed, likely
}

const Groups = {
    'stable': {
        // this includes beta and cutting edge
        'name': 'Stable',
        'longname': 'osu!stable',
        'id': 0,
        'color': '#00ff00'
    },
    "lazer": {
        'name': 'Lazer',
        'longname': 'osu!lazer',
        'id': 1,
        'color': '#ff0000'
    },
}

var module_home = {
    loadPage: function () {

    }
}

document.onscroll = function () {
    module_versionlisting.calcVisible();
}

var module_versionlisting = {
    versionListingContent: document.getElementById('versionlisting_content'),
    loadPage: function () {
        module_version.loadVersions().then(function () {
            this.loadListing();
        });
    },
    config: {
        'style': 'grid', // grid, list, compact
        'sort': 'date', // date, archivaldate, versionname

    },
    calcVisible: function () {
        var visible = getMostVisibleElement(document.getElementById("versionlisting_content").childNodes);
        if (visible != null) {
            var visible_group = visible.getAttribute("data-group");
            var visible_year = visible.getAttribute("data-year");
            console.log(visible_group);
            console.log(visible_year);
            var buttons = document.getElementsByClassName("azelia__sidebar-version");
            for (var x = 0; x < buttons.length; x++) {
                buttons[x].classList.remove("azelia__sidebar-version-active");
            }
            console.log(visible);
            document.getElementById("versionlisting_sidebar_" + visible_group + "_" + visible_year).classList.add("azelia__sidebar-version-active");
        }
    },
    generateHeader: function (title, number, group) {
        html = `<div class="azelia__versions-listing-group-header" id="groupheader_${group}">
        <h1>${title}</h1>
        <h3><strong>${number}</strong> ${number == 1 ? 'version' : 'versions'}</h3>
        </div>`;
        return html;
    },
    generateYearGroup: function (year, versioncount, archivercount, viewtype, group) {
        html = `<div class="azelia__versions-listing-group-year" data-year="` + year + `" data-group="` + group + `">
        <div class="azelia__versions-listing-group-year-top">
            <div class="azelia__versions-listing-group-year-left">
                <h2>${year}</h2>
            </div>
            <div class="azelia__versions-listing-group-year-right">
                <h3>${versioncount} ${versioncount == 1 ? 'version' : 'versions'}</h3>
                <p>from ${archivercount} ${archivercount == 1 ? 'archiver' : 'archivers'}</p>
            </div>
        </div>
        <div class="azelia__versions-listing-group-grid ${viewtype}">
            {template}
        </div>
        </div>`;
        return html;
    },
    goToYear: function (group, year) {
        var elements = document.getElementsByClassName("azelia__versions-listing-group-year");
        for (var x = 0; x < elements.length; x++) {
            if (elements[x].getAttribute("data-year") == year && elements[x].getAttribute("data-group") == group) {
                osekaiScrollTo(elements[x]);
            }
        }
    },
    goToGroup: function (group) {
        osekaiScrollTo(document.getElementById(`groupheader_${group}`));
    },
    addGroupToSidebar: function (group, years) {
        var html = `<div class="azelia__sidebar-section" id="versionlisting_sidebar_` + group + `">`;
        html += `<h1 class="azelia__sidebar-title" onclick="module_versionlisting.goToGroup('${group}')">osu!` + Groups[group].name.toLowerCase() + `</h1>`;
        html += `<div class="azelia__sidebar-versions">`;

        for (year in years) {
            var version = years[year];
            console.log(version);
            html += `<div class="azelia__sidebar-version" id="versionlisting_sidebar_` + group + `_` + years[year].year + `" onclick="module_versionlisting.goToYear('${group}', '${years[year].year}');">
            <p class="azelia__sidebar-version-year">` + years[year].year + `</p>
            <p class="azelia__sidebar-version-count"><strong>` + version.versions.length + `</strong> versions</p>
        </div>`;
        }
        html += `</div>
        </div>`;
        document.getElementById("versionlisting_sidebar").innerHTML += html;
    },
    loadListing: function () {
        document.getElementById("versionlisting_sidebar").innerHTML = "";
        var html = ``;
        console.log(Groups);
        for (group in Groups) {
            var versions = [];
            console.log("Group: " + Groups[group].name);

            for (var i = 0; i < module_version.versions.length; i++) {
                var thisVersion = module_version.versions[i];
                if (parseInt(thisVersion.Group) == Groups[group].id) {
                    // TODO: add version html
                    //html += "temp: " + thisVersion.Name + "<br>";
                    //html += `<p class="osekai__button" onclick="module_version.loadVersion(${i})">${thisVersion.Name}</p>`;
                    versions.push(thisVersion);
                } else {
                    console.log("Not in group: " + thisVersion.group);
                }
            }

            var name = "<strong>osu!</strong>" + Groups[group].name.toLowerCase();
            var count = versions.length;
            html += this.generateHeader(name, count, group);

            var years = {};
            for (var i = 0; i < versions.length; i++) {
                var thisVersion = versions[i];
                //console.log(thisVersion);
                var year = thisVersion.ReleaseDate.split('-')[0];
                if (years[year] == undefined) {
                    years[year] = [];
                    years[year]['year'] = year;
                    years[year]['versions'] = [];
                }
                years[year]['versions'].push(thisVersion);
            }
            // order array by year
            var yearsArray = [];
            for (var i = 0; i < Object.keys(years).length; i++) {
                yearsArray.push(years[Object.keys(years)[i]]);
            }
            yearsArray.sort(function (a, b) {
                return b.year - a.year;
            });
            years = yearsArray;
            console.log(years);
            this.addGroupToSidebar(group, years);
            for (year in years) {
                var versionCount = years[year].versions.length;

                let versions = years[year].versions
                let archivers = [];
                versions.forEach(ver => {6
                    if (archivers.indexOf(ver.ArchiverID) == -1)
                        archivers.push(ver.ArchiverID);
                });
                let archiversCount = archivers.length;

                html += this.generateYearGroup(years[year].year, versionCount, archiversCount, module_versionlisting.config.style, group);
                var yearhtml = ``;
                // sort versions by ReleaseDate
                years[year].versions.sort(function (a, b) {
                    return new Date(b.ReleaseDate) - new Date(a.ReleaseDate);
                });
                for (var i = 0; i < years[year].versions.length; i++) {
                    var thisVersion = years[year].versions[i];
                    yearhtml += `<div class="azelia__version-grid" id="versionlisting_grid_` + years[year].year + `" onclick="module_version.loadVersion(` + thisVersion['Id'] + `)">
                    <img src="/snapshots/versions/` + thisVersion.Name + `/thumbnail.jpg">
                    <div class="azelia__version-grid-info">
                        <p class="azelia__version-grid-info-title">` + thisVersion.Name + `</p>
                        <p class="azelia__version-grid-info-releasedate">released <strong>` + thisVersion.ReleaseDate + `</strong></p>
                        <p class="azelia__version-grid-info-archiver">archived by <strong>` + thisVersion.Archiver + `</strong></p>
                    </div>
                    <div class="azelia__version-grid-stats">
                        <p class="azelia__version-grid-stats-views">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            <span>` + thisVersion.Views + `</span>
                        </p>
                        <p class="azelia__version-grid-stats-downloads">
                            <i class="fas fa-download" aria-hidden="true"></i>
                            <span>` + thisVersion.Downloads + `</span>
                        </p>
                    </div>
                </div>`;
                    //yearhtml += "<p class='osekai__button' onclick='module_version.loadVersion(" + i + ")'>" + thisVersion.Name + "</p>";
                }
                html = html.replace('{template}', yearhtml);
            }
        }
        this.versionListingContent.innerHTML = html;
        this.calcVisible();
    },
    reorderVersions: function () {
        var newVersions = [];
        if (this.config.sort == 'date') {
            newVersions = module_version.versions.sort(function (a, b) {
                return new Date(a.Date) - new Date(b.Date);
            });
        } else if (this.config.sort == 'archivaldate') {
            newVersions = module_version.versions.sort(function (a, b) {
                return new Date(a.ArchivedDate) - new Date(b.ArchivedDate);
            });
        } else if (this.config.sort == 'versionname') {
            newVersions = module_version.versions.sort(function (a, b) {
                return a.Name.localeCompare(b.Name);
            });
        }
        module_version.versions = newVersions;
    }
}

var module_version = {
    overlayOpen: false,
    versions: null,
    overlay: document.getElementById("versionInfoOverlay"),
    toggleOverlay: function () {
        const params = new URLSearchParams(location.search);
        if (params.get("version") != null) {
            this.overlay.classList.remove("osekai__ox2-closed")
            this.overlayOpen = true;
        } else {
            this.overlay.classList.add("osekai__ox2-closed")
            this.overlayOpen = false;
        }
    },
    loadVersion: function (version) {
        console.log(version);
        var url = new URL(window.location.href);
        url.searchParams.set('version', version);
        window.history.pushState(null, pages[page].title, url);
        this.loadVersionFromUrl();
        this.toggleOverlay();
    },
    loadVersionFromUrl: function () {
        this.loadVersions().then(function () {
            const params = new URLSearchParams(location.search);
            if (params.get("version") != null) {
                var versionId = params.get("version");
                var thisVersion = null;
                for (var x = 0; x < module_version.versions.length; x++) {
                    if (module_version.versions[x].Id == versionId) {
                        thisVersion = module_version.versions[x];
                        break;
                    }
                }
                if (thisVersion == null) {
                    generatenotification("error", "Could not load this version.");
                    module_version.closeVersionPanel(true);
                    return;
                }
                console.log(thisVersion);
                //0: Object { Id: "13", Name: "20160906", Title: "osu!lazer 20160906", … }
                //  ArchivalDate: "1970-01-01 01:00:00"
                //  Archiver: "Hubz"
                //  ArchiverID: "10379965"
                //  AutoUpdates: "0"
                //  Description: "This version is an extremely early build of post-transitional osu!lazer, before the first release even occurred."
                //  Downloads: "299"
                //  ExtraInfo: "You can easily get these new codebase builds from lazer's github page by going down the commit history but we just uploaded one here as an example so you don't have to scroll down hundreds of commit history pages."
                //  Group: "1"
                //  Id: "13"
                //  Name: "20160906"
                //  Note: null
                //  ReleaseDate: "2016-09-06 00:00:00"
                //  Title: "osu!lazer 20160906"
                //  VersionDownloads: Array [ {…} ]
                //    0: Object { Id: 8592, ReferencedVersion: 13, Name: "Osekai Servers", … }
                //  VersionScreenshots: Array [ {…}, {…} ]
                //      Id: 33492
                //      ImageLink: "20160906_1.png"
                //      Order: 0
                //      ReferencedVersion: 13
                //  1: Object { Id: 33493, ReferencedVersion: 13, Order: 1, … }
                //      Video: ""
                //      Views: "1445"
                function getImageFromVersion(version, screenshotIndex) {
                    return "/snapshots/versions/" + version["Name"] + "/" + version["VersionScreenshots"][screenshotIndex]["ImageLink"];
                }
                function getThumbnailFromVersion(version) {
                    return "/snapshots/versions/" + version["Name"] + "/thumbnail.jpg";
                }
                module_version.overlay.style.setProperty("--background", "url(" + getThumbnailFromVersion(thisVersion) + ")");
                document.getElementById("vio_name").innerHTML = thisVersion["Title"] + " <strong>" + thisVersion["Name"] + "</strong>";
                document.getElementById("vio_release").innerHTML = "released <strong>" + new Date(thisVersion['ReleaseDate']).toLocaleDateString("en-gb", { day: "numeric", month: "long", year: "numeric" }) + "</strong>"
                document.getElementById("vio_description").innerHTML = thisVersion["Description"];
                var archival_date = new Date(thisVersion['ArchivalDate']).getTime();
                console.log(archival_date);
                document.getElementById("vio_archival-date").innerHTML = `added to snapshots <strong class="tooltip-v2" tooltip-content="` + thisVersion['ArchivalDate'] + `">` + TimeAgo.inWords(archival_date) + "</strong>";
                if (archival_date == 0) {
                    document.getElementById("vio_archival-date").classList.add("hidden");
                } else {
                    document.getElementById("vio_archival-date").classList.remove("hidden");
                }
            }
        });
    },
    closeVersionPanel: function (setHistory = true) {
        var url = new URL(window.location.href);
        url.searchParams.delete('version');
        if (setHistory == true) {
            window.history.pushState(null, pages[page].title, url);
        } else {
            window.history.replaceState(null, pages[page].title, url);
        }
        this.toggleOverlay();
    },
    loadVersions: function (forced = false) {
        console.log('loading versions');
        var p = new Promise(function (resolve, reject) {
            if (forced == true || module_version.versions == null) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/azelia/api/get_versions.php', true);
                xhr.onload = function () {
                    if (this.status == 200) {
                        module_version.versions = JSON.parse(this.responseText);
                        module_versionlisting.loadListing();
                        console.log(module_version.versions);
                        resolve();
                    } else {
                        reject();
                    }
                }
                xhr.send();
            } else {
                resolve();
            }
        });
        return p;
    }
}


function Init() {
    activePage = getActivePageFromURL();
    module_version.toggleOverlay();
    module_version.loadVersionFromUrl();
    goToPage(activePage);
    openLoader("Loading Osekai Snapshots [codename Azelia]");
    module_version.loadVersions().then(function () {
        closeLoader();
    });
}
Init();


document.onkeydown = function (evt) {
    evt = evt || window.event;
    if (evt.key == "Escape") {
        module_version.closeVersionPanel();
    }
};