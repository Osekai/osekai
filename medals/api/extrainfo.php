<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
$id = $_GET['id'];
$info = Database::execSelect("SELECT date, firstachieveddate, firstachievedby FROM Medals WHERE medalid = ?", "i", [$id])[0];

if ($info['firstachievedby'] != null) {
    $temp = ["id" => $info['firstachievedby']];
    $info['firstachievedby'] = $temp;
    $userinfo = Database::execSelect("SELECT name FROM Ranking WHERE id = ?", "i", [$info['firstachievedby']['id']]);
    if (count($userinfo) > 0) {
        $info['firstachievedby']['username'] = $userinfo[0]['name'];
    } else {
        // this user isn't in the ranking
        // Add the user to Members so it gets procesed later if its not already in the queue
        Database::execOperation("INSERT INTO Members (id) VALUES (?) ON DUPLICATE KEY UPDATE id = id", "i", array($info['firstachievedby']['id']));
        $info['firstachievedby'] = null;
    }
}

echo json_encode($info);
