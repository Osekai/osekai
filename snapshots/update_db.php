<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);


$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions");

$final_array = array();

$count = 0;

function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}

foreach($test as $t){
    $temp = $t['json'];
    $temp = json_decode($temp, true);
    $temp["stats"]["views"] = $t['views'];
    $temp["stats"]["downloads"] = $t['downloads'];
    $temp["version_info"]["id"] = $t['id'];
    if($temp["archive_info"]["archiver"] == "Hubz") {
        $temp["archive_info"]["archiver_id"] = 10379965;
    }
    //$temp["archive_info"]["video"] = NULL;
    //$temp["archive_info"]["extra_info"] = NULL;
    Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($temp), $t['id']));
    $release = $temp["version_info"]["release"];
    $release = date('Y-m-d H:i:s', $release);
    Database::execOperation("UPDATE SnapshotVersions SET `release` = ? WHERE id = ?", "si", array($release, $t['id']));
    
    $image = "versions/" . $temp['version_info']['version'] . "/" . $temp['screenshots'][0];
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


    //echo json_encode($temp);
}