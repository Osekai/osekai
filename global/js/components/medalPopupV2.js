const medalPopupV2 = {
    showMedalFromName: function (name) {
        for (var x = 0; x < medals.length; x++) {
            if (medals[x]["Name"] == name) {
                return medalPopupV2.showMedalFromId(medals[x]["MedalID"]);
            }
        }
    },
    showMedalFromId: function (id) {
        document.getElementById("mhv2-overlay").classList.remove("hidden")
        for (var x = 0; x < medals.length; x++) {
            if (medals[x]["MedalID"] == id) {
                var medal = medals[x];
                console.log(medal);
                document.getElementById("mhv2-name").innerHTML = medal["Name"];
                document.getElementById("mhv2-text").innerHTML = medal["Description"];
                document.getElementById("mhv2-solution").innerHTML = medal["Solution"];
                document.getElementById("mhv2-open-button").setAttribute("href", "/medals/?medal=" + medal["Name"]);
                document.getElementById("mhv2-icon").src = "/medals/img/unknown_medal.png";
                document.getElementById("mhv2-icon-blur").src = "/medals/img/unknown_medal.png";
                document.getElementById("mhv2-icon").src = medal["Link"].replace(".png", "@2x.png");
                document.getElementById("mhv2-icon-blur").src = medal["Link"].replace(".png", "@2x.png");
                document.getElementById("mhv2-overlay").classList.remove("osekai__medal-popup-closed")
                if (medal['Mods'] != null && medal['Mods'] != "") {
                    document.getElementById("mhv2-mods").innerHTML = "";
                    document.getElementById("mhv2-mods").classList.remove("hidden");
                    for (let i = 0; i < colLinks.length; i++) {
                        if (medal['Mods'].includes(colMods[i])) {
                            document.getElementById("mhv2-mods").innerHTML += '<img alt="' + colRealNames[i] + '" class="osekai__medal-popup-mod tooltip-v2" tooltip-content="' + colRealNames[i] + '" src="https://osu.ppy.sh/images/badges/mods/mod_' + colLinks[i] + '@2x.png">';
                        }
                    }
                } else {
                    document.getElementById("mhv2-mods").classList.add("hidden");
                }
                break;
            }
        }
    },
    hideOverlay: function () {
        document.getElementById("mhv2-overlay").classList.add("osekai__medal-popup-closed")
    }
}

