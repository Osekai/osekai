<?php
$app = "medals";
include_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<?php
echo '<style>
html{
    background-color: #000;
}
</style>';

//$preloadimg = Database::execSimpleSelect("SELECT link FROM Medals");
//foreach ($preloadimg as $a) {
// echo '<link rel="preload" as="image" href="' . $a['link'] . '">';
// basically preloads all the images so that on browsers they load in more smoothly (loaded after js and before content opens i think?)
//}

// yeah this doesn't work anymore
// $nDefaultHash = 41;
// if (isset($_GET['medal'])) {
//     $colMedalOldLink = Database::execSelect("SELECT medalid FROM Medals WHERE name = ?", "s", array($_GET['medal']));
//     $nDefaultHash = intval($colMedalOldLink[0]['medalid']);
// }

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
    <meta property="og:url" content="https://www.osekai.net/snapshots" />';
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
    <meta property="og:url" content="https://www.osekai.net/medals" />-->

    <?php echo $meta; ?>

    <script type="text/javascript">
        //const nDefaultHash = <?php echo $nDefaultHash; ?>;
        const nUserID = <?php if (isset($_SESSION['osu'])) {
                            echo $_SESSION['osu']['id'];
                        } else {
                            echo "-1";
                        } ?>;
        const nRights = <?php if (isset($_SESSION['role'])) {
                            echo $_SESSION['role']['rights'];
                        } else {
                            echo "-1";
                        } ?>;
        const strRole = <?php if (isset($_SESSION['role'])) {
                            echo "'" . $_SESSION['role']['name'] . "'";
                        } else {
                            echo "''";
                        } ?>;
        const strRootUrl = <?php echo "'" . $rooturl . "'"; ?>;
    </script>
    <?php
    css();
    font();
    dropdown_system();
    init3col();

    xhr_requests();
    notification_system();
    user_hover_system();
    medal_hover_system();
    tooltip_system();
    comments_system();
    report_system();
    mobileManager();

    fontawesome();
    ?>
</head>

<body>
    <div id="oBeatmapInput"></div>

    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <div class="osekai__3col-panels">
            <div class="osekai__3col_col1">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>stuffz</p>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <a class="osekai__button">cool button</a>
                        <a class="osekai__button" onclick="testDialog()">Dialog Test</a>
                        <p id="testdialog_text">you clicked...</p>
                        <br>
                        <a class="osekai__button" onclick="reportSys(0, 69420)">Report an issue with this beatmap</a>
                        <a class="osekai__button" onclick="reportSys(1, 69420)">Report an issue with this comment</a>
                        <a class="osekai__button" onclick="reportSys(2, 69420)">Report an bug on this page</a>
                        <div class="osekai__flex_row osekai__fr_centered">
                            <input class="osekai__checkbox" id="styled-checkbox-1" type="checkbox" value="value1">
                            <label for="styled-checkbox-1"></label>
                            <p class="osekai__checkbox-label">Test Checkbox</p>
                        </div>
                        <input placeholder="input" class="osekai__input osekai__fullwidth" type="text">
                    </div>
                </section>
            </div>
            <div class="osekai__3col_col1_spacer"></div>
            <div class="osekai__3col_right">
                <div class="osekai__3col_col2">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>New Beatmap Panel</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="medals__bmp3-panel-outer">
                                <div class="medals__bmp3-panel" style="background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%), url(https://assets.ppy.sh/beatmaps/107009/covers/cover.jpg);">
                                    <a class="medals__bmp3-top" href="https://osu.ppy.sh/beatmapsets/107009#osu/286489">
                                        <div class="medals__bmp3-top-left">
                                            <p class="medals__bmp3-tl-bmname">Relic Song</p>
                                            <p class="medals__bmp3-tl-artist">by <span class="medals__bmp3-bold">Shouta Kageyama</span></p>
                                        </div>
                                        <div class="medals__bmp3-top-right">
                                            <p class="medals__bmp3-tr-difficulty">Irreversible</p>
                                            <p class="medals__bmp3-tr-mapper">mapped by <span class="medals__bmp3-bold user_hover_v2" userid="631530">Leader</span></p>
                                        </div>
                                    </a>
                                    <div class="medals__bmp3-bottom">
                                        <p class="medals__bmp3-submitter">submitted <span class="medals__bmp3-bold tooltip" tooltip="Sun Jul 05 2020">about a year ago</span></p>
                                        <div class="medals__bmp3-right" id="subcontainer_286489">
                                            <div class="medals__bmp3-r-note" onclick="notePanel(286489, true);">
                                                add a note<i class="fas fa-sticky-note medals__bmp3-r-note-icon" aria-hidden="true"></i>
                                            </div>
                                            <div id="1220" onclick="vote(1220);" class="medals__bmp3-r-vote medals__bmp3-r-vote-voted">+9</div>
                                        </div>
                                    </div>

                                </div>
                                <div class="medals__bmp3-hover-area">
                                    <div class="medals__bmp3-hover-button">
                                        download with<strong> osu!direct</strong>
                                    </div>
                                    <div class="medals__bmp3-hover-button">
                                        report this beatmap
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="osekai__3col_col3">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>Mobile?</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <a class="medals__bmp3_mobile" style="background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%), url(https://assets.ppy.sh/beatmaps/107009/covers/cover.jpg);">
                                <div class="medals__bmp3_mobile-inner">
                                    <p class="medals__bmp3_mobile-title">Relic Song</p>
                                    <p class="medals__bmp3_mobile-artist">by <strong>Shouta Kageyama</strong></p>
                                    <p class="medals__bmp3_mobile-difficulty">Irreversable</p>
                                    <p class="medals__bmp3_mobile-mapper">mapped by <strong>Leader</strong></p>
                                    <p class="medals__bmp3_mobile-submission-date">submitted <strong>about a year ago</strong></p>
                                </div>
                                <div class="medals__bmp3_mobile-buttons">
                                    <div class="medals__bmp3_mobile-vote">+9</div>
                                    <div class="medals__bmp3_mobile-report"><i class="fas fa-exclamation-triangle"></i></div>
                                </div>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>