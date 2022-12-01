<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config.php");

if (isset($app)) {
    // stops the flash of white
    echo "<!DOCTYPE html>";
    echo "<style>html{background-color: black; color: white;}</style>";
}

$time_start = microtime(true);
$request_time = $_SERVER['REQUEST_TIME_FLOAT'];
$christmas = true;

$useJS = true;

// report errors. this is disabled later in the code somewhere, unsure where
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once('gitInfo.php');
define("OSEKAI_VERSION", str_replace("\n", "", $gitHash)); // cache invalidation

require_once('osekaiDB.php');
require_once('osekaiSessionManager.php');
require_once('osekaiCache.php');
require_once('osekaiLogging.php');
require_once('osu_api_functions.php');

startSession();

$appsTemp = Database::execSimpleSelect("SELECT * FROM Apps ORDER BY id");
$apps = array();
foreach ($appsTemp as $appa) {
    $apps[$appa['simplename']] = $appa;
}




$restrictedState = false;

if(isset($_SESSION['osu']['id']))
{
    $restrictedCheck = Database::execSelect("SELECT * FROM MembersRestrictions WHERE UserID = ?", "i", [$_SESSION['osu']['id']]);
    if (count($restrictedCheck) > 0 && $restrictedCheck[0]['Active'] == 1) {
        $restrictedState = true;
    }
}



$actual_link = ROOT_URL . $_SERVER['REQUEST_URI'];

if (MODE == "dev") {
    $oSession['role']['rights'] = 2;
}

$userGroups = null;

