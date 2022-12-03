<?php
mobileManager();
fontawesome();
xhr_requests();
tippy();
tooltip_system(); // to replace old ones
colour_picker();

$otherApps = [];
function addOtherApp($icon, $link, $name)
{
    global $otherApps;
    array_push($otherApps, [
        "icon" => $icon,
        "link" => $link,
        "name" => $name
    ]);;
}
addOtherApp("fas fa-globe-africa", "https://osekai.net/misc/translators", "Translators");
//addOtherApp("fas fa-layer-group", "https://osekai.net/misc/groups", "User Groups");
//addOtherApp("fas fa-question-circle", "https://osekai.net/misc/faq", "FAQ");
// note: these two can be enabled later after functionality is added. for now ignore
?>

<script>
    const apps = <?= json_encode($apps); ?>;
    const currentlyApp = <?= json_encode($app); ?>;
</script>

<img src="https://osu.ppy.sh/assets/images/mod_hidden.cfc32448.png?t=<?= time(); ?>" onerror="cantContactOsu()" hidden class="hidden">

<div id="osekai__popup_overlay"></div>
<div class="osekai__blur-overlay" id="blur_overlay" onclick="hide_dropdowns()"></div>
<h1 style="display: none;"><?= $apps[$app]['name']; ?></h1>

<div class="osekai__navbar-container">
    <div class="osekai__navbar">
        <?php if (!loggedin()) { ?>
            <!-- <div class="osekai__navbar-warning" style="background-color: #fff2">
        Due to updates to some of our internal systems, all users have been logged out. You'll have to log back in again.
    </div> -->
        <?php } ?>
        <div class="osekai__navbar-warning hidden" id="cantContactOsu">
            Osekai cannot contact osu!'s servers. This may be because osu! is down, or you could be connected to a private server. If you are connected to a private server, please disconnect from it.
        </div>
        <?php if (isRestricted()) { ?>
            <div class="osekai__navbar-restriction">
                <div class="osekai__navbar-restriction-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="osekai__navbar-restriction-text">
                    <h3>Your account on Osekai has been restricted.</h3>
                    <p>To appeal this, please contact us on the <a href="https://discord.com/invite/8qpNTs6">osu! Medal Hunters Discord server</a></p>
                </div>
            </div>
        <?php } ?>

        <div class="osekai__navbar-bottom">
            <div class="osekai__navbar-left">

                <div onclick='hideOtherApps(); navflip(); open_apps_dropdown()' class="osekai__navbar__app-container">

                    <div class="osekai__navbar__app-logo">
                        <img rel="preload" alt="<?= $apps[$app]['name']; ?>" src="https://www.osekai.net/global/img/branding/vector/<?= $apps[$app]['logo']; ?>.svg">
                    </div>
                    <i class="fas fa-caret-down nav_chevron" id="nav_chevron"></i>
                </div>
            </div>
            <div class="osekai__navbar-right">
                <?php if (loggedin()) { ?>
                    <div class="osekai__navbar-button tooltip-v2" id="notif__bell__button" tooltip-content="<?= GetStringRaw("navbar", "tooltip.notifications"); ?>">
                        <i class="fas fa-bell"></i>
                        <?php if (isExperimental()) { ?>
                            <div class="osekai__notification-counter" selector="NotificationCount">2</div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div onclick='dropdown("osekai__nav-dropdown-hidden", "dropdown__settings", 1)' class="osekai__navbar-button tooltip-v2" tooltip-content="<?= GetStringRaw("navbar", "tooltip.settings"); ?>">
                    <i class="fas fa-cog"></i>
                </div>

                <div id="navbar_searchbut" onclick='openSearch(this)' class="osekai__navbar-button tooltip-v2" tooltip-content="<?= GetStringRaw("navbar", "tooltip.search"); ?>">
                    <i class="fas fa-search"></i>
                </div>

                <img alt="Your profile picture" src="<?= getpfp(); ?>" onclick='dropdown("osekai__nav-dropdown-hidden", "dropdown__user", 1)' class="osekai__navbar-pfp <?php if (isExperimental()) {
                                                                                                                                                                            echo 'osekai__navbar-pfp-experimental';
                                                                                                                                                                        } ?>">
            </div>
        </div>
    </div>
    <div class="osekai__navbar-alerts-container" id="alerts_container">

    </div>
</div>

<div class="graceful__loading-overlay"></div>

<style id="cardstyle">
    .osekai__apps-dropdown-applist-right-card {
        background: linear-gradient(92.75deg, rgba(var(--appColour), 0.5) 0%, rgba(var(--appColour), 0.25) 100%), linear-gradient(92.75deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.25) 100%), url(/global/img/.jpg);
        background-size: cover;
        background-position: center;
    }
