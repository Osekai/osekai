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
    <meta property="og:description"
        content="we're a website which provides osu! players with medal solutions, an alternative leaderboard, and much more! check our site out" />
    <meta name="twitter:title" content="Osekai • the home of alternative rankings, medal solutions, and more" />
    <meta name="twitter:description"
        content="we're a website which provides osu! players with medal solutions, an alternative leaderboard, and much more! check our site out" />
    <title name="title">Osekai • the home of alternative rankings, medal solutions, and more</title>
    <meta name="keywords"
        content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= ROOT_URL ?>/home" />

    <?php
    font();
    css();
    echo $head;
    lottie();
    ?>
</head>

<body onload="positionNav();">
    <!-- it refuses to position!!!! -->
    <?php navbar(); ?>

    <div class="osekai__panel-container nopadding home__loader-wait" id="home">
        <!-- <div class="home__warning-bar">
            This is an experimental new style for the Osekai Homepage. What you're seeing is <del>very early in development.</del> almost done!
        </div> -->

        <div class="home__cover" onload="positionNav();">
            <div class="home__cover-background">
                <canvas id="gradient-canvas" data-transition-in></canvas>
            </div>
            <div class="home__cover-middle">
                <div class="home__cover-middle-logo">
                    <!-- <img src="img/osekai.svg" class="home__logo"> -->
                    <lottie-player class="home__logo-lottie" id="player" src="img/osekai-logo.json"
                        background="transparent" speed="1" style="height: 300px; margin: -90px 0px;"></lottie-player>
                </div>
                <p>
                    <?= GetStringRaw("home", "cover.subtitle"); ?>
                </p>
            </div>
            <div class="home__cover-bottom">
                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.8"
                        d="M3 0C2.20435 0 1.44129 0.316071 0.87868 0.87868C0.316071 1.44129 0 2.20435 0 3L0 15C0 15.7956 0.316071 16.5587 0.87868 17.1213C1.44129 17.6839 2.20435 18 3 18H6.75C6.98287 18 7.21254 18.0542 7.42082 18.1584C7.6291 18.2625 7.81028 18.4137 7.95 18.6L10.8 22.3995C10.9397 22.5858 11.1209 22.737 11.3292 22.8411C11.5375 22.9453 11.7671 22.9995 12 22.9995C12.2329 22.9995 12.4625 22.9453 12.6708 22.8411C12.8791 22.737 13.0603 22.5858 13.2 22.3995L16.05 18.6C16.1897 18.4137 16.3709 18.2625 16.5792 18.1584C16.7875 18.0542 17.0171 18 17.25 18H21C21.7956 18 22.5587 17.6839 23.1213 17.1213C23.6839 16.5587 24 15.7956 24 15V3C24 2.20435 23.6839 1.44129 23.1213 0.87868C22.5587 0.316071 21.7956 0 21 0L3 0ZM12 5.9895C14.496 3.423 20.7375 7.914 12 13.6875C3.2625 7.9125 9.504 3.423 12 5.9895Z"
                        fill="white" />
                </svg>
                <p>
                    <?= GetStringRaw("home", "cover.madeFor"); ?>
                </p>
            </div>
            <div class="home__cover-arrow" onclick="ScrollDown()">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="home__panel-container">
        <a class="home__oac" href="/coe">
            <img class="glow" src="/home/img/coe.png">
            <img class="backdrop" src="/home/img/coe.png">
            <img src="/home/img/coe.svg">
            <div>
                <h1>We're coming to COE!</h1>
                <p>Click here to learn more.</p>
            </div>
