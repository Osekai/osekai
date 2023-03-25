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
        // check if user is restricted
        // if its restricted then ignore it
        if ($apiuser != null) {
            $decodedApiUser = json_decode($apiuser, true);

            $nu[$i] = null;
            $nu[$i]["id"] = $users[$i];
            $nu[$i]["name"] = $decodedApiUser["username"];

            // put the user in the queue for next scripts run
            Database::execOperation("INSERT INTO Members (id) VALUES (?) ON DUPLICATE KEY UPDATE id = id", "i", [$users[$i]]);
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($nu);