</style>

<style id="extra_style"></style>

<div class="osekai__apps-dropdown-gradient osekai__apps-dropdown-gradient-hidden" id="osekai__apps-dropdown-gradient" onclick='navflip(); hide_dropdowns()'>

</div>

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
                <a class="osekai__apps-dropdown-mobile-button osekai__apps-dropdown-mobile-app" href="/<?= $a['app']['simplename']; ?>">
                    <img alt="Logo for <?= $a['app']['simplename']; ?>" src="https://www.osekai.net/global/img/branding/vector/<?= $a['app']['logo']; ?>.svg">
                    <div class="osekai__apps-dropdown-mobile-app-texts">
                        <h2>osekai <strong><?= $a['app']['simplename']; ?></strong></h2>
                        <h3><?= $a['app']['slogan']; ?></h3>
                    </div>
                </a>
            <?php } ?>
        </div>
        <?php
        if (isExperimental()) {
        ?>
            <div class="osekai__apps-dropdown-mobile-section" style="--height: 59px;">
                <a class="osekai__apps-dropdown-mobile-button" onclick="showOtherApps()">
                    <p>other pages</p>
                </a>
            </div>
        <?php } ?>
        <div class="osekai__apps-dropdown-mobile-section" style="--height: 46px;">
            <a class="osekai__apps-dropdown-mobile-button" href="/donate">
                <i class="fas fa-heart"></i>
                <p>support us!</p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://twitter.com/osekaiapp">
                <i class="fab fa-twitter"></i>
                <p>check out osekai on twitter!</p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://discord.com/invite/8qpNTs6">
                <i class="fab fa-discord"></i>
                <p>join the <strong>osu! Medal Hunters</strong> discord server!</p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="https://discord.gg/uZ9CsQBvqM">
                <i class="fab fa-discord"></i>
                <p>join our development discord!</p>
            </a>
        </div>

        <div class="osekai__apps-dropdown-mobile-section" style="--height: 38px;">
            <a class="osekai__apps-dropdown-mobile-button" href="/legal/privacy">
                <p>privacy policy</p>
            </a>
            <a class="osekai__apps-dropdown-mobile-button" href="/legal/contact">
                <p>contact us</p>
            </a>
        </div>

        <div class="osekai__apps-dropdown-mobile-copyright">
            © Osekai 2019-2022
        </div>
        <div class="extra-space"></div>
    </div>
    <div id="dropdown__apps-mobile-other" class="osekai__apps-dropdown-mobile-inner">
        <div class="osekai__apps-dropdown-mobile-section" style="--height: 59px;">
            <a class="osekai__apps-dropdown-mobile-button" onclick="hideOtherApps()">
                <i class="fas fa-chevron-left"></i>
                <p>back to apps</p>
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
    <div id="otherapplist" class="osekai__apps-dropdown-applist osekai__apps-dropdown-applist-other osekai__apps-dropdown-hidden">
        <div class="osekai__apps-dropdown-applist-left">

            <div class="osekai__apps-dropdown-applist-left-top">
                <div class="osekai__apps-dropdown-applist-left-bottom" onclick="hideOtherApps()">
                    <p><i class="fas fa-chevron-left"></i> back to apps</p>
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
                    <a onmouseover="setCardDetails('<?= $a['app']['simplename']; ?>')" href="/<?= $a['app']['simplename']; ?>" class="osekai__apps-dropdown-applist-app<?php if ($currentApp == true) {
                                                                                                                                                                            echo " osekai__apps-dropdown-applist-app-active";
                                                                                                                                                                        } ?>">

                        <div class="osekai__apps-dropdown-applist-app-icon">
                            <img alt="Logo for <?= $a['app']['simplename']; ?>" src="https://www.osekai.net/global/img/branding/vector/<?= $a['app']['logo']; ?>.svg">
                        </div>
                        <p>osekai <strong><?= $a['app']['simplename']; ?></strong></p>
                    </a>
                <?php

                }
                ?>
            </div>
            <?php
            if (isExperimental()) {
            ?>
                <div class="osekai__apps-dropdown-applist-left-bottom" onclick="showOtherApps()">
                    Other Apps
                </div>
            <?php
            }
            ?>
        </div>
        <div class="osekai__apps-dropdown-applist-right">
            <div id="dropdown_card" class="osekai__apps-dropdown-applist-right-card">
                <div class="osekai__apps-dropdown-applist-right-card-inner">
                    <img id="dropdown_card_icon" alt="Logo" src="https://www.osekai.net/global/img/branding/vector/<?= $apps[$app]['logo']; ?>.svg">
                    <div class="osekai__apps-dropdown-applist-right-card-texts">
                        <h3 id="dropdown_card_title">osekai <strong><?= $apps[$app]['simplename']; ?></strong></h3>
                        <p id="dropdown_card_content"><?= $apps[$app]['slogan']; ?> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="osekai__apps-dropdown-bottomleft">
        <!-- support -->
        <a class="osekai__apps-dropdown-bottomleft-extra" href="<?= ROOT_URL; ?>/donate">
            <p><?= GetStringRaw("navbar", "apps.support"); ?></p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fas fa-heart"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://twitter.com/osekaiapp">
            <p><?= GetStringRaw("navbar", "apps.twitter"); ?></p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-twitter"></i>
            </div>
        </a>
        <a rel="me" href="https://mastodon.world/@osekai" class="osekai__apps-dropdown-bottomleft-extra">
            <p><?= GetStringRaw("navbar", "apps.mastodon"); ?></p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-mastodon"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://discord.com/invite/8qpNTs6">
            <p><?= GetStringRaw("navbar", "apps.discord"); ?></p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-discord"></i>
            </div>
        </a>
        <a class="osekai__apps-dropdown-bottomleft-extra" href="https://discord.gg/uZ9CsQBvqM">
            <p><?= GetStringRaw("navbar", "apps.developmentDiscord"); ?></p>
            <div class="osekai__apps-dropdown-bottomleft-extra-icon">
                <i class="fab fa-discord"></i>
            </div>
        </a>
    </div>

    <div class="osekai__apps-dropdown-bottomright">
        <div class="links">
            <a href="https://github.com/Osekai/osekai"><?= GetStringRaw("navbar", "apps.github"); ?></a>
            <a href="https://github.com/Osekai/api-docs/wiki"><?= GetStringRaw("navbar", "apps.apiDocumentation"); ?></a>
            <a href="<?= ROOT_URL; ?>/legal/contact"><?= GetStringRaw("navbar", "apps.contact"); ?></a>
            <a href="<?= ROOT_URL; ?>/legal/privacy"><?= GetStringRaw("navbar", "apps.privacy"); ?></a>
        </div>
        <div class="osekai__apps-dropdown-bottomright-copyright">
            © Osekai 2019-<?= date("Y"); ?>
        </div>
    </div>
