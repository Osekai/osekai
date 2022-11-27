<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

// report errors
error_reporting(E_ALL);
ini_set('display_errors', 0);


// respond with json
header('Content-Type: application/json');

$query = $_GET['query'];


$response = array();


$response['query'] = $query;

$profiles = json_decode(v2_search($query), true);
if(isset($profiles['user']['data'])) {
// cut profiles to 5 in length
    $profiles = $profiles['user']['data'];
    $profiles = array_slice($profiles, 0, 10);

    $response['profiles'] = $profiles;
} else {
    $response['profiles'] = $profiles;
}



$response['medals'] = null; // TODO
// we can use a database for this one :D
$medquery = "%" . $query . "%";
$response['medals'] = Database::execSelect("SELECT * FROM Medals WHERE `name` LIKE ? LIMIT 5", "s", array($medquery));



$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY `downloads` DESC");


$response['snapshots'] = array();

$count = 0;
$count2 = 0;

$response['debug'] = array();

foreach($test as $t) {
    $temp = $t['json'];
    $temp = json_decode($temp, true);
    $temp["version_info"]["id"] = $t['id'];
    if(strpos($temp['version_info']['version'], $query) !== false)
    {
        if($count < 5){
            $response['snapshots'][$count] = $temp;
            $count++;
        }
    }

    //$response['debug'][$count2]['name'] = $temp['version_info']['version'];
    //$response['debug'][$count2]['query'] = $query;
    //$response['debug'][$count2]['strpos'] = strpos($temp['version_info']['version'], $query);
    //$count2++;
}


echo json_encode($response);