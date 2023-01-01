<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

print_r($_FILES);
echo "<br><br>";

print_r($_POST);
echo "<br><br>";

$json = file_get_contents("template.json");

$templateJson = json_decode($json, true);

$templateJson["version_info"]['release'] = strtotime($_POST['releaseDate']);
$templateJson["version_info"]['name'] = $_POST['Name'];
$templateJson["version_info"]['version'] = $_POST['Version'];

$templateJson["archive_info"]['archiver'] = $_POST['archiverName'];
$templateJson["archive_info"]['archiver_id'] = $_POST['archiverID'];
$templateJson["archive_info"]['description'] = $_POST['description'];

$autoupdate = false;
if (isset($_POST['autoUpdate']) && $_POST['autoUpdate'] == "on") {
    $autoupdate = true;
} else {
    $autoupdate = false;
}
$templateJson["archive_info"]['auto_update'] = $autoupdate;



$requiresserver = false;
if (isset($_POST['requiresServer']) && $_POST['requiresServer'] == "on") {
    $requiresserver = true;
} else {
    $requiresserver = false;
}
$templateJson["archive_info"]['requires_supporter'] = $requiresserver;

$group = 1;

if ($_POST['group'] == "stable") {
    $group = 1;
}

if ($_POST['group'] == "lazer") {
    $group = 2;
}

$templateJson["archive_info"]['group'] = $group;


if (isset($_POST['video'])) {
    $templateJson["archive_info"]['video'] = $_POST['video'];
}

if (isset($_POST['extraInfo'])) {
    $templateJson["archive_info"]['extra_info'] = $_POST['extraInfo'];
}

$key = 0;


if (isset($_POST['downloadName'])) {
    foreach ($_POST['downloadName'] as $a) {
        $downloadLink = $_POST['downloadLink'][$key];
        $downloadArray = array($a, $_POST['downloadLink'][$key]);
        array_push($templateJson['downloads'], $downloadArray);
        $key += 1;
    }
}


$ext = pathinfo($_FILES['downloadFile']['name'], PATHINFO_EXTENSION);

$tmp_name = $_FILES["downloadFile"]["tmp_name"];
$name = basename($_FILES["downloadFile"]["name"]);

if (!file_exists("../versions/" . $templateJson["version_info"]["version"])) {
    mkdir("../versions/" . $templateJson["version_info"]["version"], 0777, true);
}

move_uploaded_file($tmp_name, "../versions/" . $templateJson["version_info"]["version"] . "/" . $templateJson["version_info"]["version"] . "." . $ext);

$templateJson['downloads']["main"]["link"] = $templateJson["version_info"]["version"] . "." . $ext;

$key = 1;
foreach ($_FILES["screenshots"]["name"] as $a) {
    $ext = pathinfo($a, PATHINFO_EXTENSION);
    array_push($templateJson['screenshots'], $templateJson["version_info"]["version"] . "_" . $key . "." . $ext);

    $realkey = $key - 1;

    $tmp_name = $_FILES["screenshots"]["tmp_name"][$realkey];
    $name = basename($_FILES["screenshots"]["name"][$realkey]);
    move_uploaded_file($tmp_name, "../versions/" . $templateJson["version_info"]["version"] . "/" . $templateJson["version_info"]["version"] . "_" . $key . "." . $ext);


    $key += 1;
}

$image = "../versions/" . $templateJson['version_info']['version'] . "/" . $templateJson['screenshots'][0];
echo $image;
if(mime_content_type($image) == "image/png"){
    $img = imagecreatefrompng($image);
    echo "<br>generating from png</br>";
}else {
    $img = imagecreatefromjpeg($image);
    echo "<br>generating from jpg</br>";
}
$widthMultiplier = imagesx($img) / imagesy($img); // imgx / imgy
// let's say it's 1920x1080. 1920/1080 1.7777777777777...
// now let's downscale this to 1280x720, we can set the height to 720
// then set the width to 720*1.7777777777 (aka 720*widthMultiplier)
// that leaves us with 1280
// which gives us perfect downscaling to 720p without changing aspect ratio
if(file_exists("versions/" . $temp['version_info']['version'] . "/" . "thumbnail.jpg")){
    unlink("versions/" . $temp['version_info']['version'] . "/" . "thumbnail.jpg");
}
$imgResize = imagescale($img, 720*$widthMultiplier, 720);
$jpg = imagejpeg($imgResize, "versions/" . $temp['version_info']['version'] . "/" . "thumbnail.jpg", 50);
//echo json_encode($templateJson);

$a = json_encode($templateJson);
$b = 0;
$c = 0;
$d = date('Y-m-d H:i:s', $templateJson['version_info']['release']);

Database::execOperation("INSERT INTO SnapshotVersions (`json`, `views`, `downloads`, `release`) VALUES (?, ?, ?, ?)", "siis", array($a, $b, $c, $d));





$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY archive_date DESC LIMIT 1");

$thisver = null;

foreach($test as $t){
    $thisver = $t;
}

$webhookurl = SNAPSHOTS_WEBHOOK;

$timestamp = date("c", strtotime("now"));
print_r(json_decode($t['json'], true));

$json_data = json_encode([
    "content" => "New version! - https://osekai.net/snapshots/?version=" . $t['id'],
    "username" => "(archived by) : " . json_decode($t['json'], true)['archive_info']['archiver'],
    "tts" => false,

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init( $webhookurl );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
echo $response;
curl_close( $ch );

Caching::wipeCache("snapshots_api");
