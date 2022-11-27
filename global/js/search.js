var input = document.getElementById("searchInput");
var searchquery = "";

function searchGo() {
    console.log("searchGo");
    if (input.value.length > 0) {
        document.getElementById("searchOverlay").classList.add("searchOverlayActive");
        document.getElementById("search_results").classList.add("hidden");
        document.getElementById("search_loader").classList.remove("hidden");
    }

    //document.getElementById("loaderContainer").classList.remove("search__loader-container-closed");

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/global/api/search.php?query=' + input.value, true);

    xhr.onload = function () {
        // decode json
        var json = JSON.parse(xhr.responseText);
        if (json['query'] == input.value) {
            var medals = document.getElementById("medalsResult");
            medals.innerHTML = "";

            if (json["medals"] != null) {
                if(json["medals"].length == 0) {
                    medals.innerHTML = "<p class='osekai__search-noresults'>" + GetStringRawNonAsync("navbar", "search.noResults") + "</p>";
                }
                for (var i = 0; i < json["medals"].length; i++) {
                    medals.innerHTML += `<a href="/medals/?medal=` + json['medals'][i]["name"] + `" class="search__result search__result-medals">
                                <img src="` + json['medals'][i]["link"] + `" alt="medal" />
                                <div class="search__result-texts">
                                    <p class="search__result-text-title">` + json['medals'][i]["name"] + `</p>
                                    <p class="search__result-text-subtitle">` + json['medals'][i]["description"] + `</p>
                                </div>
                            </a>`;
                }
            } 


            var profiles = document.getElementById("profilesResult");
            profiles.innerHTML = "";

            if (json["profiles"] != null) {
                if(json["profiles"].length == 0) {
                    profiles.innerHTML = "<p class='osekai__search-noresults'>" + GetStringRawNonAsync("navbar", "search.noResults") + "</p>";
                }
                for (var i = 0; i < json["profiles"].length; i++) {
                    profiles.innerHTML += `<a href="/profiles/?user=` + json["profiles"][i]["id"] + `" class="search__result search__result-profiles">
                                <img src="` + json['profiles'][i]["avatar_url"] + `" alt="profile picture" />
                                <div class="search__result-texts">
                                    <p class="search__result-text-title">` + json['profiles'][i]["username"] + `</p>
                                </div>
                            </a>`;
                }
            }


            var snapshots = document.getElementById("snapshotsResult");
            snapshots.innerHTML = "";

            if (json["snapshots"] != null) {
                if(json["snapshots"].length == 0) {
                    snapshots.innerHTML = "<p class='osekai__search-noresults'>" + GetStringRawNonAsync("navbar", "search.noResults") + "</p>";
                }
                for (var i = 0; i < json["snapshots"].length; i++) {
                    snapshots.innerHTML += `<a href="/snapshots/?version=` + json["snapshots"][i]["version_info"]["id"] + `" class="search__result search__result-snapshots">
                                <div class="search__result-texts">
                                    <p class="search__result-text-title"><strong>` + json['snapshots'][i]["version_info"]["name"] + `</strong> ` + json['snapshots'][i]["version_info"]["version"] + `</p>
                                    <p class="search__result-text-subtitle">released ` + json['snapshots'][i]["version_info"]["release"] + `</p>
                                </div>
                            </a>`;
                }
            } 
            //document.getElementById("loaderContainer").classList.add("search__loader-container-closed");
        }
        document.getElementById("search_loader").classList.add("hidden");
        document.getElementById("search_results").classList.remove("hidden");
    };
    xhr.send();
}

input.addEventListener("keyup", function (evt) {
    if (searchquery != input.value) {
        searchquery = input.value;
        let currentquery = input.value;
        window.setTimeout(function(){
            if(currentquery == searchquery) {
            searchGo();
            } else {
                console.log(currentquery + " is no longer " + searchquery)
            }
          },250);
        
    }
}, false);

input.addEventListener("focusout", function (evt) {
    if (input.value.length == 0) {
        document.getElementById("searchOverlay").classList.remove("searchOverlayActive");
    }
}, false);

searchButton = null

function openSearch(requestedElement) {
    searchButton = requestedElement;
    document.getElementById("searchOverlay").classList.toggle("search__closed");

    if (document.getElementById("searchOverlay").classList.contains("search__closed")) {
        requestedElement.classList.remove("osekai__navbar-button-active");
        // clear value
        input.value = "";
        searchquery = "";
        // wait for animation then remove class
        setTimeout(function () {
            document.getElementById("searchOverlay").classList.remove("searchOverlayActive");
        }, 500);
    } else {
        requestedElement.classList.add("osekai__navbar-button-active");
    }
}

document.onkeydown = function (evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key === "Escape" || evt.key === "Esc");
    } else {
        isEscape = (evt.keyCode === 27);
    }
    if (isEscape) {
        if (!document.getElementById("searchOverlay").classList.contains("search__closed")) {
            openSearch(searchButton);
        }
    }
};