<?php
include("../../config.php");

$apiKey = CROWDIN_API_KEY;

$key = LANG_UPDATE_KEY;
if($_GET['key'] != $key)
{
    die("Invalid key");
}

// get latest builds
$url = "https://api.crowdin.com/api/v2/projects/514246/translations/builds";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $apiKey,
    "Content-Type: application/json"
));
$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result, true);
$latestBuildID = $result["data"][0]['data']["id"];
echo "latestBuildID = " . $latestBuildID . ";<br>";

// download latest build to tmp/
$url = "https://api.crowdin.com/api/v2/projects/514246/translations/builds/" . $latestBuildID . "/download";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $apiKey,
    "Content-Type: application/json"
));
$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result, true);
$url = $result["data"]["url"];
echo "url = " . $url . ";<br>";

// download to tmp/ (example url: https:\/\/crowdin-packages.downloads.crowdin.com\/66506251\/514246\/0\/pack.zip)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
// write to tmp/pack.zip
if(!file_exists("tmp")) {
    mkdir("tmp");
}
$file = fopen("tmp/pack.zip", "w");
fwrite($file, $result);
fclose($file);

// unzip to tmp/langs
if(!file_exists("tmp/langs")) {
    mkdir("tmp/langs");
}
$zip = new ZipArchive;
$res = $zip->open('tmp/pack.zip');
if ($res === TRUE) {
    $zip->extractTo('tmp/langs');
    $zip->close();
}
// move each folder to current, overwriting if necessary
$langs = scandir("tmp/langs");
// move to ./
// print errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}

foreach($langs as $lang) {
    if($lang != "." && $lang != "..") {
        if(file_exists($lang)) {
            echo "deleting " . $lang . "<br>";
            deleteDirectory("./" . $lang);
        }
        rename("tmp/langs/" . $lang, $lang);
        echo "renaming tmp/langs/" . $lang . " to " . $lang . "<br>";
    }
}