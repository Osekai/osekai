<?php
$app = "snapshots";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
css();

if(isset($_GET['version'])){
    $verArray = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY `release` DESC");

    $nover = false;

    foreach($verArray as $t){
        $decoded = json_decode($t['json'], true);

        if($decoded['version_info']['version'] == $_GET['version']){
            echo $t['id'];
            redirect($rooturl . "/snapshots?fromLegacy=true&version=" . $t['id']);
        }else{
            $nover = true;
        }
    }

    if($nover == true){
        redirect($rooturl . "/snapshots?fromLegacy=true");
    }
}else{
    redirect($rooturl . "/snapshots");
}