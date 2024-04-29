<?php
fontawesome();
xhr_requests();
tippy();
// to replace old ones
colour_picker();

$otherApps = [];
function addOtherApp($icon, $link, $name)
{
    global $otherApps;
    array_push($otherApps, [
        "icon" => $icon,
        "link" => $link,
        "name" => $name
    ]);
    ;
}
addOtherApp("fas fa-globe-africa", "/misc/translators", GetStringRaw("navbar", "otherApps.translators"));
addOtherApp("fas fa-layer-group", "/misc/groups", GetStringRaw("navbar", "otherApps.userGroups"));
//addOtherApp("fas fa-question-circle", "/misc/faq", "FAQ");
// note: this one can be enabled later after functionality is added. for now ignore
?>

<script>
    const apps = <?= json_encode($apps); ?>;
    const currentlyApp = <?= json_encode($app); ?>;
</script>

<img src="https://osu.ppy.sh/assets/images/mod_hidden.cfc32448.png?t=<?= time(); ?>" onerror="cantContactOsu()" hidden
    class="hidden">

<div id="osekai__popup_overlay"></div>
<div class="osekai__blur-overlay" id="blur_overlay" onclick="hide_dropdowns()"></div>
<h1 style="display: none;">
    <?= $apps[$app]['name']; ?>
</h1>

<div class="osekai__navbar-container" id="navbar_container">
    <div class="osekai__navbar">
        <div class="osekai__navbar-warning hidden" id="cantContactOsu">
            <?= GetStringRaw("navbar", "misc.cantContactOsu"); ?>
        </div>
        <div class="osekai__navbar-warning osekai__noaccel hidden" id="noHardwareAccel">
        <?= GetStringRaw("navbar", "misc.hardwareAccel.warning"); ?>
        <span onclick="hardwareAccelLearnMore()"><?= GetStringRaw("navbar", "misc.hardwareAccel.learnMore"); ?></span>
        </div>
        <div class="osekai__navbar-warning osekai__noaccel hidden" id="hardwareAccelOn">
        <?= GetStringRaw("navbar", "misc.hardwareAccel.fixed"); ?>
        </div>
        <?php if (isRestricted()) { ?>
            <div class="osekai__navbar-restriction">
                <div class="osekai__navbar-restriction-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="osekai__navbar-restriction-text">
                    <h3>
                        <?= GetStringRaw("navbar", "misc.restriction.title"); ?>
                    </h3>
                    <p>
                        <?= GetStringRaw("navbar", "misc.restriction.description"); ?>
                    </p>
                </div>
            </div>
        <?php } ?>

        <div class="osekai__navbar-bottom">
            <div class="osekai__navbar-left">
                <div onclick='apps_dropdown()' class="osekai__navbar__app-button">
                    <i class="fas fa-chevron-down"></i>
                </div>

            </div>
            <div class="osekai__navbar-center">
                <div class="osekai__navbar-center-left">
                    <img rel="preload" alt="<?= $apps[$app]['name']; ?>"
                        src="/global/img/branding/vector/<?= $apps[$app]['logo']; ?>.svg">
                    <div class="osekai__navbar-breadcrumbs" id="navbarBreadcrumbs">
                        <div class="osekai__navbar-breadcrumb">
                            test
                        </div>
                        <div class="osekai__navbar-breadcrumb">
                            test
                        </div>
                        <div class="osekai__navbar-breadcrumb">
                            test
                        </div>
                    </div>
                </div>

                <div class="osekai__navbar-center-right">
                    <div class="osekai__navbar-search osekai__navbar-search-inactive" id="search_container">
                        <input id="search_input" type="text" placeholder="search for something!">
                        <i class="fas fa-search"></i>
                        <div id="search_overlay" class="osekai__navbar-search-overlay osekai__navbar-search-overlay-hidden">

                        </div>
                    </div>
                    <?php if (loggedin()) { ?>
                        <div class="osekai__navbar-button tooltip-v2" id="notif__bell__button"
                            tooltip-content="<?= GetStringRaw("navbar", "tooltip.notifications"); ?>">
                            <i class="fas fa-bell"></i>
                            <div class="osekai__notification-counter hidden" id="NotificationCountIcon">0</div>
                        </div>
                    <?php } ?>

                    <div class="osekai__navbar-button tooltip-v2"
                        onclick='dropdown("osekai__dropdown-settings-hidden", "dropdown-settings-new", 1)'
                        tooltip-content="<?= GetStringRaw("navbar", "tooltip.settings"); ?>">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>
            <div class="osekai__navbar-right">
                <img alt="Your profile picture" src="<?= getpfp(); ?>"
                    onclick='dropdown("osekai__nav-dropdown-hidden", "dropdown__user", 1)' class="osekai__navbar-pfp <?php if (isExperimental()) {
                        echo 'osekai__navbar-pfp-experimental';
                    } ?>">
            </div>
        </div>
    </div>
    <div class="osekai__navbar-alerts-container" id="alerts_container">

    </div>
