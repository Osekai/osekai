<?php
$app = "rankings";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>


<!DOCTYPE html>
<html lang="en">

<?php

$meta = '<meta charset="utf-8">
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="https://www.osekai.net/rankings" />';

$name = "Osekai Rankings • Unknown page";
$description = "uh oh";
$tags = "";

if (!isset($_GET['ranking'])) {
    $name = "Osekai Rankings • The best alternative ranking for osu!";
    $description = "The only place to find alternative osu! rankings for medals, ranked maps, and more. [temp]";
    $tags = "homepage";
} else {
    $type = $_GET['type'];

    if ($_GET['ranking'] == "Medals" && $type == "Users") {
        $name = "Osekai Rankings • who has the most medals?";
        $description = "maybe it's you...? find out who has the most medals on osekai rankings!";
        $tags = "homepage";
    }
    if ($_GET['ranking'] == "Medals" && $type == "Rarity") {
        $name = "Osekai Rankings • what medal is the rarest?";
        $description = "what medal do people own the least? find out here!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "All Mode" && $type == "Standard Deviation") {
        $name = "Osekai Rankings • top players sorted by Standard Deviation!";
        $description = "leaderboard using standard deviation across all modes! find out who's the best all mode player!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "All Mode" && $type == "Total pp") {
        $name = "Osekai Rankings • top players sorted by total pp across all modes!";
        $description = "adding all the modes together, this leaderboard is pretty interesting. check it out!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "All Mode" && $type == "Replays") {
        $name = "Osekai Rankings • who has the most watched replays?";
        $description = "how many times has this player been watched through osu!? find out here.";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "Mappers" && $type == "Ranked Mapsets") {
        $name = "Osekai Rankings • the top ranked mappers!";
        $description = "who has the most ranked maps? it's an interesting question, and we have the answer!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "Mappers" && $type == "Loved Mapsets") {
        $name = "Osekai Rankings • the top loved mappers!";
        $description = "what mapper has the most loved maps? it's a cool question, and we have the answer!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "Mappers" && $type == "Subscribers") {
        $name = "Osekai Rankings • the top subscribed people!";
        $description = "Who has the most subscribers? it's a cool question, and we have the answer!";
        $tags = "homepage";
    }

    if ($_GET['ranking'] == "Badges" && $type == "Badges") {
        $name = "Osekai Rankings / Badges / Badges • badges";
        $description = "badges";
        $tags = "homepage";
    }

    //echo $_GET['ranking'];
    //echo $type;
}

$meta .= '
<meta name="description" content="' . $name . '" />
<meta property="og:title" content="' . $name . '" />
<meta property="og:description" content="' . $description . '" />
<meta name="twitter:title" content="' . $name . '" />
<meta name="twitter:description" content="' . $description . '" />
<title name="title">' . $name . '</title>
<meta name="keywords" content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more,' . $tags . '">
'
?>

<head>
    <?php
    echo $meta;
    echo $head;
    font();
    css();
    dropdown_system();
    fontawesome();

    mobileManager();
    notification_system();
    tippy();
    user_hover_system();
    //medal_hover_system();
    medal_popup_v2();
    tooltip_system();
    //report_system();
    ?>
</head>

