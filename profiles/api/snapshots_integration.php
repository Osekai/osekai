<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
// * this needs to be replaced once azelia rolls out. if we forget, it'll probably break the entire site. hopefully just this integration though!

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY `release` DESC");
if($test == null){
    echo "No results found";
    return;
}

$userId = $_GET['userId'];

$final_array = array();

$count = 0;

foreach($test as $t){
    $temp = $t['json'];
    $temp = json_decode($temp, true);

    if($temp['archive_info']['archiver_id'] != null && $temp['archive_info']['archiver_id'] == $userId) {
        $temp['version_info']['upload_date'] = $t['archive_date'];
        $final_array[$count] = $temp;
        $count += 1;
    }
    
}

if(count($final_array) > 0) {
    header('Content-Type: application/json');
    echo json_encode($final_array);
} else {
    echo "No results found";
}