</div>


<div class="osekai__alerts-container-br" id="alerts_container_br">
    
</div>

<div class="graceful__loading-overlay"></div>

<style id="cardstyle">
    .osekai__apps-dropdown-applist-right-card {
        background: linear-gradient(92.75deg, rgba(var(--appColour), 0.5) 0%, rgba(var(--appColour), 0.25) 100%), linear-gradient(92.75deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.25) 100%);
        background-size: cover;
        background-position: center;
    }
</style>

<style id="extra_style"></style>

<?php
$showable_apps = [];


foreach ($apps as $a) {

    $cover = ROOT_URL . "/global/img/" . $a['cover'] . ".jpg";

    $show = true;
    if ($a['experimental'] == true) {
        if (!isExperimental()) {
            $show = false;
        } else {
            $show = true;
        }
    }
    if ($a['visible'] == false) {
        $show = false;
    }
    $currentApp = false;
    if ($a['simplename'] == $app) {
        $currentApp = true;
    }
    $url = ROOT_URL . "/" . $a['simplename'];


    $app_x = [
        "url" => $url,
        "cover" => $cover,
        "show" => $show,
        "app" => $a
    ];
    if ($show == true) {
        $showable_apps[] = $app_x;
    }
}
?>


<div id="dropdown__apps_mobile" class="osekai__apps-dropdown-mobile-hidden osekai__apps-dropdown-mobile mobile">
    <div id="dropdown__apps-mobile-base" class="osekai__apps-dropdown-mobile-inner">
        <div class="osekai__apps-dropdown-mobile-section" style="--height: 76px;">
            <?php foreach ($showable_apps as $a) { ?>
                <a class="osekai__apps-dropdown-mobile-button osekai__apps-dropdown-mobile-app"
                    href="/<?= $a['app']['simplename']; ?>">
                    <img alt="Logo for <?= $a['app']['simplename']; ?>"
                        src="/global/img/branding/vector/<?= $a['app']['logo']; ?>.svg">
                    <div class="osekai__apps-dropdown-mobile-app-texts">
                        <h2>osekai <strong>
                                <?= $a['app']['simplename']; ?>
                            </strong></h2>
                        <h3>
                            <?= $a['app']['slogan']; ?>
                        </h3>
                    </div>
                </a>
            <?php } ?>
        </div>

        <div class="osekai__apps-dropdown-mobile-section" style="--height: 59px;">
            <a class="osekai__apps-dropdown-mobile-button" onclick="showOtherApps()">
                <p>
                    <?= GetStringRaw("navbar", "otherApps.title"); ?>
                </p>
            </a>
        </div>

        <div class="osekai__apps-dropdown-mobile-section" style="--height: 46px;">
            <a class="osekai__apps-dropdown-mobile-button" href="/donate">
                <i class="fas fa-heart"></i>
                <p>
                    <?= GetStringRaw("navbar", "apps.support"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://twitter.com/osekaiapp">
                <i class="fab fa-twitter"></i>
                <p>
                    <?= GetStringRaw("navbar", "apps.twitter"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://discord.gg/Rj3AYEkJj4">
                <i class="fab fa-discord"></i>
                <p>
                    <?= GetStringRaw("navbar", "apps.discord.osekai"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://discord.com/invite/8qpNTs6">
                <i class="fab fa-discord"></i>
                <p>
                    <?= GetStringRaw("navbar", "apps.discord"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://discord.gg/uZ9CsQBvqM">
                <i class="fab fa-discord"></i>
                <p>
                    <?= GetStringRaw("navbar", "apps.developmentDiscord"); ?>
                </p>
            </a>
        </div>

        <div class="osekai__apps-dropdown-mobile-section" style="--height: 38px;">
            <a class="osekai__apps-dropdown-mobile-button" href="/legal/privacy">
                <p>
                    <?= GetStringRaw("navbar", "apps.privacy"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="/legal/contact">
                <p>
                    <?= GetStringRaw("navbar", "apps.contact"); ?>
                </p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="/legal/licences">
                <p>
                    <?= GetStringRaw("navbar", "apps.licences"); ?>
                </p>
            </a>
        </div>

        <div class="osekai__apps-dropdown-mobile-copyright">
            © Osekai 2019-
            <?= date("Y"); ?>
        </div>
        <div class="extra-space"></div>
    </div>
    <div id="dropdown__apps-mobile-other"
        class="osekai__apps-dropdown-mobile-inner osekai__apps-dropdown-mobile-inner-hidden osekai__apps-dropdown-mobile-hidden">
        <div class="osekai__apps-dropdown-mobile-section" style="--height: 59px;">
            <a class="osekai__apps-dropdown-mobile-button" onclick="hideOtherApps()">
                <i class="fas fa-chevron-left"></i>
                <p>
                    <?= GetStringRaw("navbar", "otherApps.back"); ?>
                </p>
            </a>
        </div>
        <div class="osekai__apps-dropdown-mobile-section" style="--height: 70px;">
            <?php
            foreach ($otherApps as $oapp) {
                // would name this $app but for whatever fucking reason php decides that it's
                // a great idea to replace the variable "$app" with it and never put it 
                // back globally across the entire project if you dare to do that so we
                // have to kinda just be careful not to?? i hate this
                echo "<a class=\"osekai__apps-dropdown-mobile-button\" href=\"{$oapp['link']}\">
                    <i class=\"{$oapp['icon']}\"></i><p>{$oapp['name']}</p>
                </a>";
            }
            ?>
        </div>
    </div>
</div>


<div id="dropdown__apps" class="osekai__apps-dropdown-hidden osekai__apps-dropdown desktop">
    <div class="osekai__apps-dropdown-image" id="background_image">

    </div>
    <div id="otherapplist"
        class="osekai__apps-dropdown-applist osekai__apps-dropdown-applist-other osekai__apps-dropdown-hidden">
        <div class="osekai__apps-dropdown-applist-left">

            <div class="osekai__apps-dropdown-applist-left-top">
                <div class="osekai__apps-dropdown-applist-left-bottom" onclick="hideOtherApps()">
                    <p><i class="fas fa-chevron-left"></i>
                        <?= GetStringRaw("navbar", "otherApps.back"); ?>
                    </p>
                </div>
                <div class="osekai__apps-dropdown-other-content">
                    <?php
                    foreach ($otherApps as $oapp) {
                        // would name this $app but for whatever fucking reason php decides that it's
                        // a great idea to replace the variable "$app" with it and never put it 
                        // back globally across the entire project if you dare to do that so we
                        // have to kinda just be careful not to?? i hate this
                        echo "<a class=\"osekai__apps-dropdown-other-content-button\" href=\"{$oapp['link']}\">
                            <p><i class=\"{$oapp['icon']}\"></i> {$oapp['name']}</p>
                        </a>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="osekai__apps-dropdown-applist" id="outer-app-list">
        <div id="applist" class="osekai__apps-dropdown-applist-left">
            <div class="osekai__apps-dropdown-applist-left-top">
                <?php foreach ($showable_apps as $a) {

                    ?>
                    <a onmouseover="setCardDetails('<?= $a['app']['simplename']; ?>')"
                        href="/<?= $a['app']['simplename']; ?>" class="osekai__apps-dropdown-applist-app<?php if ($currentApp == true) {
                              echo " osekai__apps-dropdown-applist-app-active";
                          } ?>">
                        <div class="osekai__apps-dropdown-applist-app-icon">
                            <img alt="Logo for <?= $a['app']['simplename']; ?>"
                                src="/global/img/branding/vector/<?= $a['app']['logo']; ?>.svg">
                        </div>
                        <p>osekai <strong>
                                <?= $a['app']['simplename']; ?>
                            </strong></p>
                    </a>
                    <?php

                }
                ?>
            </div>

            <div class="osekai__apps-dropdown-applist-left-bottom" onclick="showOtherApps()">
                <?= GetStringRaw("navbar", "otherApps.title"); ?>
            </div>

        </div>
        <div class="osekai__apps-dropdown-applist-right">
            <div id="dropdown_card" class="osekai__apps-dropdown-applist-right-card">
                <div class="osekai__apps-dropdown-applist-right-card-inner">
                    <img id="dropdown_card_icon" alt="Logo"
                        src="/global/img/branding/vector/<?= $apps[$app]['logo']; ?>.svg">
                    <div class="osekai__apps-dropdown-applist-right-card-texts">
                        <h3 id="dropdown_card_title">osekai <strong>
                                <?= $apps[$app]['simplename']; ?>
                            </strong></h3>
                        <p id="dropdown_card_content">
                            <?= $apps[$app]['slogan']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="osekai__apps-dropdown-bottomleft">
        <!-- support -->
        <a class="osekai__apps-dropdown-bottomleft-extra" href="<?= ROOT_URL; ?>/donate">
            <p>
                <?= GetStringRaw("navbar", "apps.support"); ?>
            </p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fas fa-heart"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://twitter.com/osekaiapp">
            <p>
                <?= GetStringRaw("navbar", "apps.twitter"); ?>
            </p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-twitter"></i>
            </div>
        </a>
        <a rel="me" href="https://bsky.app/profile/osekai.net" class="osekai__apps-dropdown-bottomleft-extra">
            <p>
                <?= GetStringRaw("navbar", "apps.bluesky"); ?>
            </p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fas fa-circle"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://discord.gg/Rj3AYEkJj4">
            <p>
                <?= GetStringRaw("navbar", "apps.discord.osekai"); ?>
            </p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-discord"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://discord.com/invite/8qpNTs6">
            <p>
                <?= GetStringRaw("navbar", "apps.discord"); ?>
            </p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-discord"></i>
            </div>
        </a>
    </div>

    <div class="osekai__apps-dropdown-bottomright">
        <div class="links">
            <a href="https://github.com/Osekai/osekai">
                <?= GetStringRaw("navbar", "apps.github"); ?>
            </a>
            <a href="https://discord.gg/uZ9CsQBvqM"><?= GetStringRaw("navbar", "apps.developmentDiscord"); ?></a>
            <a href="<?= ROOT_URL; ?>legal/contact"><?= GetStringRaw("navbar", "apps.contact"); ?></a>
            <a href="<?= ROOT_URL; ?>legal/licences"><?= GetStringRaw("navbar", "apps.licences"); ?></a>
            <a href="<?= ROOT_URL; ?>legal/privacy"><?= GetStringRaw("navbar", "apps.privacy"); ?></a>
        </div>
        <div class="osekai__apps-dropdown-bottomright-copyright">
            © Osekai 2019-
            <?= date("Y"); ?>
        </div>
    </div>
</div>

<div id="dropdown__user" class="osekai__nav-dropdown-v2 osekai__nav-dropdown-hidden">
    <?php if (loggedin()) { ?>
        <div class="osekai__nav-dropdown-v2-mainpanel">
            <a href="/profiles?user=<?= $_SESSION['osu']['id']; ?>"><img class="osekai__nav-dropdown-v2-mainpanel-avatar"
                    src="<?= getpfp(); ?>"></a>
            <div class="osekai__nav-dropdown-v2-mainpanel-texts osekai__nav-dropdown-v2-mainpanel-texts-loading"
                id="userdropdown_texts-loading">
                <svg viewBox="0 0 50 50" class="spinner">
                    <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                    <circle class="line" cx="25" cy="25" r="22.5"></circle>
                </svg>
            </div>
            <div class="osekai__nav-dropdown-v2-mainpanel-texts hidden" id="userdropdown_texts">
                <div class="osekai__nav-dropdown-v2-mainpanel-texts-top">
                    <p class="osekai__nav-dropdown-v2-mainpanel-texts-left">
                        <?= $_SESSION['osu']['username']; ?>
                    </p>
                    <p class="osekai__nav-dropdown-v2-mainpanel-texts-right" id="userdropdown_club">0% Club</p>
                </div>
                <div class="osekai__progress-bar">
                    <div class="osekai__progessbar-inner" id="userdropdown__bar" style="width: 82.32%;"></div>
                </div>
                <div class="osekai__nav-dropdown-v2-mainpanel-texts-bottom">
                    <p class="osekai__nav-dropdown-v2-mainpanel-texts-left" id="userdropdown_pp">0pp</p>
                    <p class="osekai__nav-dropdown-v2-mainpanel-texts-right" id="userdropdown_medals">0 medals</p>
                </div>
            </div>
        </div>
        <div class="osekai__nav-dropdown-v2-lowerpanel">
            <?php if (isExperimental()) { ?>
                <!-- <div class="osekai__nav-dropdown-user-v2-lowerpanel-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?= GetStringRaw("navbar", "profile.experimentalMode"); ?></p>
                </div> -->
                <div class="osekai__generic-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>
                        <?= GetStringRaw("navbar", "profile.experimentalMode"); ?>
                    </p>
                </div>
                <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 100, 0" onclick="ExperimentalOff()">
                    <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                    <p>
                        Turn Experimental Mode Off
                    </p>
                </a>
            <?php } ?>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 104, 143, 255"
                href="/profiles?user=<?= $_SESSION['osu']['id']; ?>">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <i class="oif-app-profiles"></i>
                <p>
                    <?= GetStringRaw("navbar", "profile.viewOnOsekaiProfiles"); ?>
                </p>
            </a>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 102, 170"
                href="https://osu.ppy.sh/users/<?= $_SESSION['osu']['id']; ?>" target="_blank" rel="noopener">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <i class="oif-osu-logo"></i>
                <p>
                    <?= GetStringRaw("navbar", "profile.viewOnOsu"); ?>
                </p>
            </a>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 0, 0" href="/global/php/logout.php">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p>
                    <?= GetStringRaw("navbar", "profile.logOut"); ?>
                </p>
            </a>
        </div>
    <?php } else { ?>
        <div class="osekai__nav-dropdown-v2-lowerpanel">
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 102, 170" href="<?= $loginurl; ?>"
                onclick="openLoader('Logging you in...'); hide_dropdowns();">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p>
                    <?= GetStringRaw("navbar", "profile.logIn"); ?>
                </p>
            </a>
        </div>
    <?php } ?>
</div>

<div class="osekai__dropdown-settings osekai__dropdown-settings-hidden" id="dropdown-settings-new">
    <div class="osekai__dropdown-settings-loader">
        <svg viewBox='0 0 50 50' class='spinner'>
            <circle class='ring' cx='25' cy='25' r='22.5' />
            <circle class='line' cx='25' cy='25' r='22.5' />
        </svg>
        <p>Loading...</p>
    </div>
    <div class="osekai__dropdown-settings-pages">
        <h1 class="osekai__dropdown-settings-pages-header">Settings <span onclick="hide_dropdowns()"><i
                    class="fas fa-times-circle"></i> Close</span></h1>
        <div class="osekai__dropdown-settings-pages-list" id="settings-page-list">

        </div>
    </div>
    <div class="osekai__dropdown-settings-content" id="settings-content">

    </div>
</div>

<div id="dropdown__notifs"
    class="osekai__nav-dropdown-v2 osekai__nav-dropdown-v2-generic osekai__nav-dropdown-v2-notifications osekai__nav-dropdown-hidden">
    <div class="osekai__nav-dropdown-v2-mainpanel">
        <i class="fas fa-bell"></i>
        <div class="osekai__nav-dropdown-v2-mainpanel-texts">
            <h2>
                <?= GetStringRaw("navbar", "notifications.title"); ?>
            </h2>
            <p>
                <?= GetStringRaw("navbar", "notifications.subtitle"); ?>
            </p>
        </div>
    </div>
    <div class="osekai__nav-dropdown-v2-lowerpanel">
        <div class="osekai__nav-dropdown-v2-notifications-header">
            <div class="osekai__nav-dropdown-v2-notifications-header-left">
                <p id="NotificationCount">
                    <?= GetStringRaw("navbar", "notifications.count"); ?>
                </p>
            </div>
            <div id="ClearAll" class="osekai__nav-dropdown-v2-notifications-header-right">
                <p>
                    <?= GetStringRaw("navbar", "notifications.clearAll"); ?>
                </p> <i class="far fa-times-circle"></i>
            </div>
        </div>
        <div id="notification__list__v2" class="osekai__nav-dropdown-v2-notifications-list">
        </div>
    </div>
</div>

<div id="loading_overlay">
</div>

<div id="other_overlays">
</div>

<div id="css_cont"></div>

<meta name="viewport" content="width=device-width, initial-scale=1">


<div class="snowflakes" aria-hidden="true" id="snowflakes">

</div>

<script type="text/javascript" src="/global/js/variables.js?v=<?= OSEKAI_VERSION ?>"></script>
<script rel="preload" type="text/javascript" src="/global/js/main.js?v=<?= OSEKAI_VERSION ?>"></script>

<script type="text/javascript" src="/global/js/search.js?v=<?= OSEKAI_VERSION ?>"></script>
<script src="/global/js/navbar.js?v=<?= OSEKAI_VERSION ?>"></script>