</a>
            <!--  <a class="home__long-panel home__panel-hoverable"
                style="--paccent: 0, 0, 0; --paccent-bright: 167, 218, 245; background-image: url('img/newmedals.png'); height: 200px; margin-bottom: 20px; margin-top: -10px; justify-content: center; align-items: center;"
                href="https://discord.gg/8qpNTs6">
                <img class="home__long-img" src="img/medalhunters-logo.png" style="transform: scale(1.3);">
                <div class="home__long-texts">
                    <h1>
                        New medals are out <strong>now!</strong>
                    </h1>
                    <p>
                        Join the hunt on the osu! Medal Hunters Discord server!
                    </p>
                </div>
            </a> -->

            <div class="home__panel-row2" style="margin-bottom: 25px; margin-top: 0px; padding-top: 0px;">
                <a class="home__long-panel home__panel-hoverable"
                    style="--paccent: 0, 0, 0; --paccent-bright: 167, 218, 245; background-image: url('img/medalhunters-bg.png');"
                    href="https://discord.gg/8qpNTs6">
                    <div class="home__vertical-glowbar"></div>
                    <img class="home__long-img" src="img/medalhunters-logo.png" style="transform: scale(1.3);">
                    <div class="home__long-texts">
                        <h1>
                            <?= GetStringRaw("home", "discord.omh.title"); ?>
                        </h1>
                        <p>
                            <?= GetStringRaw("home", "discord.omh.text"); ?>
                        </p>
                    </div>
                </a>
                <a class="home__long-panel home__panel-hoverable"
                    style="--paccent: 0, 0, 0; --paccent-bright: 46, 170, 249; background-image: url('/global/img/discord.jpg');"
                    href="https://discord.gg/Rj3AYEkJj4">
                    <div class="home__vertical-glowbar"></div>
                    <img class="home__long-img" src="/global/img/branding/vector/osekai_light.svg">
                    <div class="home__long-texts">
                        <h1>
                            <?= GetStringRaw("home", "discord.osekai.title"); ?>
                        </h1>
                        <p>
                            <?= GetStringRaw("home", "discord.osekai.text"); ?>
                        </p>
                    </div>
                </a>
            </div>
            <a class="home__panel home__panel-hoverable" style="--paccent: 45, 59, 90; --paccent-bright: 102, 143, 255;"
                href="/profiles">
                <img class="home__panel-img" src="img/profiles.png">
                <div class="home__panel-inner">
                    <div class="home__vertical-glowbar"></div>
                    <div class="home__panel-inner-texts">
                        <!-- <div class="home__new">
                            <div class="home__new-badge"><?= GetStringRaw("home", "new"); ?></div>
                            <p><?= GetStringRaw("home", "new.text"); ?></p>
                        </div>
                        <h3><?= GetStringRaw("home", "new.introducing"); ?></h3> -->
                        <h1>Osekai <strong>Profiles</strong></h1>
                        <div class="home__horizontal-glowbar"></div>
                        <p class="home__app-text">
                            <?= GetStringRaw("home", "profiles.main"); ?>
                        </p>
                    </div>
                </div>
            </a>
            <a class="home__long-panel home__panel-hoverable" style="--paccent: 0, 0, 0; --paccent-bright: 255, 255, 255; background-image: url('img/bluesky.png');
                margin-bottom: 20px;" href="https://bsky.app/profile/osekai.net">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/bluesky-logo.png">
                <div class="home__long-texts">
                    <h1>
                        <?= GetStringRaw("home", "bsky"); ?>
                    </h1>
                    <p>
                        <?= GetStringRaw("home", "bsky.main"); ?>
                    </p>
                </div>
            </a>

            <div class="home__panel-row2">
                <a class="home__panel home__panel-hoverable home__panel_vertical"
                    style="--paccent: 102, 34, 68; --paccent-bright: 255, 102, 170;" href="/medals">
                    <img class="home__panel-img" src="img/medals.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Medals</strong></h1>
                            <p class="home__app-text">
                                <?= GetStringRaw("home", "medals.main"); ?>
                            </p>
                        </div>
                    </div>
                </a>
                <a class="home__panel home__panel-hoverable home__panel_vertical"
                    style="--paccent: 38, 44, 124; --paccent-bright: 63, 77, 245;" href="/snapshots">
                    <img class="home__panel-img" src="img/snapshots.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Snapshots</strong></h1>
                            <p class="home__app-text">
                                <?= GetStringRaw("home", "snapshots.main"); ?>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <a class="home__long-panel home__panel-hoverable"
                style="--paccent: 0, 0, 0; --paccent-bright: 255, 255, 255; background-image: url('img/oss-bg.jpg');"
                href="https://github.com/osekai/osekai">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/oss-icon.svg">
                <div class="home__long-texts">
                    <h1>
                        <?= GetStringRaw("home", "oss"); ?>
                    </h1>
                    <p>
                        <?= GetStringRaw("home", "oss.main"); ?>
                    </p>
                </div>
            </a>

            <div class="home__panel-row2">
                <a class="home__panel home__panel-hoverable home__panel_vertical"
                    style="--paccent: 0, 66, 79; --paccent-bright: 0, 194, 224;" href="/rankings">
                    <img class="home__panel-img" src="img/rankings.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Rankings</strong></h1>
                            <p class="home__app-text">
                                <?= GetStringRaw("home", "rankings.main"); ?>
                            </p>
                        </div>
                    </div>
                </a>
                <a class="home__panel home__panel-hoverable home__panel_vertical"
                    style="--paccent: 89,62,110; --paccent-bright: 170,102,255;" href="/badges">
                    <img class="home__panel-img" src="img/badges.png">
                    <div class="home__panel-inner">
                        <div class="home__vertical-glowbar"></div>
                        <div class="home__panel-inner-texts">
                            <h1>Osekai <strong>Badges</strong></h1>
                            <p class="home__app-text">
                                <?= GetStringRaw("home", "badges.main"); ?>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <a class="home__long-panel home__panel-hoverable"
                style="--paccent: 0, 0, 0; --paccent-bright: 237, 132, 213; background-image: url('img/support.png');"
                href="/donate">
                <div class="home__vertical-glowbar"></div>
                <img class="home__long-img" src="img/heart.svg" style="transform: scale(0.8);">
                <div class="home__long-texts">
                    <h1>
                        <?= GetStringRaw("home", "support.title"); ?>
                    </h1>
                    <p>
                        <?= GetStringRaw("home", "support.text"); ?>
                    </p>
                </div>
            </a>
            <div class="home__panel home__faq">
                <div class="home__faq-questions-list">
                    <h1>
                        <?= GetStringRaw("home", "faq"); ?>
                    </h1>
                    <h3>
                        <?= GetStringRaw("home", "faq.title"); ?>
                    </h3>
                    <div id="home__faq-list" class="home__faq-questions-list-inner"></div>
                </div>
                <div class="home__faq-answer">
                    <h1 id="home__faq-answer-title" class="home__faq-answer-title">
                        <?= GetStringRaw("home", "faq.welcome"); ?>
                    </h1>
                    <div id="home__faq-answer-answer" class="home__faq-answer-answer">
                        <?= GetStringRaw("home", "faq.welcome.intro"); ?>
                    </div>
                </div>
            </div>
            <div class="home__panel home__team">
                <h1>
                    <?= GetStringRaw("home", "team.title"); ?>
                </h1>
                <div class="home__team-grid" id="team_grid">
                </div>
            </div>
        </div>
    </div>
    <div id="home__faq-list"></div>
</body>
<script src="js/main.js?v=<?= OSEKAI_VERSION ?>"></script>
<script src="js/background.js?v=<?= OSEKAI_VERSION ?>" type="module"></script>
<img src="img/background.png" onload="positionNav();" style="display: none;">
<script>
    //document.getElementById("home").classList.remove('home__loader-wait')
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            document.getElementById("home").classList.remove('home__loader-wait')
            setTimeout(() => {
                document.getElementById("player").play();
            }, 400);
        }, 1000);
    });
</script>
<script>
    positionNav();
</script>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>