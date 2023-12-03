<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$medal_id = $_POST['medal_id'];
$value = $_POST['value'];

if(count(Database::execSelect("SELECT * FROM MedalVote WHERE user_id = ? AND medal_id = ?", "ii", [$_SESSION['osu']['id'], $medal_id])) > 0) {
    echo "Already Voted";
    exit;
}


$hasMedal = false;

$userdata = json_decode(v2_getUser($_SESSION['osu']['id']), true);
foreach($userdata["user_achievements"] as $medal) {
    if($medal['achievement_id'] == $medal_id) {
        $hasMedal = true;
    }
}



$hasmedalint = 0;
if($hasMedal) $hasmedalint = 1;

Database::execOperation("INSERT INTO `MedalVote` (`user_id`, `medal_id`, `vote_value`, `has_medal`, `date`)
VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP);", "iiii", [$_SESSION['osu']['id'], $medal_id, $value, $hasmedalint]);