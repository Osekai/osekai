<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");
// print errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
$svg = "";

// if not being visited through banner.svg, return 403
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, "banner.svg") === false && MODE == "production") {
    header("HTTP/1.1 418 I'm a teapot");

    echo "<strong>418 I'm a teapot</strong>";
    exit();
}
$userid = $_GET['id'];
$id = $userid;

$allowedModes = array("osu", "taiko", "fruits", "mania");



$userinfo = [];
$userCache = Caching::getCache("profiles_banner_user_" . $userid);
if ($userCache != null) {
    $userinfo = (array)json_decode($userCache, true);
} else {
    $userinfo = (array)json_decode(v2_getUser($userid), true);
    Caching::saveCache("profiles_banner_user_" . $userid, 7200, json_encode($userinfo));
}



$medals = count($userinfo['user_achievements']);
$medalsMax = Database::execSimpleSelect("SELECT COUNT(*) FROM `Medals`");
$medalsPercent = ($medals / $medalsMax[0]['COUNT(*)']) * 100;
// round to XX.XX
$medalsPercent = round($medalsPercent, 2);

$icon_standard = "M21.0426 11.5C21.0426 6.22957 16.7704 1.95745 11.5 1.95745C6.22957 1.95745 1.95745 6.22957 1.95745 11.5C1.95745 16.7704 6.22957 21.0426 11.5 21.0426C16.7704 21.0426 21.0426 16.7704 21.0426 11.5ZM7.02234 0.905319C8.44149 0.303404 9.94872 0 11.5 0C13.0513 0 14.5585 0.303404 15.9777 0.905319C17.3479 1.48277 18.5762 2.30979 19.6332 3.36681C20.6853 4.42383 21.5172 5.65213 22.0947 7.02234C22.6966 8.44149 23 9.94872 23 11.5C23 13.0513 22.6966 14.5585 22.0947 15.9777C21.5172 17.3479 20.6902 18.5762 19.6332 19.6332C18.5762 20.6853 17.3479 21.5172 15.9777 22.0947C14.5585 22.6966 13.0513 23 11.5 23C9.94872 23 8.44149 22.6966 7.02234 22.0947C5.65213 21.5172 4.42383 20.6902 3.36681 19.6332C2.31468 18.5762 1.48277 17.3479 0.905319 15.9777C0.303404 14.5585 0 13.0513 0 11.5C0 9.94872 0.303404 8.44149 0.905319 7.02234C1.48277 5.65213 2.30979 4.42383 3.36681 3.36681C4.42383 2.31468 5.65213 1.48277 7.02234 0.905319ZM11.5001 4.15961C7.44818 4.15961 4.15967 7.44812 4.15967 11.5C4.15967 15.5519 7.44818 18.8405 11.5001 18.8405C15.552 18.8405 18.8405 15.5519 18.8405 11.5C18.8405 7.44812 15.552 4.15961 11.5001 4.15961Z";
$icon_taiko = "M21.0426 11.5C21.0426 6.22957 16.7704 1.95745 11.5 1.95745C6.22957 1.95745 1.95745 6.22957 1.95745 11.5C1.95745 16.7704 6.22957 21.0426 11.5 21.0426C16.7704 21.0426 21.0426 16.7704 21.0426 11.5ZM7.02234 0.905319C8.44149 0.303404 9.94872 0 11.5 0C13.0513 0 14.5585 0.303404 15.9777 0.905319C17.3479 1.48277 18.5762 2.30979 19.6332 3.36681C20.6853 4.42383 21.5172 5.65213 22.0947 7.02234C22.6966 8.44149 23 9.94872 23 11.5C23 13.0513 22.6966 14.5585 22.0947 15.9777C21.5172 17.3479 20.6902 18.5762 19.6332 19.6332C18.5762 20.6853 17.3479 21.5172 15.9777 22.0947C14.5585 22.6966 13.0513 23 11.5 23C9.94872 23 8.44149 22.6966 7.02234 22.0947C5.65213 21.5172 4.42383 20.6902 3.36681 19.6332C2.31468 18.5762 1.48277 17.3479 0.905319 15.9777C0.303404 14.5585 0 13.0513 0 11.5C0 9.94872 0.303404 8.44149 0.905319 7.02234C1.48277 5.65213 2.30979 4.42383 3.36681 3.36681C4.42383 2.31468 5.65213 1.48277 7.02234 0.905319ZM4.15967 11.5C4.15967 7.44812 7.44818 4.15961 11.5001 4.15961C15.552 4.15961 18.8405 7.44812 18.8405 11.5C18.8405 15.5519 15.552 18.8405 11.5001 18.8405C7.44818 18.8405 4.15967 15.5519 4.15967 11.5ZM10.2767 6.763C8.16753 7.30619 6.60647 9.22449 6.60647 11.5C6.60647 13.7755 8.16754 15.6939 10.2767 16.2419L10.2767 6.763ZM12.7235 6.763L12.7235 16.2371C14.8326 15.6939 16.3937 13.7756 16.3937 11.5C16.3937 9.2245 14.8327 7.30619 12.7235 6.763Z";
$icon_catch = "M21.0426 11.5C21.0426 6.22957 16.7704 1.95745 11.5 1.95745C6.22957 1.95745 1.95745 6.22957 1.95745 11.5C1.95745 16.7704 6.22957 21.0426 11.5 21.0426C16.7704 21.0426 21.0426 16.7704 21.0426 11.5ZM7.02234 0.905319C8.44149 0.303404 9.94872 0 11.5 0C13.0513 0 14.5585 0.303404 15.9777 0.905319C17.3479 1.48277 18.5762 2.30979 19.6332 3.36681C20.6853 4.42383 21.5172 5.65213 22.0947 7.02234C22.6966 8.44149 23 9.94872 23 11.5C23 13.0513 22.6966 14.5585 22.0947 15.9777C21.5172 17.3479 20.6902 18.5762 19.6332 19.6332C18.5762 20.6853 17.3479 21.5172 15.9777 22.0947C14.5585 22.6966 13.0513 23 11.5 23C9.94872 23 8.44149 22.6966 7.02234 22.0947C5.65213 21.5172 4.42383 20.6902 3.36681 19.6332C2.31468 18.5762 1.48277 17.3479 0.905319 15.9777C0.303404 14.5585 0 13.0513 0 11.5C0 9.94872 0.303404 8.44149 0.905319 7.02234C1.48277 5.65213 2.30979 4.42383 3.36681 3.36681C4.42383 2.31468 5.65213 1.48277 7.02234 0.905319ZM16.2126 11.5C16.2126 12.5135 15.391 13.3351 14.3775 13.3351C13.364 13.3351 12.5424 12.5135 12.5424 11.5C12.5424 10.4865 13.364 9.66489 14.3775 9.66489C15.391 9.66489 16.2126 10.4865 16.2126 11.5ZM10.0956 9.60619C11.1091 9.60619 11.9307 8.78458 11.9307 7.77108C11.9307 6.75758 11.1091 5.93597 10.0956 5.93597C9.0821 5.93597 8.2605 6.75758 8.2605 7.77108C8.2605 8.78458 9.0821 9.60619 10.0956 9.60619ZM11.9307 15.2338C11.9307 16.2473 11.1091 17.069 10.0956 17.069C9.0821 17.069 8.2605 16.2473 8.2605 15.2338C8.2605 14.2203 9.0821 13.3987 10.0956 13.3987C11.1091 13.3987 11.9307 14.2203 11.9307 15.2338Z";
$icon_mania = "M21.0426 11.5C21.0426 6.22957 16.7704 1.95745 11.5 1.95745C6.22957 1.95745 1.95745 6.22957 1.95745 11.5C1.95745 16.7704 6.22957 21.0426 11.5 21.0426C16.7704 21.0426 21.0426 16.7704 21.0426 11.5ZM7.02234 0.905319C8.44149 0.303404 9.94872 0 11.5 0C13.0513 0 14.5585 0.303404 15.9777 0.905319C17.3479 1.48277 18.5762 2.30979 19.6332 3.36681C20.6853 4.42383 21.5172 5.65213 22.0947 7.02234C22.6966 8.44149 23 9.94872 23 11.5C23 13.0513 22.6966 14.5585 22.0947 15.9777C21.5172 17.3479 20.6902 18.5762 19.6332 19.6332C18.5762 20.6853 17.3479 21.5172 15.9777 22.0947C14.5585 22.6966 13.0513 23 11.5 23C9.94872 23 8.44149 22.6966 7.02234 22.0947C5.65213 21.5172 4.42383 20.6902 3.36681 19.6332C2.31468 18.5762 1.48277 17.3479 0.905319 15.9777C0.303404 14.5585 0 13.0513 0 11.5C0 9.94872 0.303404 8.44149 0.905319 7.02234C1.48277 5.65213 2.30979 4.42383 3.36681 3.36681C4.42383 2.31468 5.65213 1.48277 7.02234 0.905319ZM10.2766 17.666C10.2766 18.3413 10.8247 18.8894 11.5 18.8894C12.1753 18.8894 12.7234 18.3413 12.7234 17.666V5.33406C12.7234 4.65874 12.1753 4.11066 11.5 4.11066C10.8247 4.11066 10.2766 4.65874 10.2766 5.33406V17.666ZM6.36182 14.1915C6.36182 14.8668 6.9099 15.4149 7.58522 15.4149C8.26054 15.4149 8.80862 14.8668 8.80862 14.1915V8.80855C8.80862 8.13323 8.26054 7.58514 7.58522 7.58514C6.9099 7.58514 6.36182 8.13323 6.36182 8.80855V14.1915ZM15.4148 15.4149C14.7395 15.4149 14.1914 14.8668 14.1914 14.1915V8.80855C14.1914 8.13323 14.7395 7.58514 15.4148 7.58514C16.0901 7.58514 16.6382 8.13323 16.6382 8.80855V14.1915C16.6382 14.8668 16.0901 15.4149 15.4148 15.4149Z";