</div>


<?php if (loggedin()) { ?>
    <style>
        .osekai__userdropdown-bg {
            background-image: linear-gradient(to left, rgba(0, 0, 0, 0.0), rgba(0, 0, 0, 0.5)), url(<?= getcover(); ?>);
        }

        .osekai__userdropdown_v2-bg {
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.51) 23.05%, rgba(0, 0, 0, 0) 100%), center/100% url(<?= getcover(); ?>);
        }

        .osekai__nav-dropdown-v2-mainpanel {
            background: linear-gradient(263.14deg, rgba(var(--accentdark), 0.568) -1.04%, rgba(var(--accentdark), 0.8) 100%), linear-gradient(180deg, rgba(255, 255, 255, 0.3) 0%, rgba(0, 0, 0, 0.3) 100%), url(<?= getcover(); ?>), linear-gradient(0deg, rgba(var(--accentdark), 0.5), rgba(var(--accentdark), 0.5)), rgba(0, 0, 0, 0.5);
            background-blend-mode: normal, luminosity, normal, normal, normal;
            background-size: cover, cover, cover, cover, cover;
            background-position: center, center, center, center, center;
            background-repeat: no-repeat, no-repeat, no-repeat, no-repeat, no-repeat;
        }
    </style>
<?php } ?>



