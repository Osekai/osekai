<?php
// osekai home [dev]
// this dev page is for testing controls, in prod it hsould redirect to /home
// /home has actual home content on it
// read the html to see what i mean i guess

$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

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
<meta property="og:url" content="<?= ROOT_URL ?>" />

<?php
if (!isset($_SESSION['role']['rights']) || $_SESSION['role']['rights'] < 1) {
    redirect(ROOT_URL . "/home");
    exit;
}

// ^ uncomment this before release

font();
css();
dropdown_system();
init3col();
mobileManager();




report_system();
notification_system();
fontawesome();
colour_picker();
new_report_system();

medal_popup_v2();
?>

<head>
    <meta charset="utf-8">

    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="" />
    <meta property=“og:description“ content="" />
    <meta name="twitter:title" content="" />
    <meta name="twitter:description" content="" />
    <title></title>
</head>

<body>
    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels" id="mobile">
            <div class="osekai__1col_col1">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>mobile test</p>
                    </div>
                    <div class="osekai__panel-inner">
                        content test
                    </div>
                </section>
            </div>
        </div>
        <div class="osekai__3col-panels" id="desktop">
            <div class="osekai__3col_col1">
                <section class="osekai__panel osekai__panel-collapsable osekai__panel-collapsable-collapsed">
                    <div class="osekai__panel-header">
                        <p>stuffz</p>
                        <div class="osekai__panel-header-right">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <a class="osekai__button">cool button</a>
                        <a class="osekai__button"><i class="fas fa-star"></i>Favourite</a>
                        <a class="osekai__button osekai__button-on"><i class="fas fa-star"></i>Unfavourite</a>
                        <a class="osekai__button" onclick="testDialog()">Dialog Test</a>
                        <p id="testdialog_text">you clicked...</p>
                        <br>

                        <div class="osekai__flex_row osekai__fr_centered">
                            <input class="osekai__checkbox" id="styled-checkbox-1" type="checkbox" value="value1">
                            <label for="styled-checkbox-1"></label>
                            <p class="osekai__checkbox-label">Test Checkbox</p>
                        </div>
                        <input placeholder="input" class="osekai__input osekai__fullwidth" type="text">
                        <div class="osekai__checkbox-v2">
                            <div class="osekai__checkbox-v2-inner">
                                <i class="fas fa-check"></i>
                            </div>
                            <p>Test Checkbox</p>
                        </div>
                        <div class="osekai__checkbox-v2 osekai__checkbox-v2-checked">
                            <div class="osekai__checkbox-v2-inner">
                                <i class="fas fa-check"></i>
                            </div>
                            <p>Test Checkbox (checked)</p>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>report stuffs</p>
                    </div>
                    <div class="osekai__panel-inner osekai__flex-vertical-container">
                        <a class="osekai__button" onclick="reportSys(0, 69420)">Report an issue with this beatmap</a>
                        <a class="osekai__button" onclick="reportSys(1, 69420)">Report an issue with this comment</a>
                        <a class="osekai__button" onclick="reportSys(2, 69420)">Report an bug on this page</a>
                        <a class="osekai__button" onclick="doReport('beatmap', 12345, ['test from homepage'])">(new) Report an issue with this beatmap</a>
                        <a class="osekai__button" onclick="doReport('comment', 12345, ['test from homepage 2'])">(new) Report an issue with this comment</a>
                        <a class="osekai__button" onclick="doReport('bug', 12345, ['test from homepage 3'])">(new) Report a bug on this page</a>
                    </div>
                </section>
            </div>
            <div class="osekai__3col_col1_spacer"></div>
            <div class="osekai__3col_right">
                <div class="osekai__3col_col2">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>A (very old) user panel</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="osekai__section-header">
                                <div class="osekai__section-header-left">
                                    <h2>(not so sick) Sick user panel</h2>
                                </div>
                                <div class="osekai__section-header-right">
                                    <h3>gotta love it</h3>
                                    <p>right?... right???</p>
                                </div>
                            </div>
                            <a href="https://osu.ppy.sh/users/1309242" class="osekai__userpanel-v2">
                                <img src="https://a.ppy.sh/1309242" class="osekai__userpanel-v2-blur">
                                <div class="osekai__userpanel-v2-inner">
                                    <img src="https://a.ppy.sh/1309242" class="osekai__userpanel-v2-pfp">
                                    <div class="osekai__userpanel-v2-texts">
                                        <div class="osekai__userpanel-v2-top">
                                            <p class="osekai__userpanel-v2-username">mulraf</p>
                                            <img src="/global/img/gamemodes/standard.svg" class="osekai__userpanel-v2-gamemode">
                                            <p class="osekai__userpanel-v2-rank">#48,376 <span class="osekai__transparent-text">global</span></p>
                                        </div>
                                        <div class="osekai__userpanel-v2-bottom">
                                            <div class="osekai__userpanel-v2-area">
                                                <div class="osekai__userpanel-v2-icon">
                                                    <p>pp</p>
                                                </div>
                                                <p class="osekai__userpanel-v2-value">5000</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>assorted features</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="osekai__generic-warning osekai__generic-warning-info">
                                <i class="fas fa-info-circle"></i>
                                <p>This is really nice!</p>
                            </div>
                            <a class="osekai__button" onclick="medalPopupV2.showMedalFromName('Jackpot')">Medal Popup (Jackpot)</a>
                        </div>
                    </section>
                </div>
                <div class="osekai__3col_col3">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>A few hover panels</p>
                        </div>
                        <div class="osekai__panel-inner">
                            gone, need to rewrite them :)
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>A colour picker</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="osekai__gradient-bar" id="colourbar">
                                <div class="osekai__gradient-bar-left"><input type="text"></input></div>
                                <div class="osekai__gradient-bar-bar"></div>
                                <div class="osekai__gradient-bar-right"><input type="text"></input></div>
                            </div>
                            <p id="hex">#000000 > #FFFFFF</p>
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                            <p>A solid colour picker</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="osekai__colour-picker" id="singlecolpicker">
                                <input type="text"></input>
                            </div>
                            <p id="hex2"></p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>

<script>
    function testDialog() {
        openDialog("Test Title", "Test Header", "Test Message", "Button 1", function() {
            document.getElementById("testdialog_text").innerHTML = "you clicked button 1!";
        }, "Button 2", function() {
            document.getElementById("testdialog_text").innerHTML = "you clicked button 2!";
        });
    }
    var cb = new newColourBar("colourbar", function(col1, col2) {
        document.getElementById("hex").innerHTML = "#" + rgbToHex(col1[0], col1[1], col1[2]) + " > #" + rgbToHex(col2[0], col2[1], col2[2]);
    });

    var picker = document.getElementById("singlecolpicker");
    const picker1 = new CP(picker);
    var picker1_col;
    picker1.on('change', function(r, g, b, a) {
        picker1_col = [r, g, b];
        picker.getElementsByTagName("input")[0].value = rgbToHex(r, g, b);
        console.log(rgbToHex(r, g, b));
        picker.style.background = rgbToHex(r, g, b);
    });
</script>