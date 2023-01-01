<?php
$app = "profiles";
$manual_frontend = true;

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (isset($_GET['user'])) {
    //$colBadges = Database::execSelect("SELECT * FROM Badges where id = ?", "i", array($_GET['badge']));
    //include("../global/php/osu_api_functions.php");
    // we can cache this like forever
    $cache = Caching::getCache("profiles_meta_" . $_GET['user']);
    $user = "";
    if ($cache != null) {
        $user = $cache;
    } else {
        $user = v2_getUser($_GET['user']);

        if (isset($user)) {
            Caching::saveCache("profiles_meta_" . $_GET['user'], 172800, $user);
        }
    }

    if (isset($user)) {
        $user = json_decode($user, true);

        if($_GET['user'] != $user['id']) {
            // redirects from username, so you can go to https://osekai.net/profiles?user=Tanza3D and it'll auto-redirect
            redirect("/profiles?user=" . $user['id']);
            exit;
        }

        $user_id = $user['id'];
        $user_name = $user['username'];

        $title = "Osekai Profiles • " . $user_name;
        $desc = "Check out the Osekai Profiles page for " . $user_name . "! Including stats, medals, goals, timeline, and more!";
        $keyword = $user_name;
        $keyword2 = "osekai profiles";

        $meta = '<meta charset="utf-8" />
        <meta name="msapplication-TileColor" content="#303f5e">
        <meta name="theme-color" content="#303f5e">
        <meta property="og:image" content="https://a.ppy.sh/' . $user_id . '" />
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,profile,user_profile,' . $keyword . ',' . $keyword2 . ',graph,chart,goals">';
    } else {
        http_response_code(404);
        frontend();
        include($_SERVER['DOCUMENT_ROOT'] . "/404/index.php");
        exit;
    }
} else {
    $title = "Osekai Profiles • Home";
    // ! temporary description
    $desc = "Check out Osekai Profiles! Featuring stats, medals, goals, timeline, and more, for every single osu! user!";

    $meta = '<meta charset="utf-8" />
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta name="msapplication-TileColor" content="#303f5e">
        <meta name="theme-color" content="#303f5e">
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,profile,user_profile,badges,graph,chart,goals">
        <meta property="og:url" content="/profiles" />';
}
frontend();
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />



<head>
    <?php

    echo $meta;
    echo $head;

    font();
    css();
    dropdown_system();
    mobileManager();

    xhr_requests();
    osu_api();
    user_hover_system();
    medal_popup_v2();
    tooltip_system();
    report_system();
    notification_system();
    comments_system();
    fontawesome();

    tippy();

    chart_js();

    colour_picker();
    ?>
</head>

