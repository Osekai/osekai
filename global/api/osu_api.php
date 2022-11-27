<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");


if(isset($_POST['UserID'])) {
    $data = null;
    if(isset($_POST['Mode']))
    {
        $data = v2_getUser($_POST['UserID'], $_POST['Mode'], true, $_POST['UseAllMedals']);
    }
    else
    {
        $data = v2_getUser($_POST['UserID']);
    }
    
    
    echo json_encode($data); // we have to do this before decoding because W H A T ? ? ? i don't know but it works :(
    $data = json_decode($data, true);
    
    $id = $_POST['UserID'];
    $rank = 0;

    $username = $data['username'];

    Database::execOperation("INSERT INTO ProfilesUserinfo (osuID, Username, Rank) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Username = ?, Rank = ?", "isisi", array($id, $username, $rank, $username, $rank));
}

if(isset($_POST['SearchQuery'])) {
    echo json_encode(v2_search($_POST['SearchQuery']));
}
