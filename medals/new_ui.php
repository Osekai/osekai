<?php
$app = "medals";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<?php
echo '<style>
html{
    background-color: #000;
}
</style>';
echo $head;

if (isset($_GET['medal'])) {
    $colMedals = Database::execSelect("SELECT * FROM Medals WHERE `name` = ?", "s", array($_GET['medal']));

    $final_array = array();

    $ver;

    foreach ($colMedals as $medal) {
        $title = "The solution to the medal \"" . $medal['name'] . "\"!";
        $desc = $medal['description'];
        $keyword = $medal['name'];
        $keyword2 = $medal['grouping'];
        
        $meta = '<meta charset="utf-8" />
        <meta name="msapplication-TileColor" content="#ff66aa">
        <meta name="theme-color" content="#ff66aa">
        <meta property="og:image" content="' . $medal['link'] . '">
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="achivements,osekai,solutions,tricks,tips,osu,medal,medals,solution,solutions,osu!,osu!game,osugame,game,video game,award,achievement,' . $keyword . ',in,group,' . $keyword2 . '">';
    }
} else {
    $meta = '<meta charset="utf-8" />
    <meta name="description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <meta name="msapplication-TileColor" content="#ff66aa">
    <meta name="theme-color" content="#ff66aa">
    <meta property="og:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta property="og:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <meta name="twitter:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta name="twitter:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <title name="title">Osekai Medals • Need a medal solution? We have it!</title>
    <meta name="keywords" content="achivements,osekai,solutions,tricks,tips,osu,medal,medals,solution,solutions,osu!,osu!game,osugame,game,video game,award,achievement">
    <meta property="og:url" content="/medals" />';
}

?>

<!DOCTYPE html>
<html lang="en">

<head class="<?php echo $app; ?>">
    <!--<meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#ff66aa">
    <meta name="theme-color" content="#ff66aa">
    <meta name="description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <meta property="og:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta property="og:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <meta name="twitter:title" content="Osekai Medals • The solution to almost all of the osu! medals, right at your fingertips!" />
    <meta name="twitter:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <title name="title">Osekai Medals • Need a medal solution? We have it!</title>
    <meta name="keywords" content="achivements,osekai,solutions,tricks,tips,osu,medal,medals,solution,solutions,osu!,osu!game,osugame,game,video game,award,achievement ">
    <meta charset="utf-8">
    <meta property="og:url" content="/medals" />-->
    
    <?php echo $meta; ?>

    <?php
    css();
    font();
    dropdown_system();
    init3col();

    xhr_requests();
    notification_system();
    
    
    
    comments_system();
    report_system();
    mobileManager();
    tippy();
    ?>
</head>