<body>
    <div id="oBeatmapInput"></div>

    <?php navbar(); ?>
    <div class="osekai__panel-container">

        <!-- <HOME V2> -->

        <div class="osekai__home-2col" id="home">
            <div class="osekai__home-2col-col1">
                <?php print_home_panel(); ?>

                <div class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p><?php echo GetStringRaw("rankings", "home.addUser.title"); ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <?php if (loggedin()) { ?>
                            <p><?php echo GetStringRaw("rankings", "home.addUser.loggedIn"); ?></p>
                        <?php } else { ?>
                            <p><?php echo GetStringRaw("rankings", "home.addUser.loggedOut"); ?></p>
                        <?php } ?>

                        <div class="rankings__add">
                            <p><?php echo GetStringRaw("rankings", "home.addUser.addAnotherUser.title"); ?></p>

                            <?php if (loggedin()) { ?>
                                <div class="rankings__add-row">
                                    <input class="osekai__input" type="text" placeholder="<?php echo GetStringRaw("rankings", "home.addUser.addAnotherUser.placeholder"); ?>" id="osekai__input-id">
                                    <div class="osekai__button" id="osekai__button-add"><?php echo GetStringRaw("rankings", "home.addUser.addAnotherUser.button"); ?></div>
                                </div>
                            <?php } else { ?>
                                <p><?php echo GetStringRaw("rankings", "home.addUser.addAnotherUser.loggedOut"); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if (isExperimental()) { ?>
                    <div class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>Tasks</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <p class="rankings__tasks-header">Current Task</p>
                            <div class="rankings__task rankings__task-working rankings__task-current">
                                <div class="rankings__task-accent">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="rankings__task-content">
                                    <div class="rankings__task-content-inner">
                                        <div class="rankings__task-text">
                                            <div class="rankings__task-text-left">
                                                <h3>Task: <strong>Full</strong></h3>
                                                <h2>Running Update</h2>
                                            </div>
                                            <div class="rankings__task-text-right">
                                                <h3><strong>50%</strong> - 9000/15000</h3>
                                                <h2>1:29:00 <light>ETA</light>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="osekai__progress-bar">
                                            <div class="osekai__progress-bar-inner" style="width: 50%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="rankings__tasks-header">Completed Tasks</p>
                            <div class="rankings__task rankings__task-finished">
                                <div class="rankings__task-accent">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="rankings__task-content-small">
                                    <div class="rankings__task-text-left">
                                        <h2>Full</h2>
                                        <h3>20,000 users processed</h3>
                                    </div>
                                    <div class="rankings__task-text-right">
                                        <h2>2 days ago</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="rankings__task rankings__task-error">
                                <div class="rankings__task-accent">
                                <i class="fas fa-times"></i>
                                </div>
                                <div class="rankings__task-content-small">
                                    <div class="rankings__task-text-left">
                                        <h2>Full</h2>
                                        <h3>20,000 users processed</h3>
                                    </div>
                                    <div class="rankings__task-text-right">
                                        <h2>2 days ago</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="osekai__home-2col-col2">
                <div class="rankings__home-v2-header medals-v2">
                    <h1><?php echo GetStringRaw("rankings", "general.medals.title"); ?></h1>
                </div>
                <div class="rankings__home-v2-button-container">
                    <div class="rankings__home-v2-button" selector="home__button" app="appUsers">
                        <h1><?php echo GetStringRaw("rankings", "general.medals.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.medals.users"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.medals.users.subheader"); ?></p>
                    </div>
                    <div class="rankings__home-v2-button" selector="home__button" app="appRarity">
                        <h1><?php echo GetStringRaw("rankings", "general.medals.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.medals.rarity"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.medals.rarity.subheader"); ?></p>
                    </div>
                </div>
                <div class="rankings__home-v2-header allmode-v2">
                    <h1><?php echo GetStringRaw("rankings", "general.allmode.title"); ?></h1>
                </div>
                <div class="rankings__home-v2-button-container">
                    <div class="rankings__home-v2-button" selector="home__button" app="appReplays">
                        <h1><?php echo GetStringRaw("rankings", "general.allmode.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.allmode.replays"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.allmode.replays.subheader"); ?></p>
                    </div>
                    <div class="rankings__home-v2-button" selector="home__button" app="appStdev">
                        <h1><?php echo GetStringRaw("rankings", "general.allmode.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.allmode.standardDeviation"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.allmode.standardDeviation.subheader"); ?></p>
                    </div>
                    <div class="rankings__home-v2-button" selector="home__button" app="appTPP">
                        <h1><?php echo GetStringRaw("rankings", "general.allmode.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.allmode.total"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.allmode.total.subheader"); ?></p>
                    </div>
                </div>
                <div class="rankings__home-v2-header mappers-v2">
                    <h1><?php echo GetStringRaw("rankings", "general.mappers.title"); ?></h1>
                </div>
                <div class="rankings__home-v2-button-container">
                    <div class="rankings__home-v2-button" selector="home__button" app="appRanked">
                        <h1><?php echo GetStringRaw("rankings", "general.mappers.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.mappers.ranked"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.mappers.ranked.subheader"); ?></p>
                    </div>
                    <div class="rankings__home-v2-button" selector="home__button" app="appLoved">
                        <h1><?php echo GetStringRaw("rankings", "general.mappers.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.mappers.loved"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.mappers.loved.subheader"); ?></p>
                    </div>
                    <div class="rankings__home-v2-button" selector="home__button" app="appSubscribers">
                        <h1><?php echo GetStringRaw("rankings", "general.mappers.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.mappers.subscribers"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.mappers.subscribers.subheader"); ?></p>
                    </div>
                </div>
                <div class="rankings__home-v2-header badges-v2">
                    <h1><?php echo GetStringRaw("rankings", "general.badges.title"); ?></h1>
                </div>
                <div class="rankings__home-v2-button-container">
                    <div class="rankings__home-v2-button" selector="home__button" app="appBadges">
                        <h1><?php echo GetStringRaw("rankings", "general.badges.title"); ?> / <strong><?php echo GetStringRaw("rankings", "general.badges.title"); ?></strong></h1>
                        <p><?php echo GetStringRaw("rankings", "general.badges.title"); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- </HOME V2> -->



        <div class="osekai__1col-panels" id="mobile">
            <section class="osekai__panel">
                <div class="osekai__panel-header">
                    <div class="osekai__panel-breadcrumb">
                        <p class="osekai__panel-bc_inactive" selector="breadcrumb"></p>
                        <p class="osekai__panel-bc_active" selector="subcrumb"></p>
                    </div>
                </div>
                <div class="osekai__mobile__panel-section osekai__mp-75">
                    <div class="rankings__nav-options" selector="apps">
                    </div>
                </div>
                <div class="osekai__mobile__panel-section osekai__mp-50">
                    <div class="osekai__panel-hwb-left rankings__nav-options" selector="typecrumb">
                    </div>
                </div>
                <div class="osekai__panel-inner">
                    <div class="osekai__pagination" selector="pagelist">
                    </div>
                    <div class="osekai__pagination-prevnextbuttons">
                        <div class="osekai__pagination-nb-button" selector="button__prev__page">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                            <p><?php echo GetStringRaw("rankings", "pagination.previous"); ?></p>
                        </div>
                        <div class="osekai__pagination-nb-button osekai__pagination-nb-next-button" selector="button__next__page">
                            <p><?php echo GetStringRaw("rankings", "pagination.next"); ?></p>
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="osekai__divider"></div>

                    <div class="osekai__mobile__dropdown-search-split">
                        <div class="osekai__mobile-dss-dropdown osekai__dropdown-opener" selector="dropdown__filters">
                            <p class="osekai__mobile-dss-text" selector="filter__activeItem">Username</p>
                            <i class="fas fa-chevron-down osekai__mobile-dss-icon" aria-hidden="true"></i>
                            <div class="osekai__dropdown osekai__dropdown-middle" selector="filter__items">
                            </div>
                        </div>
                        <div class="osekai__mobile-dss-search">
                            <i class="fas fa-search osekai__mobile-dss-icon" aria-hidden="true"></i>
                            <input selector="search__input" type="text" placeholder="<?php echo GetStringRaw("rankings", "search.placeholder"); ?>" maxlength="40">
                        </div>
                    </div>

                    <div class="osekai__divider"></div>

                    <div class="rankings__inner" selector="rankings__main">
                        <!-- <div class="rankings__mobile-area">
                            <div class="rankings__mobile__bar col90ab">
                                <div class="rankings__mobile__top-bar">
                                    <p>#1</p>
                                    <p>xxluizxx47</p>

                                    <div class="osekai__left osekai__center-flex-row">
                                        <p><span class="strong">50</span> medals</p>
                                        <i class="fas fa-angle-down snapshots__mobile__info snapshots__mobile__info-closed"></i>
                                    </div>
                                </div>
                                <div class="rankings__mobile__bottom-content">
                                    <div class="osekai__flex_row rankings__mobile__header">
                                        <p><span class="light">medal</span> <span class="strong">completion</span></p>
                                        <p class="osekai__left">69%</p>
                                    </div>
                                    <div class="rankings__pb-bar">
                                        <div class="rankings__pb-innerbar" style="width: 69%;">

                                        </div>
                                    </div>
                                    <div class="osekai__flex_row rankings__mobile__header">
                                        <p><span class="strong">rarest</span> <span class="light">medal</span></p>
                                    </div>
                                    <p><span class="rankings__mobile__inline-medal"><img src="https://assets.ppy.sh/medals/web/all-secret-jackpot.png"></span><span class="strong">Mappers' Guild Pack IX</span></p>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </section>
        </div>
        <div class="osekai__1col-panels" id="desktop">
            <section class="osekai__panel">
                <div class="osekai__panel-header-with-buttons">
                    <div class="osekai__panel-hwb-left">
                        <div class="osekai__panel-breadcrumb">
                            <p class="osekai__panel-bc_inactive" selector="breadcrumb"></p>
                            <p class="osekai__panel-bc_active" selector="subcrumb"></p>
                        </div>
                    </div>
                    <div class="osekai__panel-hwb-right rankings__nav-options" selector="apps">
                    </div>
                </div>
                <div class="osekai__panel-nav osekai__panel-nav__lrcont">
                    <div class="osekai__panel-hwb-left rankings__nav-options" selector="typecrumb">
                    </div>
                    <div class="osekai__panel-hwb-center">
                        <div class="osekai__pagination">
                            <div class="osekai__pagination-nb-button" id="button__prev__page" selector="button__prev__page">
                                <i class="fas fa-chevron-left"></i>
                                <p><?php echo GetStringRaw("rankings", "pagination.previous"); ?></p>
                            </div>
                            <div class="osekai__pagination-nb-button" id="button__next__page" selector="button__next__page">
                                <p><?php echo GetStringRaw("rankings", "pagination.next"); ?></p>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="osekai__panel-hwb-right">
                        <div class="osekai__panel-header-button osekai__dropdown-opener" selector="dropdown__filters">
                            <p class="osekai__panel-header-dropdown-text" selector="filter__activeItem"></p>
                            <i class="fas fa-chevron-down osekai__panel-header-dropdown-icon"></i>
                            <div class="osekai__dropdown osekai__dropdown-middle osekai__dropdown osekai__dropdown-middle-hidden" selector="filter__items">
                            </div>
                        </div>
                        <div class="osekai__panel-header-input">
                            <i class="fas fa-search osekai__panel-header-button-icon" aria-hidden="true"></i>
                            <p class="osekai__panel-header-button-text">
                                <label class="osekai__panel-header-input__sizer">
                                    <input selector="search__input" type="text" size="12" placeholder="<?php echo GetStringRaw("rankings", "search.placeholder"); ?>" maxlength="40">
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="osekai__panel-inner rankings__inner" selector="rankings__main">
                </div>
            </section>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=1.0.2"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>