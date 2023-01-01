<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$cache = Caching::getCache("snapshots_api");
if($cache != null){
    echo $cache;
    exit;
}

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY `release` DESC");

$final_array = array();

$count = 0;

foreach($test as $t){
    $temp = $t['json'];
    $temp = json_decode($temp, true);
    $temp["stats"]["views"] = $t['views'];
    $temp["stats"]["downloads"] = $t['downloads'];
    $temp["version_info"]["id"] = $t['id'];
    if(strtotime($t['archive_date']) > strtotime("-7 days")){
        $temp['archive_info']['new'] = true;
        
    }
    
    $temp['archive_info']['upload_date'] = $t['archive_date'];
    if($temp['archive_info']['archiver_id'] != null) {
        $temp['archive_info']['pfp'] = "https://a.ppy.sh/" . $temp['archive_info']['archiver_id'];
        //$user = getuser($temp['archive_info']['archiver_id']);
        //$temp['archive_info']['fetched_username'] = $user['username'];
    }
    $final_array[$count] = $temp;
    $count += 1;
}

header('Content-Type: application/json');
echo json_encode($final_array);

Caching::saveCache("snapshots_api", "7200", json_encode($final_array));
?>