//$svg = str_replace("[PC]", $clubperc, $svg);

$background = "style1_background.svg";
$foreground = "style1_foreground.svg";

$club = "club-none";

if ($medalsPercent >= 95) {
    $club = "club-95";
    $clubperc = "95";
} else if ($medalsPercent >= 90) {
    $club = "club-90";
    $clubperc = "90";
} else if ($medalsPercent >= 80) {
    $club = "club-80";
    $clubperc = "80";
} else if ($medalsPercent >= 60) {
    $club = "club-60";
    $clubperc = "60";
} else if ($medalsPercent >= 40) {
    $club = "club-40";
    $clubperc = "40";
} else {
    $club = "club-none";
}

$backgroundStyles = [
    "clubglows" => "style1_background.svg",
    "test" => "test.svg",
];

$foregroundStyles = [
    "medal-oriented" => "style1_foreground.svg"
];



$user = Database::execSelect("SELECT * FROM ProfilesBanners WHERE UserID = ?", "i", array($userid));
if ($user == null || count($user) == 0) {
    Database::execOperation("INSERT INTO `ProfilesBanners` (`UserID`, `Background`, `Foreground`, `CustomGradient`, `CustomSolid`, `CustomImage`) VALUES (?, 'clubglows', 'medal-oriented', '', '', '');", "i", array($userid));
    $user = Database::execSelect("SELECT * FROM ProfilesBanners WHERE UserID = ?", "i", array($userid));
}

