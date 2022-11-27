<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(loggedin() && !isRestricted()) {
    $thisUser = $_SESSION['osu']['id'];
    $visiting = $_GET['userId'];
    Database::execOperation("INSERT INTO ProfilesVisited (visited_by, visited_id) VALUES (?, ?)", "ii", array($thisUser, $visiting));
}