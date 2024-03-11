<?php

$sql = "SELECT MembersRestrictions.*, Ranking.name as Username
FROM MembersRestrictions
lEFT JOIN Ranking ON Ranking.id = MembersRestrictions.UserID
WHERE (Ranking.name LIKE ? OR UserID LIKE ?) AND (Time > ? AND Time < ?)";

if(isset($_REQUEST['strName'])) {
    $name = $_REQUEST['strName'];
} else {
    $name = "";
}
if(isset($_REQUEST['nUserID'])) {
    $id = $_REQUEST['nUserID'];
} else {
    $id = "";
}

$name = "%$name%";
$id = "%$id%";

if(isset($_REQUEST['dDateAfter'])) {
    $dateAfter = $_REQUEST['dDateAfter'];
} else {
    $dateAfter = "0000-00-00 00:00:00";
}
if(isset($_REQUEST['dDateBefore'])) {
    $dateBefore = $_REQUEST['dDateBefore'];
} else {
    $dateBefore = "now()";
}

$types = "siss";
$vars = [$name, $id, $dateAfter, $dateBefore];


//echo $sql;
//echo "<br>";
//echo $types;
//echo "<br>";
//echo print_r($vars, true);
echo json_encode(Database::execSelect($sql, $types, $vars));