<div class="osekai__panel-container" id="versionlisting">
    Welcoem to version listing :)
    <p onclick="goToPage('home')">Go to home</p>
    <div class="osekai__1col-panels">
        <div class="osekai__1col_col1">
            <section class="osekai__panel">
                <div class="osekai__panel-header-with-buttons">
                    <div class="osekai__panel-hwb-left">
                        <p id="title">Versons</p>
                    </div>
                    <div class="osekai__panel-hwb-right">
                        <div class="osekai__panel-header-button" id="sort" onclick="openSortDropdown()">
                            <p class="osekai__panel-header-dropdown-text" id="sort_activeItem">Release Date</p>
                            <i class="fas fa-chevron-down osekai__panel-header-dropdown-icon" aria-hidden="true"></i>
                            <div class="osekai__dropdown osekai__dropdown-hidden" id="sort_items">
                                <div class="osekai__dropdown-item" onclick="changeSorting('awarded_at_asc')" id="sort_awarded_at_asc">Release Date</div>
                                <div class="osekai__dropdown-item" onclick="changeSorting('awarded_at_asc')" id="sort_awarded_at_asc">Archival Date</div>
                                <div class="osekai__dropdown-item" onclick="changeSorting('awarded_at_asc')" id="sort_awarded_at_asc">Views</div>
                                <div class="osekai__dropdown-item" onclick="changeSorting('awarded_at_asc')" id="sort_awarded_at_asc">Downloads</div>
                            </div>
                        </div>
                        <div class="osekai__panel-header-viewtypes osekai__panel-header-viewtypes-size">
                            <!-- "grid_large", "list_2wide", "list_1wide", "ultra_compact" -->
                            <div tooltip="Grid" class="tooltip osekai__panel-header-viewtype osekai__panel-header-viewtype-active" onclick="changeViewtype('grid_large')" id="viewtype-grid_large">
                                <i class="fas fa-grip-horizontal" aria-hidden="true"></i>
                            </div>
                            <div tooltip="Compact List" class="tooltip osekai__panel-header-viewtype desktop" onclick="changeViewtype('list_2wide')" id="viewtype-list_2wide">
                                <i class="fas fa-th-list" aria-hidden="true"></i>
                            </div>
                            <div tooltip="Ultra Compact" class="tooltip osekai__panel-header-viewtype" onclick="changeViewtype('ultra_compact')" id="viewtype-ultra_compact">
                                <i class="fas fa-box" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="osekai__panel-header-input">
                            <i class="fas fa-search osekai__panel-header-button-icon" aria-hidden="true"></i>
                            <p class="osekai__panel-header-button-text">
                                <label class="osekai__panel-header-input__sizer">
                                    <input id="search" type="text" size="14" placeholder="search for a version!" maxlength="40">
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="osekai__panel-inner-withsidebar">
                    <div class="osekai__panel-sidebar">
                        <div class="osekai__panel-sidebar-inner" id="versionlisting_sidebar">
                            <div class="azelia__sidebar-section">
                                <h1 class="azelia__sidebar-title azelia__sidebar-title-active">osu!stable</h1>
                                <div class="azelia__sidebar-versions">
                                    <div class="azelia__sidebar-version azelia__sidebar-version-active">
                                        <p class="azelia__sidebar-version-year">2019</p>
                                        <p class="azelia__sidebar-version-count"><strong>12</strong> versions</p>
                                    </div>
                                    <div class="azelia__sidebar-version">
                                        <p class="azelia__sidebar-version-year">2019</p>
                                        <p class="azelia__sidebar-version-count"><strong>12</strong> versions</p>
                                    </div>
                                </div>
                            </div>
                            <h1 class="azelia__sidebar-title">osu!stable</h1>
                        </div>
                    </div>
                    <div class="osekai__panel-inner">
                        <div id="versionlisting_content" class="azelia__versions-listing">
                            <div class="azelia__versions-listing-group">
                                <div class="azelia__versions-listing-group-header">
                                    <h1><strong>osu!</strong>stable</h1>
                                    <h3><strong>150</strong> versions</h3>
                                </div>
                                <div class="azelia__versions-listing-group-year">
                                    <div class="azelia__versions-listing-group-year-top">
                                        <div class="azelia__versions-listing-group-year-left">
                                            <h2>2022</h2>
                                        </div>
                                        <div class="azelia__versions-listing-group-year-right">
                                            <h3>50 versions</h3>
                                            <p>from 20 archivers</p>
                                        </div>
                                    </div>
                                    <div class="azelia__versions-listing-group-grid grid">
                                        <?php for ($x = 0; $x < 10; $x++) { ?>
                                            <div class="azelia__version-grid">
                                                <img src="/snapshots/versions/b20220222beta/b20220222beta_1.jpg">
                                                <div class="azelia__version-grid-info">
                                                    <p class="azelia__version-grid-info-title">b20211112</p>
                                                    <p class="azelia__version-grid-info-releasedate">released <strong>21st December 2020</strong></p>
                                                    <p class="azelia__version-grid-info-archiver">archived by <strong>someone</strong></p>
                                                </div>
                                                <div class="azelia__version-grid-stats">
                                                    <p class="azelia__version-grid-stats-views">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                        <span>0</span>
                                                    </p>
                                                    <p class="azelia__version-grid-stats-downloads">
                                                        <i class="fas fa-download" aria-hidden="true"></i>
                                                        <span>0</span>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>