<link rel="stylesheet" href="<?= ROOT_URL ?>/global/css/search.css" type="text/css" />

<div class="search__overlay search__closed" id="searchOverlay">
    <div class="search__closeonclick" onclick="openSearch(document.getElementById('navbar_searchbut'))"></div>
    <div id="searchBar" class="search__bar">
        <i class="fa fa-search search__bar-icon"></i>
        <input id="searchInput" type="text" class="search__bar-input" placeholder="<?php echo GetStringRaw("navbar", "search.placeholder"); ?>" />
        <!-- <div class="search__bar-go"><i class="fa fa-chevron-right"></i></div> -->
    </div>
    <div class="search__results">
        <div id="search_loader" class="search__loader-container search__loader-container-closed">
            <svg viewBox='0 0 50 50' class='spinner'>
                <circle class='ring' cx='25' cy='25' r='22.5' />
                <circle class='line' cx='25' cy='25' r='22.5' />
            </svg>
        </div>
        <div class="search__results-container" id="search_results">
            <div class="search__results-row1">
                <div class="search__result-app">
                    <div class="search__result-title">
                        <img src="<?= ROOT_URL ?>/global/img/branding/vector/white/profiles.svg" alt="app icon" />
                        <p>Osekai <strong>Profiles</strong></p>
                    </div>
                    <div class="search__result-list" id="profilesResult">

                    </div>
                </div>
            </div>
            <div class="search__results-row2">
                <div class="search__result-app">
                    <div class="search__result-title">
                        <img src="<?= ROOT_URL ?>/global/img/branding/vector/white/medals.svg" alt="app icon" />
                        <p>Osekai <strong>Medals</strong></p>
                    </div>
                    <div class="search__result-list" id="medalsResult">

                    </div>
                </div>
                <div class="search__result-app">
                    <div class="search__result-title">
                        <img src="<?= ROOT_URL ?>/global/img/branding/vector/white/snapshots.svg" alt="app icon" />
                        <p>Osekai <strong>Snapshots</strong></p>
                    </div>
                    <div class="search__result-list" id="snapshotsResult">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= ROOT_URL ?>/global/js/search.js"></script>