<div id="dropdown__user" class="osekai__nav-dropdown-v2 osekai__nav-dropdown-hidden">
    <?php if (loggedin()) { ?>
        <div class="osekai__nav-dropdown-v2-mainpanel">
            <a href="/profiles?user=<?= $_SESSION['osu']['id']; ?>"><img class="osekai__nav-dropdown-v2-mainpanel-avatar" src="<?= getpfp(); ?>"></a>
            <div class="osekai__nav-dropdown-v2-mainpanel-texts osekai__nav-dropdown-v2-mainpanel-texts-loading" id="userdropdown_texts-loading">
                <svg viewBox="0 0 50 50" class="spinner">
                    <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                    <circle class="line" cx="25" cy="25" r="22.5"></circle>
                </svg>
            </div>
            <div class="osekai__nav-dropdown-v2-mainpanel-texts hidden" id="userdropdown_texts">
                <div class="osekai__nav-dropdown-v2-mainpanel-texts-top">
                    <p class="osekai__nav-dropdown-v2-mainpanel-texts-left"><?= $_SESSION['osu']['username']; ?></p>
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
                <!-- <div class="oseaki__nav-dropdown-user-v2-lowerpanel-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?= GetStringRaw("navbar", "profile.experimentalMode"); ?></p>
                </div> -->
                <div class="osekai__generic-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?= GetStringRaw("navbar", "profile.experimentalMode"); ?></p>
                </div>
                <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 100, 0" onclick="ExperimentalOff()">
                    <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                    <p>
                        Turn Experimental Mode Off
                    </p>
                </a>
            <?php } ?>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 104, 143, 255" href="/profiles?user=<?= $_SESSION['osu']['id']; ?>">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p>
                    <?= GetStringRaw("navbar", "profile.viewOnOsekaiProfiles"); ?>
                </p>
            </a>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 102, 170" href="https://osu.ppy.sh/users/<?= $_SESSION['osu']['id']; ?>">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p>
                    <?= GetStringRaw("navbar", "profile.viewOnOsu"); ?>
                </p>
            </a>
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 0, 0" href="https://www.osekai.net/global/php/logout.php">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p><?= GetStringRaw("navbar", "profile.logOut"); ?></p>
            </a>
        </div>
    <?php } else { ?>
        <div class="osekai__nav-dropdown-v2-lowerpanel">
            <a class="osekai__nav-dropdown-v2-lowerpanel-button" style="--col: 255, 102, 170" href="<?= $loginurl; ?>" onclick="openLoader('Logging you in...'); hide_dropdowns();">
                <div class="osekai__nav-dropdown-v2-lowerpanel-button-bar"></div>
                <p><?= GetStringRaw("navbar", "profile.logIn"); ?></p>
            </a>

        </div>
    <?php } ?>
