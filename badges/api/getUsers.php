<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$badges = Database::execSelect("SELECT * FROM Badges WHERE id = ?", "i", array($_GET['badge_id']));
$users = json_decode($badges[0]['users']);

$nu;

for ($i = 0; $i < count($users); $i++) {
    $rankingUser = Database::execSelect("SELECT * FROM Ranking WHERE id = ?", "i", array($users[$i]));
    if ($rankingUser != null)
        $nu[$i] = $rankingUser[0];
    else {
        $apiuser = v2_getUser($users[$i], "osu", false, false);
        if ($apiuser != null) // check if user is restricted
            $decodedApiUser = json_decode($apiuser, true);

        $nu[$i] = null;
        $nu[$i]["id"] = $users[$i];
        $nu[$i]["name"] = $decodedApiUser["username"];
    }
}

echo json_encode($nu);
