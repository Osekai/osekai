<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#353d55">
    <meta name="theme-color" content="#353d55">
    <meta name="description" content="Osekai • the home of alternative rankings, medal solutions, and more" />
    <meta property="og:title" content="Osekai • the home of alternative rankings, medal solutions, and more" />
    <meta property="og:description" content="we're a website which provides osu! players with medal solutions, an alternative leaderboard, and much more! check our site out" />
    <meta name="twitter:title" content="Osekai • the home of alternative rankings, medal solutions, and more" />
    <meta name="twitter:description" content="we're a website which provides osu! players with medal solutions, an alternative leaderboard, and much more! check our site out" />
    <title name="title">Osekai • the home of alternative rankings, medal solutions, and more</title>
    <meta name="keywords" content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="https://www.osekai.net/home" />

    <?php
    font();
    css();
    dropdown_system();
    mobileManager();
    echo $head;

    //notification_system();
    //user_hover_system();
    //medal_hover_system();
    //tooltip_system();
    //comments_system();
    //report_system();
    ?>
</head>

<body onload="positionNav();"> <!-- it refuses to position!!!! -->
    <?php navbar(); ?>

    <div class="osekai__panel-container nopadding home__loader-wait" id="home">
        <!-- <div class="home__warning-bar">
            This is an experimental new style for the Osekai Homepage. What you're seeing is <del>very early in development.</del> almost done!
        </div> -->

        <div class="home__cover" onload="positionNav();">
        <div class="home__cover-background">
            <img src="img/background.png">
        </div>
            <div class="home__cover-middle">
                <div class="home__cover-middle-logo">
                    <img src="img/osekai.svg" class="home__logo">
                </div>
                <p><?php echo GetStringRaw("home", "cover.subtitle"); ?></p>
            </div>
            <div class="home__cover-bottom">
                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.8" d="M3 0C2.20435 0 1.44129 0.316071 0.87868 0.87868C0.316071 1.44129 0 2.20435 0 3L0 15C0 15.7956 0.316071 16.5587 0.87868 17.1213C1.44129 17.6839 2.20435 18 3 18H6.75C6.98287 18 7.21254 18.0542 7.42082 18.1584C7.6291 18.2625 7.81028 18.4137 7.95 18.6L10.8 22.3995C10.9397 22.5858 11.1209 22.737 11.3292 22.8411C11.5375 22.9453 11.7671 22.9995 12 22.9995C12.2329 22.9995 12.4625 22.9453 12.6708 22.8411C12.8791 22.737 13.0603 22.5858 13.2 22.3995L16.05 18.6C16.1897 18.4137 16.3709 18.2625 16.5792 18.1584C16.7875 18.0542 17.0171 18 17.25 18H21C21.7956 18 22.5587 17.6839 23.1213 17.1213C23.6839 16.5587 24 15.7956 24 15V3C24 2.20435 23.6839 1.44129 23.1213 0.87868C22.5587 0.316071 21.7956 0 21 0L3 0ZM12 5.9895C14.496 3.423 20.7375 7.914 12 13.6875C3.2625 7.9125 9.504 3.423 12 5.9895Z" fill="white" />
                </svg>
                <p><?php echo GetStringRaw("home", "cover.madeFor"); ?></p>
            </div>
            <div class="home__cover-arrow" onclick="ScrollDown()">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
        <div class="home__panel-container">
            <a class="home__panel home__panel-hoverable" style="--paccent: 45, 59, 90; --paccent-bright: 102, 143, 255;" href="/profiles">
                <img class="home__panel-img" src="img/profiles.png">
                <div class="home__panel-inner">
                    <div class="home__vertical-glowbar"></div>
                    <div class="home__panel-inner-texts">
                        <div class="home__new">
                            <div class="home__new-badge"><?php echo GetStringRaw("home", "new"); ?></div>
                            <p><?php echo GetStringRaw("home", "new.text"); ?></p>
                        </div>
                        <h3><?php echo GetStringRaw("home", "new.introducing"); ?></h3>
                        <h1>Osekai <strong>Profiles</strong></h1>
                        <div class="home__horizontal-glowbar"></div>
                        <p class="home__app-text"><?php echo GetStringRaw("home", "profiles.main"); ?></p>
                    </div>
                </div>
            </a>


            <!-- <a class="home__long-panel home__panel-hoverable" style="--paccent: 0, 0, 0; --paccent-bright: 255, 102, 170; background-image: url('img/harumachi-bg.png');" href="https://harumachi.chromb.uk">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/harumachi.svg" style="transform: scale(1.3);">
                <div class="home__long-texts">
                    <h1><?php echo GetStringRaw("home", "harumachi.title"); ?></h1>
                    <p><?php echo GetStringRaw("home", "harumachi.text"); ?></p>
                </div>
            </a> -->
            <div class="home__panel-row2">
                <a class="home__panel home__panel-hoverable home__panel_vertical" style="--paccent: 102, 34, 68; --paccent-bright: 255, 102, 170;" href="/medals">
                    <img class="home__panel-img" src="img/medals.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Medals</strong></h1>
                            <p class="home__app-text"><?php echo GetStringRaw("home", "medals.main"); ?></p>
                        </div>
                    </div>
                </a>
                <a class="home__panel home__panel-hoverable home__panel_vertical" style="--paccent: 38, 44, 124; --paccent-bright: 63, 77, 245;" href="/snapshots">
                    <img class="home__panel-img" src="img/snapshots.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Snapshots</strong></h1>
                            <p class="home__app-text"><?php echo GetStringRaw("home", "snapshots.main"); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <a class="home__long-panel home__panel-hoverable" style="--paccent: 0, 0, 0; --paccent-bright: 167, 218, 245; background-image: url('img/medalhunters-bg.svg');" href="https://discord.gg/8qpNTs6">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/medalhunters.svg" style="transform: scale(1.3);">
                <div class="home__long-texts">
                    <h1><?php echo GetStringRaw("home", "discord.title"); ?></h1>
                    <p><?php echo GetStringRaw("home", "discord.text"); ?></p>
                </div>
            </a>
            <div class="home__panel-row2">
                <a class="home__panel home__panel-hoverable home__panel_vertical" style="--paccent: 0, 66, 79; --paccent-bright: 0, 194, 224;" href="/rankings">
                    <img class="home__panel-img" src="img/rankings.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Rankings</strong></h1>
                            <p class="home__app-text"><?php echo GetStringRaw("home", "rankings.main"); ?></p>
                        </div>
                    </div>
                </a>
                <a class="home__panel home__panel-hoverable home__panel_vertical" style="--paccent: 89,62,110; --paccent-bright: 170,102,255;" href="/badges">
                    <img class="home__panel-img" src="img/badges.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Badges</strong></h1>
                            <p class="home__app-text"><?php echo GetStringRaw("home", "badges.main"); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <a class="home__long-panel home__panel-hoverable" style="--paccent: 0, 0, 0; --paccent-bright: 237, 132, 213; background-image: url('img/support.png');" href="/donate">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/heart.svg" style="transform: scale(0.8);">
                <div class="home__long-texts">
                    <h1><?php echo GetStringRaw("home", "support.title"); ?></h1>
                    <p><?php echo GetStringRaw("home", "support.text"); ?></p>
                </div>
            </a>
            <div class="home__panel home__faq">
                <div class="home__faq-questions-list">
                    <h1><?php echo GetStringRaw("home", "faq"); ?></h1>
                    <h3><?php echo GetStringRaw("home", "faq.title"); ?></h3>
                    <div id="home__faq-list" class="home__faq-questions-list-inner"></div>
                </div>
                <div class="home__faq-answer">
                    <h1 id="home__faq-answer-title" class="home__faq-answer-title"><?php echo GetStringRaw("home", "faq.welcome"); ?></h1>
                    <div id="home__faq-answer-answer" class="home__faq-answer-answer"><?php echo GetStringRaw("home", "faq.welcome.intro"); ?></div>
                </div>
            </div>
            <div class="home__team">
                <h1><?php echo GetStringRaw("home", "team.title"); ?></h1>
                <div class="home__team-section">
                    <p class="home__team-section-title">Main Development</p>
                    <div class="home__team-grid">
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/1309242" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app osekai">
                                        <img src="/global/img/branding/vector/osekai_dark.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>mulraf</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.mulraf"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/1309242">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=1309242">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://youtube.com/mulraf">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/10379965" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app osekai">
                                        <img src="/global/img/branding/vector/osekai_dark.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>Tanza3D <span style="opacity: 0.75;">/ Hubz</span></h2>
                                    <p>Project Lead, Developer & Lead Designer</p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/10379965">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=10379965">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitter.com/hubziii">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitch.tv/tanza_live">
                                    <i class="fab fa-twitch"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.hubza.co.uk">
                                    <i class="fas fa-globe"></i>
                                </a>
                                <a class="home__team-panel-lower-social" rel="me" href="https://mastodon.online/@tanza">
                                    <i class="fab fa-mastodon"></i>
                                </a>
                            </div>
                        </div>
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/9350342" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app osekai-inverted">
                                        <img src="/global/img/branding/vector/osekai_light.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>EXtremeExploit <span style="opacity: 0.75;">/ Pedrito</span></h2>
                                        <p>Developer</p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/9350342">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=9350342">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://github.com/EXtremeExploit/">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.twitch.tv/extremeexploit_">
                                    <i class="fab fa-twitch"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://pedro.moe/">
                                    <i class="fas fa-globe"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="home__team-section">
                    <p class="home__team-section-title">App Development</p>
                    <div class="home__team-grid">
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/3357640" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app rankings">
                                        <img src="/global/img/branding/vector/white/rankings.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>Electroyan</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.electroyan"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/3357640">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=3357640">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/14125695" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app snapshots">
                                        <img src="/global/img/branding/vector/white/snapshots.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>TheEggo</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.generic.snapshotsDeveloper"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/14125695">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=14125695">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/2211396" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app rankings">
                                        <img src="/global/img/branding/vector/white/rankings.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>Badewanne3</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.electroyan"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/2211396">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=2211396">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://github.com/MaxOhn">
                                    <i class="fab fa-github"></i>
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/7279762" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app admin">
                                        <img src="/global/img/branding/vector/osekai_light.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>Coppertine</h2>
                                        <p>Admin Tools Development</p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/7279762">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=7279762">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitter.com/shuffler2001">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.twitch.tv/coppertine">
                                    <i class="fab fa-twitch"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.artstation.com/coppertine">
                                    <i class="fas fa-globe"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="home__team-section">
                    <p class="home__team-section-title">Community Management</p>
                    <div class="home__team-grid">
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/1699875" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app discord">
                                        <img src="/home/img/medalhunters.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>Remyria</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.remyria"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/1699875">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=1699875">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitch.tv/remyria">
                                    <i class="fab fa-twitch"></i>
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/16487835" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app snapshots">
                                        <img src="/global/img/branding/vector/white/snapshots.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>ILuvSkins</h2>
                                        <p><?php echo GetStringRaw("home", "team.role.generic.snapshotsManager"); ?></p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/16487835">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=16487835">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://chromb.uk/chromb.png" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app osekai-inverted">
                                        <img src="/global/img/branding/vector/osekai_light.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>chromb</h2>
                                        <p>Community Manager</p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/10238680">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=10238680">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.youtube.com/channel/UCq37paEnfI4pmwE5j3rO5eg">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitch.tv/chr0mb">
                                    <i class="fab fa-twitch"></i>
                                </a>
                            </div>
                        </div>
                        <!--  -->
                        <div class="home__team-panel">
                            <div class="home__team-panel-upper">
                                <div class="home__team-panel-upper-pfpsection">
                                    <img src="https://a.ppy.sh/18152711" class="home__team-panel-upper-pfp">
                                    <div class="home__team-panel-upper-pfp-app osekai-inverted">
                                        <img src="/global/img/branding/vector/osekai_light.svg" class="home__team-panel-upper-pfp-app-img">
                                    </div>
                                </div>
                                <div class="home__team-panel-upper-name">
                                    <h2>MegaMix_Craft</h2>
                                        <p>Community Manager</p>
                                </div>
                            </div>
                            <div class="home__team-panel-lower">
                                <a class="home__team-panel-lower-social" href="https://osu.ppy.sh/users/18152711">
                                    <img src="/global/img/icons/osu-logo-white.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="/profiles/?user=18152711">
                                    <img src="/global/img/branding/vector/white/profiles.svg">
                                </a>
                                <a class="home__team-panel-lower-social" href="https://twitter.com/MegaMix_Craft">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a class="home__team-panel-lower-social" href="https://www.youtube.com/MegaMix_Craft">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a class="home__team-panel-lower-social" rel="me" href="https://mastodon.world/@MegaMix_Craft">
                                    <i class="fab fa-mastodon"></i>
                                </a>
                                <a class="home__team-panel-lower-social" rel="me" href="https://www.speedrun.com/user/MegaMix_Craft">
                                    <i class="fas fa-trophy"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="home__faq-list"></div>
</body>
<script src="js/main.js?v=1.2"></script>
<img src="img/background.png" onload="positionNav();" style="display: none;">
<script>
    //document.getElementById("home").classList.remove('home__loader-wait')
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => { 
        document.getElementById("home").classList.remove('home__loader-wait')
        }, 1000);
    });
</script>
<script>positionNav();</script>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>