if (isset($app)) {

    if (MODE != "production") {
        // production site uses htaccess file to avoid issue
        // dev and local doesn't work

        $site_adress = ((((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $whole_url = $site_adress . $_SERVER['REQUEST_URI'];

        $pos = strpos($whole_url, "?");
        $changed_url = FALSE;
        if ($pos !== FALSE && $whole_url[$pos - 1] != "/") {
            $whole_url = substr_replace($whole_url, "/", $pos, 0);
            $changed_url = TRUE;
        } else if ($pos == FALSE && substr($whole_url, -1) != '/') {
            $whole_url = $whole_url . "/";
            $changed_url = TRUE;
        }
        if ($changed_url) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $whole_url);
            exit();
        }
    }
    $roles = Database::execSimpleSelect("SELECT * FROM AvailableRoles");
    $medals = Database::execSelect("CALL FUNC_GetMedals(?, ?)", "ss", ['', '']);
    $userGroups = Database::execSimpleSelect("SELECT * FROM Groups");
?>
    <script type="text/javascript">
        const christmas = "<?php echo $christmas; ?>";
        const nAppId = "<?php echo $apps[$app]['id']; ?>";
        const version = "<?php echo OSEKAI_VERSION; ?>";
        //const medalAmount = 261; // this should be pulled from the database in the future
        const nUserID = <?php if (isset($_SESSION['osu']) && $_SESSION['osu'] != "") {
                            echo $_SESSION['osu']['id'];
                        } else {
                            echo "-1";
                        } ?>;
        const nUsername = '<?php if (isset($_SESSION['osu']) && $_SESSION['osu'] != "") {
                                echo $_SESSION['osu']['username'];
                            } else {
                                echo "guest";
                            } ?>';

        const nRights = <?php if (isset($_SESSION['role']) && $_SESSION['role'] != "" && $_SESSION['role'] != null && $_SESSION['role']['rights'] != null) {
                            echo $_SESSION['role']['rights'];
                        } else {
                            echo "-1";
                        } ?>;
        const strRole = <?php if (isset($_SESSION['role']['name']) && $_SESSION['role']['name'] != "") {
                            echo "'" . $_SESSION['role']['name'] . "'";
                        } else {
                            echo "''";
                        } ?>;
        const medalAmount = <?php echo count($medals); ?>;
        const experimental = <?php
                                if (isExperimental()) {
                                    echo $_SESSION['options']['experimental'];
                                } else {
                                    echo "0";
                                } ?>;
        const roles = <?php echo json_encode($roles); ?>;
        const userGroups = <?php echo json_encode($userGroups); ?>;
        const medals = <?php echo json_encode($medals); ?>;
        const restrictedState = <?php if (isRestricted()) echo "1";
                                else echo "0"; ?>;
    </script>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/osekaiLocalization.php");
    foreach ($apps as $appa) {
        $apps[$appa['simplename']]['slogan'] = LocalizeText($apps[$appa['simplename']]['slogan']);
    }
    ?>
    <script>
        loadSource("<?php echo $app; ?>");
        loadSource("general");
    </script>
<?php
} else {
    $useJS = false;
    include_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/osekaiLocalization.php");
}


$loginurl = "https://osu.ppy.sh/oauth/authorize?response_type=code&client_id=" . OSU_OAUTH_CLIENT_ID . "&redirect_uri=" . htmlentities(OSU_OAUTH_REDIRECT_URI);




if (isset($app)) {
    if (!isExperimental() && $apps[$app]['experimental'] == 1 && MODE == "production") {
        include($_SERVER['DOCUMENT_ROOT'] . "/404/index.php");
        exit;
    }
    if ($app == "admin" && MODE == "production") {
        if ($_SESSION['role']['rights'] < 1) {
            navbar();
            include($_SERVER['DOCUMENT_ROOT'] . "/404/index.php");
            exit;
        }
    }
}


// here's the system path, we need this later probably
$path = $_SERVER['DOCUMENT_ROOT'];
$server_root = $path . "/";

$coltype = "none";

$vapp = "test";
if (isset($app)) {
    $vapp = $apps[$app]['colour_logo'];
}

$favi = ROOT_URL . "/global/img/branding/vector/" . $vapp . ".svg";
$head = '<link rel="alternate icon" type="image/svg" href="' . $favi . '" />';

function css()
{
    // imports css

    global $app;
    global $apps;


    // imports custom css, if it exists.
    // if it doesn't exist, too bad. gonna still try

    echo '<link rel="stylesheet" href="/global/css/main.css?v=' . OSEKAI_VERSION . '">';
    //echo "<style>";
    //echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/global/css/main.css");
    // https://web.dev/render-blocking-resources/?utm_source=lighthouse&utm_medium=lr
    //echo "</style>";
    $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $app . "/css/";
    $path_public = ROOT_URL . "/" . $app . "/";

    //if (isExperimental()) {
    //    echo '<link rel="stylesheet" href="/global/css/experimental.css?v=' . OSEKAI_VERSION . '">';
    //    $curUrl = ROOT_URL . $_SERVER['REQUEST_URI'];
    //
    //    $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $app . "/css/";
    //    $path_public = ROOT_URL . "/" . $app . "/";
    //    if (file_exists($path . "experimental.css")) {
    //        echo '<link rel="stylesheet" href="'.$path_public.'css/experimental.css?v=' . OSEKAI_VERSION . '">';
    //    } else {
    //        echo '<link rel="stylesheet" href="'.$path_public.'css/main.css?v=' . OSEKAI_VERSION . '">';
    //        echo '<link rel="stylesheet" href="./css/main.css?v=' . OSEKAI_VERSION . '">';
    //    }
    //} else {
    //    echo '<link rel="stylesheet" href="css/main.css?v=' . OSEKAI_VERSION . '">';
    //}

    echo '<link rel="stylesheet" href="' . $path_public . 'css/main.css?v=' . OSEKAI_VERSION . '">';
    echo '<link rel="stylesheet" href="./css/main.css?v=' . OSEKAI_VERSION . '">';
    if (isExperimental()) {
        echo '<link rel="stylesheet" href="/global/css/experimental.css?v=' . OSEKAI_VERSION . '">';
    }

    // set the accent
    $colourDark = explode(",", $apps[$app]['color_dark']);
    $colourDarkHsl = rgbToHsl($colourDark[0], $colourDark[1], $colourDark[2]);
    $colour = explode(",", $apps[$app]['color_dark']);
    $colourHsl = rgbToHsl($colour[0], $colour[1], $colour[2]);
    echo '<style>
    html{
        --accentdark: ' . $apps[$app]['color_dark'] . ';
        --accent: ' . $apps[$app]['color'] . ';

        --accentdark_hue: ' . $colourDarkHsl[0] . 'deg;
        --accent_hue: ' . $colourHsl[0] . 'deg;
        --accentdark_saturation: ' . $colourDarkHsl[1] . '%;
        --accent_saturation: ' . $colourHsl[1] . '%;
        --accentdark_value: ' . $colourDarkHsl[2] . '%;
        --accent_value: ' . $colourHsl[2] . '%;
    }
    </style>
    <style id="custom_theme_container"></style>';
}

function comments_system()
{
    tippy();

    // twemoji for emojis
    echo '<script src="https://twemoji.maxcdn.com/v/latest/twemoji.min.js" crossorigin="anonymous"></script>';

    // emoji picker
    echo '<script src="/global/js/picmo/picmo.js?v=2"></script>';
    echo '<script src="/global/js/picmo/picmo-popup.js"></script>';

    // imports main css
    echo '<link rel="stylesheet" href="/global/css/comments.css">';

    // bbcode
    echo '<script type="text/javascript" src="/global/js/bbcode/bbcode-config.js"></script>';
    echo '<script type="text/javascript" src="/global/js/bbcode/bbcode-parser.js"></script>';

    /* imports comment system
    please import xhr before using this */
    echo '<script type="text/javascript" src="/global/js/comment_system.js?v=' . OSEKAI_VERSION . '"></script>';
}

function chart_js()
{
    echo '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/moment@2.27.0"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>';
}

$mmLoaded = false;

function mobileManager()
{
    // imports css

    global $mmLoaded;

    // imports main css
    if ($mmLoaded == false) {
        echo '<script type="text/javascript" src="/global/js/mobileManager.js"></script>';
        $mmLoaded = true;
    }
}

function font()
{
    // imports font

    echo '<link rel="preload" rel="preconnect" href="https://fonts.gstatic.com"><link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">';
}

function navbar()
{
    // loads navbar

    // vscode says none of these are used
    // but they are actually all used
    // see /global/php/navbar.php

    global $locales;
    global $currentLocale;
    global $app;
    global $apps;
    global $coltype;
    global $server_root;
    global $actual_link;
    global $loginurl;
    global $christmas;

    $_SESSION['redirect_url'] = true;

    include($server_root . "global/php/navbar.php");
}

function dropdown_system()
{
    // loads dropdown system

    echo '<script type="text/javascript" src="/global/js/dropdown_system.js"></script>';
}

function init3col()
{
    // loads 3col system

    fontawesome(); // we'll need this

    global $coltype;
    $coltype = "3"; // tells the arrow thing on the left to exist

    echo '<script type="text/javascript" src="/global/js/3col.js"></script>';
}

function fontawesome()
{
    // guess what this one does

    echo '<script rel="preload" src="https://kit.fontawesome.com/91ad005f46.js" crossorigin="anonymous"></script>';
    echo '<link rel="stylesheet" href="/global/fonts/osekai-icon-font/style.css?v=' . OSEKAI_VERSION . '">';
}

function lottie()
{
    echo '<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>';
}

$hoversystemloaded = false;

function user_hover_system()
{
    global $hoversystemloaded;

    // loads hover system

    $id = 0;
    $avatar = "";
    $name = "Loading..."; // none of these need to be here

    // best way to load html into a page

    // 02/11/2021 hubz: no its fucking not lmao
    // should be importing a php or html file at the very least
    // TODO: fix this mess

    echo '<a id="beatmap_hover_panel" href="https://osu.ppy.sh/users/' . $id . '" class="osekai__hoverpanel beatmap_hover_panel_hidden osekai__userpanel">
    <img id="bhp_avi" src="' . $avatar . '" class="osekai__userpanel-pfp">
    <img id="bhp_ctc" src="https://osu.ppy.sh/images/flags/XX.png" class="osekai__userpanel-countryflag">
    <p id="bhp_usn" class="osekai__userpanel-username">' . $name . '</p>
    </a>';

    echo '<a id="userhoverpanel_v2" href="https://osu.ppy.sh/users/1309242" class="osekai__userpanel-v2 osekai__userpanel-hoverpanel osekai__userpanel-hoverpanel-hidden">
    <img id="userhoverpanel_v2_blur" src="" class="osekai__userpanel-v2-blur">
    <div class="osekai__userpanel-v2-inner">
        <img id="userhoverpanel_v2_pfp" src="" class="osekai__userpanel-v2-pfp">
        <div class="osekai__userpanel-v2-texts">
            <div class="oseaki__userpanel-v2-top">
                <p id="userhoverpanel_v2_username" class="osekai__userpanel-v2-username">mulraf</p>
                <img id="userhoverpanel_v2_gamemode" src="/global/img/gamemodes/standard.svg" class="osekai__userpanel-v2-gamemode">
                <p class="osekai__userpanel-v2-rank" id="userhoverpanel_v2_rank">#48,376 <span class="osekai__transparent-text">global</span></p>
            </div>
            <div class="osekai__userpanel-v2-bottom">
                <div class="osekai__userpanel-v2-area">
                    <div class="osekai__userpanel-v2-icon">
                        <p>pp</p>
                    </div>
                    <p class="osekai__userpanel-v2-value" id="userhoverpanel_v2_pp">5000</p>
                </div>
            </div>
        </div>
    </div>
</a>';

    // should be an include lmao

    echo '<script type="module" type="text/javascript" src="/global/js/hover_system.js"></script>';
    $hoversystemloaded = true;
    // nothing ever checks this lmao
}

$reportsystemloaded = false;

function report_system()
{
    notification_system();

    global $reportsystemloaded;

    if ($reportsystemloaded == false) {
        echo '<script type="module" type="text/javascript" src="/global/js/report_system.js"></script>';
        $reportsystemloaded = true;
    }
}

function new_report_system()
{
    include_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/osekaiReportSystem.php");
}

function notification_system()
{
    include("notification_system.php");
}

function osu_api()
{
    echo '<script type="module" type="text/javascript" src="/global/js/osu_api.js"></script>';
}

function xhr_requests()
{
    echo '<script type="module" type="text/javascript" src="/global/js/xhr.js"></script>';
}

function medal_hover_system()
{
    global $hoversystemloaded;

    // loads medal hover system

    echo '<a href="osekai.net" class="medal_hover_panel medal_hover_panel_hidden" id="medal_hover_panel" style="--mouse-x: 0;">
        <img class="mhp__medal-icon-blur" id="mhp_blur" src="https://assets.ppy.sh/medals/">
        <div class="medal_hover_panel-inner">    
            <div class="mhp__top">
                <img class="mhp__medal-icon" id="mhp_mic" src="https://assets.ppy.sh/medals/">
                <div class="mhp__left-area">
                    <p class="mhp__medal-name" id="mhp_nam">Loading...</p>
                    <p class="mhp__medal-desc" id="mhp_dsc">Loading...</p>
                </div>
            </div>
            <p class="mhp_solution" id="mhp_sol">you figure it out lmao</p>
        </div>
    </a>';

    echo '<script type="module" type="text/javascript" src="/global/js/hover_system.js"></script>';
    $hoversystemloaded = true;
}

function tooltip_system()
{
    global $hoversystemloaded;

    // loads medal hover system

    echo '
    <div id="tooltip" class="tooltip_obj tooltip_hidden">
        <p id="tooltip_text">Tooltip!</p>
    </div>
    ';

    echo '<script type="module" type="text/javascript" src="/global/js/hover_system.js"></script>';
    $hoversystemloaded = true;
}

function loggedin()
{
    // check if user is logged in
    return isset($_SESSION['osu']);
}

function redirect($url = null)
{
    if ($url == null) {
        $url = ROOT_URL;
    }
    // redirects by placing js on the page
    // usage: redirect("url");

    echo "<script>
    window.location.href = '" . $url . "';
    </script>";
    exit;
}

function getpfp()
{
    // gets the user's pfp

    if (loggedin()) {
        return $_SESSION['osu']['avatar_url'];
    } else {
        return "https://osu.ppy.sh/assets/images/avatar-guest.8a2df920.png";
    }
}

function getcover()
{
    // gets the user's cover

    if (loggedin()) {
        return $_SESSION['osu']['cover_url'];
    } else {
        // should never be displaying the cover at this point
        return "https://osu.ppy.sh/assets/images/avatar-guest.8a2df920.png";
    }
}

function getbeatmap($id)
{
    // gets a beatmap. i don't think it's ever used?
    // update: it isn't used
    // usage: getbeatmap(1);
    // its not that hard

    $eee = file_get_contents('https://osu.ppy.sh/api/get_beatmaps?k=' . OSU_API_V1_KEY . '&b=' . $id);
    $response = json_decode($eee, true);
    $response[0]['cover_url'] = "https://assets.ppy.sh/beatmaps/" . $response[0]['beatmapset_id'] . "/covers/cover.jpg";
    return $response[0];
}

function spawnbeatmappanel($id)
{
    // unused (for now)
    $beatmap = getbeatmap($id);
    print_r($beatmap);
}

function getuser($id, $bypass_local = false)
{
    // gets a user
    // usage:
    // getuser(2); (gets peppy)
    $response = json_encode(Database::execSelect("SELECT * FROM Ranking WHERE id = ? OR name = ?", "is", array($id, $id)));
    if ($response == null || !isset($response) || $response == "null" || $bypass_local == true) {
        $eee = file_get_contents('https://osu.ppy.sh/api/get_user?k=' . OSU_API_V1_KEY . '&u=' . $id);
        $response = json_decode($eee, true)[0];
        $response['avatar_url'] = "https://a.ppy.sh/" . $id;
        $response['id'] = $response["user_id"];
        $username = $response["username"];
        $response['name'] = $username;
        return $response;
    } else {
        return json_decode($response, true)[0];
    }
    // TODO: this is way too slow. i'm implementing GetUserFromDatabase() for now
}

function GetUserFromDatabase($id)
{
    // gets a user from the database
    // usage:
    // getuser(2); (gets peppy)
    $response = json_encode(Database::execSelect("SELECT * FROM Ranking WHERE id = ?", "i", array($id)));
    if ($response == null || !isset($response) || $response == "null") {
        return null;
    } else {
        return json_decode($response, true)[0];
    }
}

function getmedal($name)
{
    $medals = Database::execSelect("SELECT Medals.medalid AS MedalID " .
        ", Medals.name AS Name " .
        ", Medals.link AS Link " .
        ", Medals.description AS Description " .
        ", Medals.restriction AS Restriction " .
        ", Medals.grouping AS Grouping " .
        ", Medals.instructions AS Instructions " .
        ", Solutions.solution AS Solution " .
        ", Solutions.mods AS Mods " .
        ", MedalStructure.Locked AS Locked " .
        ", (CASE WHEN restriction = 'osu' THEN 2 WHEN restriction = 'taiko' THEN 3 WHEN restriction = 'fruits' THEN 4 WHEN restriction = 'mania' THEN 5 ELSE 1 END) AS ModeOrder " .
        ", Medals.ordering AS Ordering " .
        "FROM Medals " .
        "LEFT JOIN Solutions ON Medals.medalid = Solutions.medalid " .
        "LEFT JOIN MedalStructure ON MedalStructure.MedalID = Medals.medalid " .
        "WHERE Medals.name = ?", "s", array($name));
    return ($medals[0]); // guess what this function does
}

function spawnuserpanel($id)
{
    // Spawns a user panel
    //
    // Usage:
    // spawnuserpanel(2); [spawns a user panel for peppy]

    $user = getuser($id);
    //print_r($user);
    $avatar = $user['avatar_url'];
    $name = $user['name'];
    $countrycode = $user['country_code'];

    echo '<a href="https://osu.ppy.sh/users/' . $id . '" class="osekai__userpanel">
    <img src="' . $avatar . '" class="osekai__userpanel-pfp">
    <img src="https://osu.ppy.sh/images/flags/' . $countrycode . '.png" class="osekai__userpanel-countryflag">
    <p class="osekai__userpanel-username">' . $name . '</p>
    </a>';
}

function pushnotification($title, $message, $userid, $sysid = "", $html = "")
{
    Database::execOperation("INSERT INTO Notifications (SystemID, UserID, Message, Title, HTML, Date) VALUES (?, ?, ?, ?, ?, now()) ON DUPLICATE KEY UPDATE UserID = UserID, Message = Message", "sisss", array((($sysid !== "") ? $sysid : NULL), $userid, $message, $title, $html));
}

// Function for basic field validation (present and neither empty nor only white space
function IsNullOrEmptyString($str)
{
    return (!isset($str) || trim($str) === '');
}

function isbot()
{

    return (isset($_SERVER['HTTP_USER_AGENT'])
        && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
    );
}

function print_home_panel()
{
    global $app;
    global $apps;

    // print errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $html = '<div class="osekai__home-panel">
        <div class="osekai__home-panel-inner">
            <img src="https://www.osekai.net/global/img/branding/vector/' . $apps[$app]['logo'] . '.svg" class="osekai__home-panel-logo">
            <div class="osekai__home-panel-texts">
                <h1><strong>osekai</strong> ' . $apps[$app]['simplename'] . '</h1>
                <p>' . GetStringRaw("apps", $app . ".slogan") . '</p>
            </div>
            </div>
        </div>
        <style>
            .osekai__home-panel {
                background: linear-gradient(0deg, #0006, #0004), linear-gradient(0deg, rgba(' . $apps[$app]['color_dark'] . ', 0.5), rgba(' . $apps[$app]['color_dark'] . ', 0.3)), url("/global/img/' . $apps[$app]['cover'] . '.jpg") no-repeat center center;
                background-size: cover;
    background-position: center;
            }
        </style>';

    echo $html;
}

$tippyLoaded = false;

function tippy()
{
    global $tippyLoaded;
    if (!$tippyLoaded) {
        echo '<script src="/global/js/popper/popper.min.js"></script>
        <script src="/global/js/tippy/tippy-bundle.umd.min.js"></script>';
    }
}

function tippy_headless()
{
    echo '<script src="/global/js/popper/popper.min.js"></script>';
    echo '<script src="/global/js/tippy/tippy-headless.umd.min.js"></script>';
}

function colour_picker()
{
    echo '<link rel="stylesheet" href="/global/css/colourpicker/color-picker.css?v=' . OSEKAI_VERSION . '">';
    echo '<script src="/global/js/colourpicker/color-picker.js?v=' . OSEKAI_VERSION . '"></script>';
    echo '<script src="/global/js/colourpicker.js?v=' . OSEKAI_VERSION . '"></script>';
}

function medal_popup_v2()
{
    include_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/medalHoverV2.php");
}


function isExperimental()
{
    if (MODE == "dev") {
        return true;
    }
    if (isset($_SESSION['options']) && isset($_SESSION['options']['experimental']) && $_SESSION['options']['experimental'] == 1) return true;
    return false;
}
function isRestricted()
{
    global $restrictedState;
    return $restrictedState;
}
//echo "<style>body{ background: -webkit-linear-gradient(rgba(var(--accentdark), 0.2), rgba(var(--accentdark), 0.2)), black; )</style>";
if (isset($app_extra)) {
    if ($app_extra == "other") {
        include_once($_SERVER['DOCUMENT_ROOT'] . "//misc/php/main.php");
    }
}

function getGroupFromId($id)
{
    global $userGroups;
    if ($userGroups == null) {
        $userGroups = Database::execSimpleSelect("SELECT * FROM Groups");
    }
    foreach ($userGroups as $group) {
        if ($group['Id'] == $id) {
            return $group;
        }
    }
}

function badgeHtmlFromGroup($group, $size)
{
    return "<div class=\"osekai__group-badge osekai__group-badge-{$size}\" style=\"--colour: {$group['Colour']}\">{$group['ShortName']}</div>";
}


function rgbToHsl( $r, $g, $b ) {
	$oldR = $r;
	$oldG = $g;
	$oldB = $b;

	$r /= 255;
	$g /= 255;
	$b /= 255;

    $max = max( $r, $g, $b );
	$min = min( $r, $g, $b );

	$h;
	$s;
	$l = ( $max + $min ) / 2;
	$d = $max - $min;

    	if( $d == 0 ){
        	$h = $s = 0; // achromatic
    	} else {
        	$s = $d / ( 1 - abs( 2 * $l - 1 ) );

		switch( $max ){
	            case $r:
	            	$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 ); 
                        if ($b > $g) {
	                    $h += 360;
	                }
	                break;

	            case $g: 
	            	$h = 60 * ( ( $b - $r ) / $d + 2 ); 
	            	break;

	            case $b: 
	            	$h = 60 * ( ( $r - $g ) / $d + 4 ); 
	            	break;
	        }			        	        
	}

	return array( round( $h, 2 ), round( $s*100, 2 ), round( $l*100, 2 ) );
    // don't question the multiplications, it works
}