</div>

<div id="dropdown__settings" class="osekai__nav-dropdown-v2 osekai__nav-dropdown-v2-generic osekai__nav-dropdown-v2-settings osekai__nav-dropdown-hidden">
    <div class="osekai__nav-dropdown-v2-mainpanel">
        <img src="/global/img/branding/vector/osekai_light.svg" class="osekai__nav-dropdown-v2-mainpanel-logo">
        <div class="osekai__nav-dropdown-v2-mainpanel-texts">
            <h2><?= GetStringRaw("navbar", "settings.title"); ?></h2>
            <p><?= GetStringRaw("navbar", "settings.subtitle"); ?></p>
        </div>
    </div>
    <div class="osekai__nav-dropdown-v2-lowerpanel">

        <h1 class="osekai__dropdown-button-head"><?= GetStringRaw("navbar", "settings.global.title"); ?></h1>
        <h2 class="osekai__dropdown-button-subhead"><?= GetStringRaw("navbar", "settings.global.theme"); ?></h2>
        <div class="osekai__nav-dropdown-v2-dropdowncontainer">
            <div class="osekai__dropdown-button-inner osekai__dropdown-opener" onclick="OpenSettingsDropdown('dropdown__themes');">
                <p id="dropdown__themes-text">system theme</p>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="osekai__dropdown osekai__dropdown-hidden" id="dropdown__themes">
                <div class="osekai__dropdown-item osekai__dropdown-item-active">Username</div>
                <div class="osekai__dropdown-item">User ID</div>
                <div class="osekai__dropdown-item">Country</div>
                <div class="osekai__dropdown-item">Rarest Medal</div>
            </div>
        </div>
        <div id="customThemePicker" class="osekai__nav-dropdown-v2-split-colour-picker">
            <div class="osekai__nav-dropdown-v2-split-colour-picker-half">
                <div class="osekai__colour-picker" id="custom_colpicker_accent-dark">
                    <input type="text"></input>
                </div>
                </p>Accent Dark</p>
            </div>
            <div class="osekai__nav-dropdown-v2-split-colour-picker-half">
                <div class="osekai__colour-picker" id="custom_colpicker_accent">
                    <input type="text"></input>
                </div>
                <p>Accent</p>
            </div>
        </div>
        <?php if (1 == 1) { ?>
            <h2 class="osekai__dropdown-button-subhead"><?= GetStringRaw("navbar", "settings.global.language"); ?></h2>
            <div class="osekai__nav-dropdown-v2-dropdowncontainer">
                <div class="osekai__dropdown-button-inner osekai__dropdown-opener" onclick="OpenSettingsDropdown('dropdown__languages');">
                    <p id="dropdown__languages-text"><?= $currentLocale['name']; ?></p>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="osekai__dropdown osekai__dropdown-hidden" id="dropdown__languages">
                    <?php
                    //print_r($locales);
                    // Ignore experimental languages if the user isn't experimental
                    foreach ($locales as $language) {
                        if (isset($language['experimental']) && $language['experimental'] == true && !isExperimental()) {
                            continue;
                        }
                    ?>
                        <div class="osekai__dropdown-item" onclick="setLanguage('<?= $language['code']; ?>');">
                            <img src="<?= $language["flag"]; ?>" class="osekai__dropdown-item-flag">

                            <?php

                            if (isset($language['experimental']) && $language['experimental'] == 1) {
                                echo "<span class='osekai__dropdown-item-exp'>EXP</span>";
                            } else if (isset($language['wip']) && $language['wip'] == 1) {
                                echo "<span class='osekai__dropdown-item-wip'>WIP</span>";
                            }
                            echo "<p>" . $language["name"] . "</p>"; ?>

                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($christmas == 1) { ?>
            <div class="osekai__flex_row osekai__fr_centered osekai_100">
                <p class="osekai__checkbox-label"><i class="fas fa-snowflake" style="margin-right: 4px;"> </i> Snowflakes</p>
                <input class="osekai__checkbox" id="settings_global__snowflakes" type="checkbox" value="value1" onchange="saveSettings();">
                <label for="settings_global__snowflakes"></label>
            </div>
        <?php } ?>
        <h1 class="osekai__dropdown-button-head"><?= GetStringRaw("navbar", "settings.profiles.title"); ?></h1>
        <div class="osekai__flex_row osekai__fr_centered osekai_100">
            <p class="osekai__checkbox-label"><?= GetStringRaw("navbar", "settings.profiles.showMedalsFromAllModes"); ?></p>
            <input class="osekai__checkbox" id="settings_profiles__showmedalsfromallmodes" type="checkbox" value="value1" onchange="saveSettings();">
            <label for="settings_profiles__showmedalsfromallmodes"></label>
        </div>
        <h1 class="osekai__dropdown-button-head"><?= GetStringRaw("navbar", "settings.medals.title"); ?></h1>
        <div class="osekai__flex_row osekai__fr_centered osekai_100">
            <p class="osekai__checkbox-label"><?= GetStringRaw("navbar", "settings.medals.hideMedalsWhenFilterEnabled"); ?></p>
            <input class="osekai__checkbox" id="settings_medals__hidemedalswhenunobtainedfilteron" type="checkbox" value="value1" onchange="saveSettings();">
            <label for="settings_medals__hidemedalswhenunobtainedfilteron"></label>
        </div>
    </div>
</div>

<?php if (isExperimental()) { ?>
    <div id="dropdown__notifs" class="osekai__nav-dropdown-v2 osekai__nav-dropdown-v2-generic osekai__nav-dropdown-v2-notifications osekai__nav-dropdown-hidden">
        <div class="osekai__nav-dropdown-v2-mainpanel">
            <i class="fas fa-bell"></i>
            <div class="osekai__nav-dropdown-v2-mainpanel-texts">
                <h2><?= GetStringRaw("navbar", "notifications.title"); ?></h2>
                <p><?= GetStringRaw("navbar", "notifications.subtitle"); ?></p>
            </div>
        </div>
        <div class="osekai__nav-dropdown-v2-lowerpanel">
            <div class="osekai__nav-dropdown-v2-notifications-header">
                <div class="osekai__nav-dropdown-v2-notifications-header-left">
                    <p><?= GetStringRaw("navbar", "notifications.count"); ?></p>
                </div>
                <div class="osekai__nav-dropdown-v2-notifications-header-right">
                    <p><?= GetStringRaw("navbar", "notifications.clearAll"); ?></p> <i class="far fa-times-circle"></i>
                </div>
            </div>
            <div id="notification__list__v2" class="osekai__nav-dropdown-v2-notifications-list">
                <div class="osekai__nav-dropdown-v2-notification">
                    <div class="osekai__nav-dropdown-v2-notification-upper">
                        <img src="/global/img/branding/vector/osekai_light.svg">
                        <p>Test notification text</p>
                    </div>
                </div>
                <div class="osekai__nav-dropdown-v2-notification">
                    <a class="osekai__nav-dropdown-v2-notification-upper osekai__nav-dropdown-v2-notification-upper-clickable" href="test">
                        <img src="/global/img/branding/vector/white/profiles.svg">
                        <p>Test notification text with description</p>
                    </a>
                    <div class="osekai__nav-dropdown-v2-notification-lower">
                        <p>what a cool description we have here!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="dropdown__notifs" class="osekai__nav-dropdown-hidden osekai__nav-dropdown osekai__nav-dropdown-notifs">
        <div class="osekai__notifications-header">
            <?= GetStringRaw("navbar", "notifications.title"); ?>
            <div id="notification__counter" class="osekai__notifications-header-right">
            </div>
        </div>
        <div id="notification__list" class="osekai__notifications-list">
        </div>
    </div>
<?php } ?>
<?php
if ($coltype == "3") {
?>
    <div class="osekai__ct3-sidebar">
        <div id="3col_arrow" class="osekai__ct3-arrow_area ct3open" onclick="switch3col();">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
<?php
}
?>

<div id="loading_overlay">
</div>

<div id="other_overlays">
</div>

<div id="css_cont"></div>


<script>
    if (<?= (int)isExperimental(); ?> == 1) toggleExperimental();
</script>

<meta name="viewport" content="width=device-width, initial-scale=1">

<?php

include("search_overlay.php");

?>


<?php
if ($christmas == true) { ?>

    <style>
        /* customizable snowflake styling */
        .snowflake {
            color: #fff;
            font-size: 1em;
            font-family: Arial, sans-serif;
            text-shadow: 0 0 5px #0005, 0px 0px 25px #fffa;
            opacity: 1;
            pointer-events: none;
        }

        @-webkit-keyframes snowflakes-fall {
            0% {
                top: -10%
            }

            100% {
                top: 100%
            }
        }

        @-webkit-keyframes snowflakes-shake {

            0%,
            100% {
                -webkit-transform: translateX(0);
                transform: translateX(0)
            }

            50% {
                -webkit-transform: translateX(80px);
                transform: translateX(80px)
            }
        }

        @keyframes snowflakes-fall {
            0% {
                top: -10%
            }

            100% {
                top: 100%
            }
        }

        @keyframes snowflakes-shake {

            0%,
            100% {
                transform: translateX(0)
            }

            50% {
                transform: translateX(80px) translatey(40px)
            }
        }

        .snowflake {
            position: fixed;
            top: -10%;
            z-index: 9999;
            opacity: 0.8;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: default;
            -webkit-animation-name: snowflakes-fall, snowflakes-shake;
            -webkit-animation-duration: 10s, 3s;
            -webkit-animation-timing-function: linear, ease-in-out;
            -webkit-animation-iteration-count: infinite, infinite;
            -webkit-animation-play-state: running, running;
            animation-name: snowflakes-fall, snowflakes-shake;
            animation-duration: 10s, 3s;
            animation-timing-function: linear, ease-in-out;
            animation-iteration-count: infinite, infinite;
            animation-play-state: running, running
        }
    </style>
    <div class="snowflakes" aria-hidden="true" id="snowflakes">
        <?php
        for ($x = 0; $x < 40; $x++) {
            if (false == true) {
                // experimental mode which makes the app icon fall
                // i don't like how it looks but thought i'd leave it in
                echo '<div class="snowflake">
                    <i class="oif-app-' . $app . '"></i>
                </div>';
            } else {
                echo '<div class="snowflake">
                    <i class="fas fa-snowflake"></i>
                </div>';
            }
        }
        echo "<style>";
        for ($x = 0; $x < 40; $x++) {
            $delay1 = (mt_rand() / mt_getrandmax()) * 10;
            $delay2 = (mt_rand() / mt_getrandmax()) * 10;
            $position = (mt_rand() / mt_getrandmax()) * 100;
            echo ".snowflake:nth-of-type({$x}) {
            left: {$position}%;
            -webkit-animation-delay: {$delay1}s, {$delay2}s;
            animation-delay: {$delay1}s, {$delay2}s
        }";
        }
        echo "</style>";
        ?>
    </div>

<?php } ?>

<script type="text/javascript" src="<?= ROOT_URL; ?>/global/js/variables.js?v=<?= OSEKAI_VERSION; ?>"></script>
<script type="text/javascript" src="<?= ROOT_URL; ?>/global/js/main.js?v=<?= OSEKAI_VERSION; ?>"></script>

<script src="<?= ROOT_URL; ?>/global/js/navbar.js"></script>