<body>
    <div id="oBeatmapInput"></div>

    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <div class="osekai__3col-panels">
            <div id="osekai__col1" class="osekai__3col_col1">
                <div class="medals__scroller">
                    <div class="medals__search__area">
                        <div class="medals__search__bar">
                            <div class="medals__search">
                                <i class="fas fa-search"></i>
                                <input type="text" id="txtMedalSearch" class="medals__search-input" placeholder="search for a medal">
                            </div>
                            <div class="medals__search__filters-icon tooltip-v2" tooltip-content="Search Filters">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.4688 11.6875H5.3125V11.1562C5.3125 10.8641 5.07344 10.625 4.78125 10.625H3.71875C3.42656 10.625 3.1875 10.8641 3.1875 11.1562V11.6875H0.53125C0.239062 11.6875 0 11.9266 0 12.2188V13.2812C0 13.5734 0.239062 13.8125 0.53125 13.8125H3.1875V14.3438C3.1875 14.6359 3.42656 14.875 3.71875 14.875H4.78125C5.07344 14.875 5.3125 14.6359 5.3125 14.3438V13.8125H16.4688C16.7609 13.8125 17 13.5734 17 13.2812V12.2188C17 11.9266 16.7609 11.6875 16.4688 11.6875ZM16.4688 6.375H13.8125V5.84375C13.8125 5.55156 13.5734 5.3125 13.2812 5.3125H12.2188C11.9266 5.3125 11.6875 5.55156 11.6875 5.84375V6.375H0.53125C0.239062 6.375 0 6.61406 0 6.90625V7.96875C0 8.26094 0.239062 8.5 0.53125 8.5H11.6875V9.03125C11.6875 9.32344 11.9266 9.5625 12.2188 9.5625H13.2812C13.5734 9.5625 13.8125 9.32344 13.8125 9.03125V8.5H16.4688C16.7609 8.5 17 8.26094 17 7.96875V6.90625C17 6.61406 16.7609 6.375 16.4688 6.375ZM16.4688 1.0625H9.5625V0.53125C9.5625 0.239062 9.32344 0 9.03125 0H7.96875C7.67656 0 7.4375 0.239062 7.4375 0.53125V1.0625H0.53125C0.239062 1.0625 0 1.30156 0 1.59375V2.65625C0 2.94844 0.239062 3.1875 0.53125 3.1875H7.4375V3.71875C7.4375 4.01094 7.67656 4.25 7.96875 4.25H9.03125C9.32344 4.25 9.5625 4.01094 9.5625 3.71875V3.1875H16.4688C16.7609 3.1875 17 2.94844 17 2.65625V1.59375C17 1.30156 16.7609 1.0625 16.4688 1.0625Z" fill="white" />
                                </svg>
                            </div>
                        </div>
                        <div class="osekai__panel medals__search__filters">
                            <div class="osekai__panel-header">
                                <p>Filtering options</p>
                            </div>
                            <div class="osekai__panel-inner">
                                <div id="osekai__panel-disabled" class="" tooltip-content="Log in to use this feature">
                                    <div class="osekai__flex_row osekai__fr_centered">
                                        <input class="osekai__checkbox" id="styled-checkbox-1" type="checkbox" value="value1">
                                        <label for="styled-checkbox-1"></label>
                                        <p class="osekai__checkbox-label">Unobtained medals</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section id="oMedalSection"></section>
                </div>
            </div>
            <div class="osekai__3col_col1_spacer"></div>
            <div id="osekai__col__right" class="osekai__3col_right">
                <div class="osekai__3col_col2">
                    <section class="osekai__panel">
                        <div class="medals__info osekai__panel-header" id="medal__info">
                            <div class="medals__info-background">
                                <div class="medals__info-background-overlay"></div>
                                <img selector="oMedalIcon" class="medals__info-background-medal" src="/medals/img/unknown_medal.png">
                            </div>
                            <img selector="oMedalIcon" class="osekai__mi-icon osekai__mi-icon-glow" src="/medals/img/unknown_medal.png">
                            <img selector="oMedalIcon" class="osekai__mi-icon" src="/medals/img/unknown_medal.png">
                            <div class="osekai__mi-text">
                                <h1 id="strMedalTitle" class="osekai__mit-title">We're loading this medal!</h1>
                                <p id="strMedalDesc" class="osekai__mit-desc">Description</p>
                                <div id="colMods" class="osekai__mi-mods"></div>
                            </div>
                        </div>
                        <div class="osekai__panel-inner medals__info-sol">
                            <div class="medals__solution">
                                <p id="strMedalHeader" class="medals__sol-header"><img id="gamemodeImg" class="medals__sol-gamemode" src="<?= ROOT_URL ?>/global/img/gamemodes/standard.svg">Solution</p>
                                <p id="strMedalSolution" class="medals__sol-solution">Give us a moment...</p>
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <div class="osekai__panel-header-with-buttons">
                            <div class="osekai__panel-hwb-left">
                                <p>Comments</p>
                            </div>
                            <div class="osekai__panel-hwb-right">
                                <div class="osekai__panel-header-button" id="filter__button">
                                    <p class="osekai__panel-header-dropdown-text" id="filter__selected">
                                        Votes
                                    </p>
                                    <i class="fas fa-chevron-down osekai__panel-header-dropdown-icon"></i>
                                    <div class="osekai__dropdown osekai__dropdown-hidden" id="filter__list">
                                        <div class="osekai__dropdown-item" id="filter__votes">
                                            Votes
                                        </div>
                                        <div class="osekai__dropdown-item" id="filter__date">
                                            Most Recent
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="comments__area">
                                <div class="comments__post-box">
                                    <img src="<?php echo getpfp(); ?>" class="comments__pb-user-pfp">
                                    <div class="comments__input-box <?php if (!isset($_SESSION['osu'])) { echo 'osekai__input-disabled'; } else { echo ''; } ?>")">
                                        <div class="comments__input-box-textarea">
                                            <textarea style="overflow:hidden" id="comments__input" class="comments__input-box__text" rows="1"></textarea>
                                        </div>
                                        <button id="comments__send" class="comments__input-box__send">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="osekai__divider">
                                </div>
                                <div>
                                    <div class="comments__main-comment-area" id="comments__box">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="osekai__3col_col3">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header-with-buttons" id="AddMapPanel">
                            <div class="osekai__panel-hwb-left">
                                Beatmaps <p id="beatmap_count" class="medals__bm-count">0</p>
                            </div>
                        </div>
                        <div id="oBeatmapContainer" class="osekai__panel-inner">
                        </div>
                        <div id="oBeatmapContainer_GetFromOsu" class="osekai__panel-inner hidden">
                            <a class="osekai__button osekai__button-wide" id="oBeatmapContainer_GetFromOsu_Button" href="">
                                <!-- View beatmap pack on <strong>osu.ppy.sh</strong> -->
                                <i class="fas fa-external-link-alt"></i> View beatmap pack on <strong>osu.ppy.sh</strong>
                            </a>
                        </div>
                    </section>
                    <?php if (isset($_SESSION['role'])) {
                        if ($_SESSION['role']['rights'] > 0) {
                    ?>
                            <section class="osekai__panel">
                                <div class="osekai__panel-header">
                                    Edit this medal
                                </div>
                                <div class="osekai__panel-nav">
                                    <p>The editing feature is currently in beta, and features are limited. Features will be added as we need them! Keep in mind this feature is only available to admins.</p>
                                </div>
                                <div class="osekai__panel-inner">
                                    <p class="osekai__h2">Medal Info</p>
                                    <div class="osekai__edit__panel">
                                        <p class="osekai__h3">Description</p>
                                        <textarea id="solution__editor" onkeyup="textAreaAdjust(this)" class="osekai__input osekai__100" rows="1"></textarea>
                                        <div class="osekai__divider"></div>
                                        <p class="osekai__h3">Mods</p><input type="text" id="solution__mods" class="osekai__input osekai__100">
                                        <div class="osekai__divider"></div>
                                        <p class="osekai__h3">Pack ID</p><input type="text" id="solution__packid" class="osekai__input osekai__100">
                                    </div>
                                    <div class="osekai__flex_row osekai__100">
                                        <div onclick="updateSolution();" id="solution__save" class="osekai__button osekai__right">Save</div>
                                    </div>
                                </div>
                            </section>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=1.0.2"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>