<body>
    <div id="oTimeline"></div>

    <?php navbar(); ?>
    <div class="osekai__panel-container profiles__home hidden" id="home">
        <div class="osekai__2col-panels">
            <div class="osekai__2col_col1">
                <?php print_home_panel(); ?>
                <?php if (loggedin()) { ?>
                    <div class="profiles__sidebar-button" onclick="loadCurrentUser();">
                        <img class="profiles__sb-pfp" src="https://a.ppy.sh/<?= $_SESSION['osu']['id']; ?>">
                        <img class="profiles__sb-pfp profiles__sb-pfp-outline" src="https://a.ppy.sh/<?= $_SESSION['osu']['id']; ?>">
                        <img class="profiles__sb-background" src="https://a.ppy.sh/<?= $_SESSION['osu']['id']; ?>">
                        <div class="texts">
                            <p><?= GetStringRaw("profiles", "home.yourProfile.title"); ?></p>
                            <h1><?= GetStringRaw("profiles", "home.yourProfile.subtitle"); ?></h1>
                        </div>
                    </div>
                <?php } ?>
                <section class="osekai__panel" style="margin-top: 25px;">
                    <div class="osekai__panel-header">
                        <i class="fas fa-eye"></i>
                        <p>
                            <?php if (loggedin()) echo GetStringRaw("profiles", "home.recentlyViewed.title");
                            else echo GetStringRaw("profiles", "home.mostViewed.title"); ?>
                        </p>
                    </div>
                    <div class="osekai__panel-inner profiles__ranking" id="recentlyviewed">
                        <!-- <div class="profiles__ranking-user" onclick="loadUser(4637369);"><img src="https://a.ppy.sh/4637369" class="profiles__ranking-pfp">
                            <div class="profiles__ranking-texts">
                                <p class="profiles__ranking-username">?</p>
                            </div>
                            <p class="profiles__ranking-rank"><span>#</span>?</p>
                        </div> -->
                    </div>
                </section>
            </div>
            <div class="osekai__2col_col2">
                <section class="osekai__panel">
                    <div class="osekai__panel-header-with-buttons">
                        <div class="osekai__panel-hwb-left">
                            <i class="fas fa-chart-line"></i>
                            <p><?= GetStringRaw("profiles", "home.globalRankings.title"); ?></p>
                        </div>
                        <div id="mode__list__home" class="osekai__panel-hwb-right" style="padding-right: 7px;">
                            <img mode="all" tooltip-content="all mode" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/all.svg">
                            <img mode="osu" tooltip-content="standard" class="profiles__gamemode-button tooltip-v2 profiles__gamemode-button-active" src="/global/img/gamemodes/standard.svg">
                            <img mode="taiko" tooltip-content="taiko" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/taiko.svg">
                            <img mode="fruits" tooltip-content="catch" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/fruits.svg">
                            <img mode="mania" tooltip-content="mania" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/mania.svg">
                        </div>
                    </div>
                    <div id="profiles__ranking" class="osekai__panel-inner profiles__ranking">
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="osekai__panel-container hidden" id="profile">
        <div class="profiles__top-bar">
            <div class="profiles__top-bar-home" onclick="loadHome();">
                <i class="fas fa-home"></i>
            </div>
            <div class="profiles__user-bar">
                <div class="profiles__user-bar-user">
                    <img class="profiles__user-bar-pfp" src="https://a.ppy.sh/2" selector="pfp">
                    <p class="profiles__user-bar-name" id="name__main"></p>
                </div>
                <div id="mode__list" class="osekai__left osekai__center-flex-row">
                    <img mode="all" tooltip-content="all mode" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/all.svg">
                    <img mode="osu" tooltip-content="standard" class="profiles__gamemode-button tooltip-v2 profiles__gamemode-button-active" src="/global/img/gamemodes/standard.svg">
                    <img mode="taiko" tooltip-content="taiko" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/taiko.svg">
                    <img mode="fruits" tooltip-content="catch" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/fruits.svg">
                    <img mode="mania" tooltip-content="mania" class="profiles__gamemode-button tooltip-v2" src="/global/img/gamemodes/mania.svg">
                </div>
            </div>
        </div>
        <div class="osekai__2col-panels">
            <div class="osekai__2col_col1">
                <section class="osekai__panel profiles__cover">
                    <div class="profiles__cover-top">
                        <div id="cover__img" class="profiles__cover-banner">
                        </div>
                        <div class="profiles__cover-userinfo">
                            <img selector="cover_blur_img" class="osekai__pfp-blur-bg">
                            <div class="profiles__cover-userinfo-inner">
                                <a tooltip-content="View profile on osu.ppy.sh" id="osu_link" target="_blank" class="profiles__cover-link tooltip-v2"><i class="fas fa-external-link-alt"></i></a>
                                <img class="profiles__cover-info-pfp" src="https://a.ppy.sh/1" selector="pfp">
                                <div class="profiles__cover-info-content">
                                    <div id="name__sub" class="profiles__cover-info-sub"><?= GetStringRaw("general", "loading.longer"); ?></div>
                                    <h2 id="current__rank__global">Global #?</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profiles__cover-bottom">
                        <img selector="cover_blur_img" class="osekai__pfp-blur-bg">
                        <div class="profiles__cover-bottom-inner">
                            <div id="bar__social" class="profiles__cover-bottom-row">
                                <div class="profiles__cover-bottom_text">
                                    <p><i class="fab fa-discord"></i><a target="_blank" id="discord"><?= GetStringRaw("general", "loading.longer"); ?></a></p>
                                </div>
                                <div class="profiles__cover-bottom_text">
                                    <p><i class="fab fa-twitter"></i><a target="_blank" id="twitter"><?= GetStringRaw("general", "loading.longer"); ?></a></p>
                                </div>
                                <div class="profiles__cover-bottom_text">
                                    <p><i class="fas fa-link"></i><a target="_blank" id="website"><?= GetStringRaw("general", "loading.longer"); ?></a></p>
                                </div>
                            </div>
                            <div class="profiles__cover-bottom-row">
                                <div class="profiles__cover-bottom_text">
                                    <p id="location"><i class="fas fa-map-marker-alt"></i> <?= GetStringRaw("general", "loading.longer"); ?></p>
                                </div>
                                <div class="profiles__cover-bottom_text">
                                    <p id="arrival__date"><span class="light">joined</span> <?= GetStringRaw("general", "loading.longer"); ?></p>
                                </div>
                                <div class="profiles__cover-bottom_text">
                                    <p id="hardware"><span class="light">plays with</span> <?= GetStringRaw("general", "loading.longer"); ?></p>
                                </div>
                            </div>
                            <div id="bar__interests" class="profiles__cover-bottom-row">
                                <div class="profiles__cover-bottom_text">
                                    <p id="interests"><i class="fas fa-heart"></i> <?= GetStringRaw("general", "loading.longer"); ?></p>
                                </div>
                                <div class="profiles__cover-bottom_text">
                                    <p id="occupation"><i class="fas fa-briefcase"></i> <?= GetStringRaw("general", "loading.longer"); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- <section class="osekai__panel">
                    <div class="osekai__panel-header-with-buttons">
                        <div class="osekai__panel-hwb-left">
                            Showcase
                        </div>
                        <div selector="user__control" class="osekai__panel-hwb-right">
                            <div class="osekai__panel-header-button">
                                <i class="fas fa-plus-circle osekai__panel-header-button-icon" aria-hidden="true"></i>
                                <p class="osekai__panel-header-button-text">Set/Change Showcase</p>
                            </div>
                        </div>
                    </div>
                    <div class="osekai__panel-inner">
                        This user has not set a showcase yet.
                    </div>
                </section> -->
                <!-- score panel, want to keep this -->
                <!--<section class="osekai__panel">
                    <div class="osekai__panel-header-with-buttons">
                        <div class="osekai__panel-hwb-left">
                            Featured Scores
                        </div>
                        <div selector="user__control" class="osekai__panel-hwb-right">
                            <div class="osekai__panel-header-button">
                                <i class="fas fa-plus-circle osekai__panel-header-button-icon" aria-hidden="true"></i>
                                <p class="osekai__panel-header-button-text">Add Beatmap</p>
                            </div>
                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <div class="osekai__score-panel" style="background-image: linear-gradient(#0008, #000a), url('https://assets.ppy.sh/beatmaps/28751/covers/cover.jpg');">
                            <div class="osekai__score-panel-top">
                                <div class="osekai__score-panel-left">
                                    <p class="osekai__sp-title">rog-unlimitation</p>
                                    <p class="osekai__sp-artist">by 07th Expansion</p>
                                    <img class="osekai__sp-gamemode" src="/global/img/gamemodes/standard.svg">
                                </div>
                                <div class="osekai__score-panel-right">
                                    <p class="osekai__sp-star"><i class="fas fa-star"></i> 5.29</p>
                                    <p class="osekai__sp-difficulty">AngelHoney</p>
                                    <p class="osekai__sp-mapper">mapped by <strong>AngelHoney</strong></p>
                                </div>
                            </div>
                            <div class="osekai__score-panel-bottom">
                                <div class="osekai__score-panel-left">
                                    <img class="osekai__sp-rank-icon" src="https://osu.ppy.sh/assets/images/GradeSmall-A.d785e824.svg">
                                    <p class="osekai__sp-score">11,596,868</p>
                                    <p class="osekai__sp-rank">#16,902 global</p>
                                    <div class="osekai__sp-mods">
                                        <img class="osekai__sp-mod" src="https://osu.ppy.sh/assets/images/mod_double-time.348a64d3.png">
                                    </div>
                                </div>
                                <div class="osekai__score-panel-right">
                                    <div class="osekai__sp-stack-row">
                                        <div class="osekai__sp-stack">
                                            <p class="osekai__sps-top">pp</p>
                                            <p class="osekai__sps-bottom">191</p>
                                        </div>
                                        <div class="osekai__sp-stack">
                                            <p class="osekai__sps-top">combo</p>
                                            <p class="osekai__sps-bottom">835x</p>
                                        </div>
                                        <div class="osekai__sp-stack">
                                            <p class="osekai__sps-top">accuracy</p>
                                            <p class="osekai__sps-bottom">90.34%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>-->
                <section class="osekai__panel">
                    <div id="AddEntryPanel" class="osekai__panel-header-with-buttons">
                        <div class="osekai__panel-hwb-left">
                            <i class="fas fa-stream"></i>
                            <p><?= GetStringRaw("profiles", "profile.timeline.title"); ?></p>

                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <div class="osekai__flex_row">
                            <p id="timeline__start">loading</p>
                            <p id="timeline__end" class="osekai__left">loading</p>
                        </div>
                        <div id="timeline__dots" class="profiles__timeline">
                            <div selector="timeline__dot" class="profiles__timeline-dot" style="--pos: 0%"></div>
                            <div selector="timeline__dot" class="profiles__timeline-dot" style="--pos: 100%"></div>
                        </div>
                        <div class="profiles__info-panel-container">
                            <div id="timeline__info" class="profiles__info-panel profiles__info-panel-closed">
                                <div id="timeline__header" class="profiles__info-panel-header">March 2001</div>
                                <div class="profiles__info-panel-row">
                                    <p>march <span>1st</span></p>
                                    <h3>joined osu!</h3>
                                    <div class="profiles__info-panel-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                </div>
                                <div class="profiles__info-panel-row">
                                    <p>march <span>1st</span></p>
                                    <h3>joined osu!</h3>
                                    <div class="profiles__info-panel-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="goals__section" class="osekai__panel">
                    <div class="osekai__panel-header">
                        <i class="fas fa-bullseye"></i>
                        <p><?= GetStringRaw("profiles", "profile.goals.title"); ?></p>

                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <div class="profiles__feature-introduction profiles__feature-introduction-goals hidden" id="goals__welcome">
                            <div class="profiles__feature-introduction-toprow">
                                <h1><?= GetStringRaw("profiles", "profile.goals.welcome.title"); ?></h1>
                                <p><?= GetStringRaw("profiles", "profile.goals.welcome.close"); ?></p>
                            </div>
                            <div class="profiles__feature-introduction-main">
                                <p><?= GetStringRaw("profiles", "profile.goals.welcome.body"); ?></p>
                            </div>
                        </div>
                        <div selector="user__control" class="profiles__goal-input">
                            <div id="goals__dropdown" class="profiles__goal-dropdown-content osekai__dropdown osekai__dropdown-hidden">
                                <p class="profiles__dropdown-header"><?= GetStringRaw("profiles", "profile.goals.goalType"); ?></p>
                                <div id="btn-goals__PP" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/pp.svg"> <?= GetStringRaw("profiles", "profile.goals.pp"); ?></div>
                                <div id="btn-goals__Rank" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/rank.svg"> <?= GetStringRaw("profiles", "profile.goals.rank"); ?></div>
                                <div id="btn-goals__Country Rank" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/rank.svg"> country rank</div>
                                <div id="btn-goals__Medals" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/medals.svg"> <?= GetStringRaw("profiles", "profile.goals.medals"); ?></div>
                                <div id="btn-goals__% Medals" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/medals.svg"> medal %</div>
                                <div id="btn-goals__SS Count" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/ss.svg"> <?= GetStringRaw("profiles", "profile.goals.ssCount"); ?></div>
                                <div id="btn-goals__Badges" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/badges.svg"> <?= GetStringRaw("profiles", "profile.goals.badges"); ?></div>
                                <div id="btn-goals__Level" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/level.svg"> <?= GetStringRaw("profiles", "profile.goals.level"); ?></div>
                                <div id="btn-goals__Ranked Score" class="osekai__dropdown-item profiles__goal-dropdown-button"><img src="img/goals/vector/level.svg"> ranked score</div>
                            </div>
                            <div id="goals__dropdown__button" class="profiles__goal-dropdown osekai__dropdown-opener">
                                <img id="current__goaltype" class="profiles__goal-type" src="img/goals/vector/medals.svg">
                                <i class="fas fa-caret-down"></i>
                            </div>

                            <input id="goal__input" type="number" onkeydown="javascript: return event.keyCode === 8 || event.keyCode === 46 ? true : !isNaN(Number(event.key))" placeholder="<?= GetStringRaw("profiles", "profile.goals.input.placeholder"); ?>" class="profiles__goal-input-amount">
                            <div id="goals__add__button" class="profiles__goal-add">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div id="goals__panel" class="osekai__flex-vertical-container osekai__100">
                            <!-- <div class="profiles__goal">
                                <div class="profiles__goal-container-main">
                                    <div class="osekai__progress-bar osekai__progress-bar-gold">
                                        <div class="osekai__progress-bar-inner" style="width: 100%;"></div>
                                    </div>
                                    <div class="profiles__goal-texts">
                                        <div class="profiles__goal-left">
                                            <div class="profiles__goal-large-text">
                                                <p>Reach 500pp</p>
                                            </div>
                                            <div class="profiles__goal-small-text">
                                                <p>2100pp (420%)</p>
                                            </div>
                                        </div>
                                        <div class="profiles__goal-right">
                                            <div class="profiles__goal-large-text">
                                                <p>started <strong>11 months ago</strong></p>
                                            </div>
                                            <div class="profiles__goal-small-text">
                                                <p>claimed <strong>8 months ago</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div selector="user__control" class="profiles__goal-container-buttons">
                                    <div class="profiles__goal-claim-button profiles__goal-button">
                                        <i class="fas fa-flag"></i>
                                    </div>
                                    <div class="profiles__goal-delete-button profiles__goal-button">
                                        <i class="far fa-times-circle"></i>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </section>
                <section class="osekai__panel" id="banner-panel">

                    <div class="osekai__panel-header">
                        <i class="fas fa-user"></i>
                        <p><?= GetStringRaw("profiles", "profile.banner.title"); ?></p>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">

                        <!-- <div class="profiles__userbanner-image">
                                <img src="/profiles/img/banner.svg?id=10379965&background=clubglows&foreground=medal-oriented" class="profiles__userbanner-image-src">
                                <div class="profiles__userbanner-image-loader">
                                    <svg viewBox="0 0 50 50" class="spinner">
                                        <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                                        <circle class="line" cx="25" cy="25" r="22.5"></circle>
                                    </svg>
                                </div>
                            </div> -->
                        <div class="profiles__userbanner">
                            <div class="profiles__banner-loading-overlay" id="banner-full-loader">
                                <svg viewBox="0 0 50 50" class="spinner">
                                    <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                                    <circle class="line" cx="25" cy="25" r="22.5"></circle>
                                </svg>
                            </div>
                            <div class="profiles__userbanner-top">
                                <div class="profiles__userbanner-top-toggle">
                                    <div id="banner-toggle-type_bbcode" class="profiles__userbanner-top-toggle-item" onclick="UserBanner.SwitchUrl('bbcode')">
                                        <?= GetStringRaw("profiles", "profile.banner.copyType.bbcode"); ?>
                                    </div>
                                    <div id="banner-toggle-type_raw" class="profiles__userbanner-top-toggle-item" onclick="UserBanner.SwitchUrl('raw')">
                                        <?= GetStringRaw("profiles", "profile.banner.copyType.raw"); ?>
                                    </div>
                                </div>
                                <div class="profiles__userbanner-top-text">
                                    <pre id="banner-copy-placeholder"><?= GetStringRaw("general", "loading.longer"); ?></pre>
                                </div>
                                <div class="profiles__userbanner-top-copy" onclick="UserBanner.CopyUrl();">
                                    <i class="fas fa-clipboard"></i>
                                </div>
                            </div>
                            <div class="profiles__userbanner-bottom-container">
                                <div class="profiles__userbanner-display">
                                    <div class="profiles__userbanner-image">
                                        <img class="profiles__userbanner-image-svg-container" src="" alt="" id="banner-image">
                                        <div class="profiles__userbanner-image-loader" id="banner-loader">
                                            <svg viewBox="0 0 50 50" class="spinner">
                                                <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                                                <circle class="line" cx="25" cy="25" r="22.5"></circle>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="profiles__userbanner-bottom">
                                    <div class="profiles__userbanner-bottom-top">
                                        <div class="profiles__dropdown-with-header" id="dropdown-section-backdrop">
                                            <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.backdropStyle.title"); ?></p>
                                            <div class="osekai__dropdown-button-inner osekai__dropdown-opener" onclick="UserBanner.OpenDropdown('banner-dropdown-background-style')">
                                                <p id="dropdown__themes-text">Club Glows</p>
                                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                            </div>
                                            <div class="osekai__dropdown osekai__dropdown-hidden" id="banner-dropdown-background-style">
                                            </div>
                                        </div>
                                        <div class="profiles__dropdown-with-header" id="dropdown-section-foreground">
                                            <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.foregroundStyle.title"); ?></p>
                                            <div class="osekai__dropdown-button-inner osekai__dropdown-opener" onclick="UserBanner.OpenDropdown('banner-dropdown-foreground-style')">
                                                <p id="dropdown__themes-text">Medal-Oriented</p>
                                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                            </div>
                                            <div class="osekai__dropdown osekai__dropdown-hidden" id="banner-dropdown-foreground-style">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="profiles__userbanner-bottom-bottom" id="custom-background-settings">
                                        <h1><?= GetStringRaw("profiles", "profile.banner.customBackground.title"); ?></h1>
                                        <div class="profiles__userbanner-bottom-right-controlbar">
                                            <div class="profiles__userbanner-bottom-right-controlbar-dropdown" id="dropdown-section-customstyle">
                                                <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.customBackground.style.title"); ?></p>
                                                <div class="osekai__dropdown-button-inner osekai__dropdown-opener" onclick="UserBanner.OpenDropdown('banner-dropdown-custom-style')">
                                                    <p id="dropdown__themes-text">Gradient</p>
                                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                                </div>
                                                <div class="osekai__dropdown osekai__dropdown-hidden" id="banner-dropdown-custom-style">
                                                </div>
                                            </div>
                                            <div class="profiles__gradient-picker" id="banner-gradient-picker">
                                                <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.customBackground.gradient"); ?></p>
                                                <div class="osekai__gradient-bar" id="colourbar">
                                                    <div class="osekai__gradient-bar-left"><input type="text"></input></div>
                                                    <div class="osekai__gradient-bar-bar"></div>
                                                    <div class="osekai__gradient-bar-right"><input type="text"></input></div>
                                                </div>
                                            </div>
                                            <div class="profiles__angle-picker" id="banner-angle-picker">
                                                <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.customBackground.angle"); ?></p>
                                                <div class="osekai__slider-with-input">
                                                    <div class="osekai__slider-input-container">
                                                        <input type="text" id="angle-input" value="0" class="osekai__input" onchange="UserBanner.UpdateAngle('input')">
                                                    </div>
                                                    <div class="osekai__slider-container">
                                                        <input type="range" min="0" max="360" value="0" class="osekai__slider" id="angle-slider" onchange="UserBanner.UpdateAngle('slider')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profiles__gradient-picker hidden" id="banner-solid-picker">
                                                <p class="profiles__userbanner-dropdown-header"><?= GetStringRaw("profiles", "profile.banner.customBackground.colour"); ?></p>
                                                <div class="osekai__colour-picker" id="colour-solid-picker">
                                                    <input type="text"></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="osekai__button osekai__button-solid profiles__custom-save-button" onclick="UserBanner.SaveSettings(true)">Save</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/commentsPanel.php"); ?>
            </div>
            <div class="osekai__2col_col2">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <i class="fas fa-chart-line"></i>
                        <p><?= GetStringRaw("profiles", "profile.stats.title"); ?></p>
                    </div>
                    <div class="profiles__osekai__panel-nav osekai__panel-nav osekai__flex_row">
                        <div class="profiles__rank-nav forcetooltip">
                            <?= GetStringRaw("profiles", "profile.stats.globalRank"); ?> <strong id="current__global__rank">?</strong>
                        </div>
                        <div class="profiles__rank-nav forcetooltip">
                            <?= GetStringRaw("profiles", "profile.stats.countryRank"); ?> <strong id="current__country__rank">?</strong>
                        </div>
                        <div class="osekai__left osekai__flex_row w-auto g-18">
                            <div class="profiles__rank-nav">
                                <?= GetStringRaw("profiles", "profile.stats.accuracy"); ?> <strong id="accuracy">?</strong>
                            </div>
                            <div class="profiles__rank-nav">
                                <?= GetStringRaw("profiles", "profile.stats.pp"); ?> <strong id="pp__count">?</strong>
                            </div>
                        </div>
                    </div>
                    <div id="stats__graph__area" class="profiles__graph-area">
                        <div id="stats__chart__wrapper" class="chartWrapper">
                            <canvas id="stats__chart"></canvas>
                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <div class="profiles__panel-row">
                            <div class="profiles__panel profiles__panel-maxwidth">
                                <p><?= GetStringRaw("profiles", "profile.stats.playtime"); ?> <strong id="play__time">?</strong></p>
                                <p><?= GetStringRaw("profiles", "profile.stats.plays"); ?> <strong id="play__count">?</strong></p>
                            </div>
                            <div class="profiles__panel profiles__panel-ranks">
                                <div class="profiles__vertical-rank">
                                    <img src="https://osu.ppy.sh/assets/images/GradeSmall-SS-Silver.6681366c.svg">
                                    <p id="ssh__count">?</p>
                                </div>
                                <div class="profiles__vertical-rank">
                                    <img src="https://osu.ppy.sh/assets/images/GradeSmall-SS.a21de890.svg">
                                    <p id="ss__count">?</p>
                                </div>
                                <div class="profiles__vertical-rank">
                                    <img src="https://osu.ppy.sh/assets/images/GradeSmall-S-Silver.811ae28c.svg">
                                    <p id="sh__count">?</p>
                                </div>
                                <div class="profiles__vertical-rank">
                                    <img src="https://osu.ppy.sh/assets/images/GradeSmall-S.3b4498a9.svg">
                                    <p id="s__count">?</p>
                                </div>
                                <div class="profiles__vertical-rank">
                                    <img src="https://osu.ppy.sh/assets/images/GradeSmall-A.d785e824.svg">
                                    <p id="a__count">?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <i class="oif-medal"></i>
                        <p><?= GetStringRaw("profiles", "profile.medals.title"); ?></p>
                    </div>
                    <div class="profiles__osekai__panel-nav osekai__panel-nav osekai__flex_row">
                        <div class="profiles__rank-nav">
                            <?= GetStringRaw("profiles", "profile.medals.medals"); ?> <strong id="medal__count">?</strong>
                        </div>
                        <div class="profiles__rank-nav">
                            <?= GetStringRaw("profiles", "profile.medals.rank"); ?> <strong id="medal__rank__global">?</strong>
                        </div>
                    </div>
                    <div id="medals__graph__area" class="profiles__graph-area">
                        <div id="medals__chart__wrapper" class="chartWrapper">
                            <canvas id="medals__chart"></canvas>
                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <div id="completion__bar__scheme" class="profiles__medalinfo-section">
                            <div class="profiles__medals-section-top">
                                <p><?= GetStringRaw("profiles", "profile.medals.completion"); ?> <span id="completion__amount" class="profiles__medal-progress-percentage">?</span></p>
                                <p class="profiles__medals-section-top-right profiles__medals-section-top-right-percentage">
                                    <strong id="medals__to__go">? medals to go</strong> <?= GetStringRaw("profiles", "profile.medals.until"); ?> <strong><span id="next__club">?</span>!</strong>
                                </p>
                            </div>
                            <div class="profiles__medalinfo-section-bar">
                                <div class="osekai__progress-bar osekai__progress-bar-custom osekai__progress-bar-clubs">
                                    <div id="completion__bar" class="osekai__progress-bar-inner"></div>
                                </div>
                            </div>
                        </div>
                        <div id="rarest__medal__panel" class="profiles__medalinfo-section">
                            <div class="profiles__medals-section-top">
                                <p><?= GetStringRaw("profiles", "profile.medals.rarest"); ?></p>
                                <!-- <p class="profiles__medals-section-top-right">only <strong id="rarest__medal__frequency">?% of players</strong> have this medal</p> -->
                                <p class="profiles__medals-section-top-right">
                                    <?= GetStringRaw("profiles", "profile.medals.onlyPercent", ['<strong id="rarest__medal__frequency">' . GetStringRaw("profiles", "profile.medals.percentOfPlayers", ["0"]) . '</strong>']); ?>
                                </p>
                            </div>
                            <div class="profiles__medalinfo-section_rarest-medalinfo">
                                <img id="rarest__medal__bg" src="https://assets.ppy.sh/medals/web/all-secret-5050.png" class="background">
                                <div class="profiles__medalinfo-section_rarest-medalinfo-left">
                                    <img id="rarest__medal__img" src="https://assets.ppy.sh/medals/web/all-secret-5050.png">
                                    <div class="texts">
                                        <h2 id="rarest__medal__title">Loading</h2>
                                        <p id="rarest__medal__description">Please wait...</p>
                                    </div>
                                </div>
                            </div>
                </div>
                        <div id="rarest__medal__panel__error" class="profiles__medalinfo-error hidden">
                            <?= GetStringRaw("profiles", "profile.medals.rarest.fail") ?>
                        </div>
                        <div id="medals__history" class="profiles__medal-history">
                        </div>
                    </div>

                </section>

                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <i class="oif-medal-outlined"></i>
                        <p><?= GetStringRaw("profiles", "profile.unachievedMedals.title") ?></p>
                    </div>
                    <div id="unachieved_panel" class="osekai__panel-inner osekai__flex-vertical-container">
                        <!-- <div class="profiles__unachievedmedals-section hush-hush">
                                <div class="profiles__unachievedmedals-section-header">
                                    <p class="profiles__unachievedmedals-section-header-left">Hush-Hush</p>
                                    <div class="osekai__progress-bar">
                                        <div style="width: 40%" class="osekai__progress-bar-inner"></div>
                                    </div>
                                    <p class="profiles__unachievedmedals-section-header-right"><span>24</span>/<light>50</light>
                                    </p>
                                </div>
                                <div class="profiles__unachievedmedals-section-inner">
                                    <div class="profiles__unachievedmedals-section-list">
                                        <div class="profiles__unachievedmedals-section-progress-medal">
                                            <img src="">
                                            <div class="profiles__unachievedmedals-section-progress-inner">
                                                <div class="profiles__unachievedmedals-section-progress-inner-top">
                                                    <h3>
                                                        <light>30,000 Drum Hits -></light><span> 300,000 Drum Hits</span>
                                                    </h3>
                                                    <p class="percentage">50%</p>
                                                    <p class="progress">[150,000 / <light>300,000</light>]</p>
                                                </div>
                                                <div class="osekai__progress-bar">
                                                    <div style="width: 40%" class="osekai__progress-bar-inner"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="profiles__unachievedmedals-section-grid">
                                        <div class="profiles__unachievedmedals-section-grid-medal">
                                            <img src="https://assets.ppy.sh/medals/web/all-secret-celestialmovement.png">
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                    </div>
                </section>

                <!-- snapshots integration -->
                <section class="osekai__panel" id="snapshots--panel">
                    <div class="osekai__panel-header">
                        <i class="fas fa-camera"></i>
                        <p><?= GetStringRaw("profiles", "profile.snapshots.title"); ?></light>
                        </p>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container" id="snapshots--container">

                    </div>
                </section>
                <!-- end of snapshots integration -->
            </div>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=<?= OSEKAI_VERSION; ?>"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>
<!-- woo -->