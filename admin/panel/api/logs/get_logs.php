<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
$apps = "all";
$filter = "";
$user = "";

if(isset($_POST['apps'])) $apps = $_POST['apps'];
if(isset($_POST['filter'])) $filter = $_POST['filter'];
if(isset($_POST['user'])) $user = $_POST['user'];

$sql = "SELECT AdminLogs.*, Ranking.name AS username 
FROM AdminLogs
LEFT JOIN Ranking as Ranking on Ranking.id = AdminLogs.user WHERE ";
$types = "";
$vars = [];

$per_page = 50;
$offset = 0;
if(isset($_POST['offset'])) $offset = $_POST['offset']; // basically the page

if($apps != "all") {
    foreach(explode(",", $apps) as $app) {
        $sql .= "AdminLogs.app = ? AND ";
        $types .= "i";
        $vars[] = $app;
    }
}

if($user != "") {
    $sql .= "AdminLogs.user = ? AND ";
    $types .= "i";
    $vars[] = $user;
}

if($filter != "") {
    $sql .= "AdminLogs.data LIKE ? AND ";
    $types .= "s";
    $vars[] = "%".$filter."%";
}

// remove trailing AND
if(str_ends_with($sql, " AND ")) {
    $sql = substr($sql, 0, -5);
}

if(str_ends_with($sql, " WHERE ")) {
    $sql = substr($sql, 0, -7);
}

$sql .= " ORDER BY AdminLogs.id DESC LIMIT ? OFFSET ?";
$types .= "ii";
$vars[] = $per_page;
$vars[] = $offset;
if(true == false) {
echo "sql:" . $sql . "<br>";
echo "types:" . $types . "<br>";
echo "data:";
print_r($vars);
echo "<br>";
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(Database::execSelect($sql, $types, $vars));