$user = $user[0];


$svg .= '<svg width="1400" height="250" viewBox="0 0 1400 250" fill="none" xmlns="http://www.w3.org/2000/svg"
xmlns:xlink="http://www.w3.org/1999/xlink" class="' . $club . '">';



$customstyle = "";

function getRotation($rotation)
{
    $pi = $rotation * (pi() / 180);
    $coords = array(
        'x1' => round(50 + sin($pi) * 50) . '%',
        'y1' => round(50 + cos($pi) * 50) . '%',
        'x2' => round(50 + sin($pi + pi()) * 50) . '%',
        'y2' => round(50 + cos($pi + pi()) * 50) . '%',
    );
    return $coords;
}

$x1 = 0;
$y1 = 0;
$x2 = 0;
$y2 = 0;
$angle = 90;

if ($user['Background'] == "custom") {
    $customstyle .= "<style>";
    $col1 = "";
    $col2 = "";

    if ($user['CustomStyle'] == "solid") {
        $col1 = $user['CustomSolid'];
        $col2 = $user['CustomSolid'];
    } else if ($user['CustomStyle'] == "gradient") {
        $gradient = json_decode($user['CustomGradient']);
        $col1 = $gradient[0];
        $col2 = $gradient[1];
        $angle = $gradient[2];
    }
    $customstyle .= "* {
        --accent1: " . $col1 . ";
        --accent2: " . $col2 . ";
        --accent3: " . $col1 . ";
        --accent4: " . $col2 . ";
        --angle: " . $angle . ";
    }";
    $customstyle .= "</style>";
} else {
    $background = $backgroundStyles[$user["Background"]];
    $foreground = $foregroundStyles[$user["Foreground"]];
}

$rot = getRotation($angle);
$x1 = $rot['x1'];
$y1 = $rot['y1'];
$x2 = $rot['x2'];
$y2 = $rot['y2'];

$svg .= file_get_contents("default_goal_colours.svg");

$svg .= $customstyle;

$svg .= file_get_contents($background);

$svg .= file_get_contents($foreground);

$svg .= '</svg>';



$username = $userinfo['username'];
$pp = $userinfo['statistics']['pp'];
$rank = $userinfo['statistics']['global_rank'];
$rarestmedal = 0;

$achievements = $userinfo['user_achievements'];


$highest = 500;
$highestMedal = [];

foreach ($achievements as $achievement) {
    if ($achievement['frequency'] != null && $achievement['frequency'] != 0 && $achievement['frequency'] < $highest) {
        $highest = $achievement['frequency'];
        $highestMedal = $achievement;
    }
}


