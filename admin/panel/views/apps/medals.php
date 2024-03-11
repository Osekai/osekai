<div class="basic-page-split">
    <div class="basic-page-sidebar">
        <div class="basic-page-querybars">
            <div class="basic-page-querybar">
                <div>
                    <p>group by</p>
                    <div class="basic-page-dropdown-container">
                        <div class="basic-page-dropdown-button-inner basic-page-dropdown-opener" onclick="openMedalsDropdown('dropdown-groupby')">
                            <p id="medals__group-by-text">Categories<i class="fas fa-chevron-down basic-page-dropdown-chevron"></i></p>

                        </div>
                        <div class="basic-page-dropdown basic-page-dropdown-hidden" id="dropdown-groupby">
                            <div class="dropdown-item" onclick="groupMedals('none')">None</div>
                            <div class="dropdown-item dropdown-item-active" onclick="groupMedals('categories')">Categories</div>
                        </div>
                    </div>
                </div>
                <div>
                    <p>order by</p>
                    <div class="basic-page-dropdown-container">
                        <div class="basic-page-dropdown-button-inner basic-page-dropdown-opener" onclick="openMedalsDropdown(`dropdown-sortby`)">
                            <p id="medals__order-by-text">Default<i class="fas fa-chevron-down basic-page-dropdown-chevron"></i></p>
                        </div>
                        <div class="basic-page-dropdown basic-page-dropdown-hidden" id="dropdown-sortby">
                            <div class="dropdown-item" onclick="sortMedals('alpha')">A-Z <i class="fas fa-arrow-down"></i></div>
                            <div class="dropdown-item" onclick="sortMedals('alpha-inverse')">A-Z <i class="fas fa-arrow-up"></i></div>
                            <div class="dropdown-item" onclick="sortMedals('id')">Medal ID <i class="fas fa-arrow-down"></i></div>
                            <div class="dropdown-item" onclick="sortMedals('id-inverse')">Medal ID <i class="fas fa-arrow-up"></i></div>
                            <div class="dropdown-item" onclick="sortMedals('rarity')">Rarity <i class="fas fa-arrow-down"></i></div>
                            <div class="dropdown-item" onclick="sortMedals('rarity-inverse')">Rarity <i class="fas fa-arrow-up"></i></div>
                            <div class="dropdown-item dropdown-item-active" onclick="sortMedals('default')">Default</div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="basic-page-querybar">
                <input type="text" id="medals__searchbar" placeholder="search here" class="query-input">
            </div>
        </div>
        <div class="basic-page-item-list">
            <div id="medals__none-list" class="hidden"></div>
            <!-- <h2 collapsible-button>Hush-Hush</h2>
            <div id="medals__hush-hush-list" class="collapsible-list open"></div>
            <h2 collapsible-button>Skill</h2>
            <div id="medals__skill-list" class="collapsible-list"></div>
            <h2 collapsible-button>Dedication</h2>
            <div id="medals__dedication-list" class="collapsible-list"></div>
            <h2 collapsible-button>Beatmap Packs</h2>
            <div id="medals__beatmap-packs-list" class="collapsible-list"></div>
            <h2 collapsible-button>Seasonal Spotlights</h2>
            <div id="medals__seasonal-spotlights-list" class="collapsible-list"></div>
            <h2 collapsible-button>Beatmap Spotlights</h2>
            <div id="medals__beatmap-spotlights-list" class="collapsible-list"></div>
            <h2 collapsible-button>Mod Introduction</h2>
            <div id="medals__mod-introduction-list" class="collapsible-list"></div>
            <h2 collapsible-button>Beatmap Challenge Packs</h2>
            <div id="medals__beatmap-challenge-packs-list" class="collapsible-list"></div> -->
        </div>
    </div>
    <div class="basic-page-inner">
        <div class="medals__medal-info hidden">
            <img src="" alt="">
            <div class="medals__medal-info-text">
                <h1 class="medals__medal-info-title"></h1>
                <p class="medals__medal-info-description text-gray"></p>
                <p class="medals__medal-info-solution"></p>
            </div>
        </div>
        <div class="basic-page-tabs">
            <a href="#" id="medals__beatmaps-tab" class="basic-page-tab" onclick="openMedalsTab(MedalPages.Beatmaps)">Beatmaps</a>
            <a href="#" id="medals__details-tab" class="basic-page-tab basic-page-tab-active" onclick="openMedalsTab(MedalPages.Details)">Details</a>
        </div>
        <div class="basic-page-content basic-page-inner-content basic-page-inner-content-shown" id="medals__medal-details-content">
            <div class="medals__medal-details">
                <section>
                    <h1>Base Details</h1>
                    <p>Solution</p>
                    <textarea class="input" id="medals__medal-solution-textarea"></textarea>
                    <p>Video</p>
                    <input type="text" class="input input-pattern" placeholder="None" id="medals__medal-solution-video" pattern="[0-9A-z-_]{11}">
                </section>
                <section>
                    <h1>Mods</h1>
                    <div class="medals__modswitches"></div>
                </section>
                <section>
                    <h1>Extra Info</h1>
                    <p>Addition Date</p>
                    <input type="date" class="input" placeholder="" id="medals__addition-date">
                    <div class="medals__first-achieved">
                        <div class="medals__first-achieved-date-form">
                            <p>Date First Achieved</p>
                            <input type="date" class="input" placeholder="" id="medals__first-achieved-date">
                        </div>
                        <div class="medals__first-achieved-user-form">
                            <p>First Achieved By</p>
                            <input type="text" class="input input-pattern" placeholder="None" id="medals__first-achieved-user" pattern="[0-9]{0,8}">
                        </div>
                    </div>
                </section>
            </div>
            <div class="medals__medal-beatmap-options">
                <h1>Beatmap Options</h1>
                <label for="medals__lock-submissions" class="checkbox" id="medals__lock-submissions-label">
                    <input type="checkbox" id="medals__lock-submissions">Lock Submissions
                </label>
                <label for="medals__beatmap-packs" class="checkbox">
                    <input type="checkbox" id="medals__beatmap-packs">Beatmap Packs
                </label>
                <div class="medals__beatmap-packs-ids">
                    <p>osu!</p>
                    <input type="text" name="" id="medals__beatmap-pack-osu" class="input input-pattern" pattern="([A-Z]+)?[0-9]{0,4}">
                    <p>osu!taiko</p>
                    <input type="text" name="" id="medals__beatmap-pack-taiko" class="input input-pattern" pattern="([A-Z]+)?[0-9]{0,4}">
                    <p>osu!catch</p>
                    <input type="text" name="" id="medals__beatmap-pack-catch" class="input input-pattern" pattern="([A-Z]+)?[0-9]{0,4}">
                    <p>osu!mania</p>
                    <input type="text" name="" id="medals__beatmap-pack-mania" class="input input-pattern" pattern="([A-Z]+)?[0-9]{0,4}">
                </div>
                <a href="#" id="medals__medals-link" class="button">View on Osekai Medals</a>
            </div>
        </div>
        <div class="basic-page-content basic-page-inner-content" id="medals__medal-beatmap-content">
            <div class="medals__live-beatmaps">
                <h1>Live</h1>
                <div class="medals__live-beatmaps-list">
                </div>
            </div>
            <div class="medals__deleted-beatmaps">
                <h1>Deleted</h1>
                <div class="medals__deleted-beatmaps-list">
                </div>
            </div>
        </div>
        <div class="basic-page-bottom-bar">
            <a id="medals__discard-button" class="button button-danger" onclick="revertMedalInformation()">Discard</a>
            <a id="medals__update-button" class="button" onclick="updateMedal()">Update</a>
        </div>
    </div>
    <div class="basic-page-notes">
        <notes-section></notes-section>
    </div>
</div>