$rarestmedal = $highestMedal['name'];

$rarestmedal_image = $highestMedal['link'];


$medalrank = $userinfo['user_achievements_total']['global_rank'];

$svg = str_replace("[USN]", $username, $svg);
$svg = str_replace("[PP]", $pp, $svg);
$svg = str_replace("[RNK]", $rank, $svg);
$svg = str_replace("[RMN]", $rarestmedal, $svg);

if (!isset($_GET['no-angle'])) {
    $svg = str_replace("{GX1}", $x1, $svg);
    $svg = str_replace("{GY1}", $y1, $svg);
    $svg = str_replace("{GX2}", $x2, $svg);
    $svg = str_replace("{GY2}", $y2, $svg);
}

$rarestmedalpercentage = $highestMedal['frequency'];
// round to XX.XX
$rarestmedalpercentage = round($rarestmedalpercentage, 2);
$svg = str_replace("[RMPC]", $rarestmedalpercentage, $svg);

function convertImageToB64($image, $cache)
{
    $cachec = "";
    if ($cachec = Caching::getCache($cache)) {
        return $cachec;
    }


    $rarestmedal_image = file_get_contents($image);
    $rarestmedal_image_b64 = base64_encode($rarestmedal_image);
    $type = new finfo(FILEINFO_MIME_TYPE);
    $mime = $type->buffer($rarestmedal_image);
    $type = explode("/", $mime)[0];
    $prefix = "data:image/";
    if ($type == IMAGETYPE_GIF) {
        $prefix .= "gif;base64,";
    } else if ($type == IMAGETYPE_JPEG) {
        $prefix .= "jpeg;base64,";
    } else if ($type == IMAGETYPE_PNG) {
        $prefix .= "png;base64,";
    } else {
        $prefix .= "png;base64,";
    }
    $rarestmedal_image_b64 = $prefix . $rarestmedal_image_b64;

    Caching::saveCache($cache, 7200, $rarestmedal_image_b64);

    return $rarestmedal_image_b64;
}


$svg = str_replace("[MEDALICON]", convertImageToB64($rarestmedal_image, "profiles_banner_user_" . $userid . "_rarestmedal_img"), $svg);

$svg = str_replace("[MC]", $medals, $svg);
$svg = str_replace("[MRNK]", $medalrank, $svg);
try {
    $svg = str_replace("[PC]", $clubperc, $svg);
} catch (Exception $e) {
    $svg = str_replace("[PC]", "", $svg);
}

if ($userinfo['playmode'] == "osu") {
    $svg = str_replace("[GMICON]", $icon_standard, $svg);
} else if ($userinfo['playmode'] == "taiko") {
    $svg = str_replace("[GMICON]", $icon_taiko, $svg);
} else if ($userinfo['playmode'] == "fruits") {
    $svg = str_replace("[GMICON]", $icon_catch, $svg);
} else if ($userinfo['playmode'] == "mania") {
    $svg = str_replace("[GMICON]", $icon_mania, $svg);
}

$svg = str_replace("[ID]", $id, $svg);

$profile_image = "https://a.ppy.sh/{$id}";
$svg = str_replace("[PIC]", convertImageToB64($profile_image, "profiles_banner_user_" . $userid . "_pfp"), $svg);

// max bar width is 1128 (100%), base off of medal percentage
$medalwidth = 1128;
$medalwidth = $medalwidth * $medalsPercent / 100;
$svg = str_replace("[MW]", $medalwidth, $svg);

$showmedalrank = 1;

if ($medalrank == null || $medalrank > 4000) {
    $showmedalrank = 0;
}

if ($club == "club-none") {
    $svg = str_replace("<tspan>% Club</tspan> with ", "", $svg);
}

$svg = str_replace("[SHOW_MEDAL_RANK]", $showmedalrank, $svg);

if (!isset($_GET['norounding'])) {
    $svg = str_replace("{ROUNDED}", '<clipPath id="clip0_6_552">
    <rect width="1400" height="250" rx="24" fill="white"/>
    </clipPath>', $svg);
}

header('Content-Type: image/svg+xml');
echo $svg;

if ($_SERVER['REMOTE_ADDR'] == "162.243.141.52") {
    $url = "https://discord.com/api/webhooks/1024742472938705018/rDTFdgcS96GGF6avXwK-3cZnWq1S1Ggg_JWkfxflyIYd8sCFpVzrolbE92daxMdccnqy";
    $data = array('content' => "User https://osu.ppy.sh/users/" . $_GET['id'] . " loaded through osu!, they probably have it on their me! page");
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}
