<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$groups = Database::execSimpleSelect("SELECT * FROM Groups ORDER BY `Order`");

for($x = 0; $x < count($groups); $x++) {
    $groups[$x]['Users'] = Database::execSelect("SELECT GroupAssignments.*, Ranking.name FROM GroupAssignments LEFT JOIN Ranking ON Ranking.id = GroupAssignments.UserId
    WHERE GroupAssignments.GroupId = ?", "i", [$groups[$x]['Id']]);
}
$groupsFinal = [];
foreach($groups as $group) {
    if($group['Hidden'] == 0) {
        $groupsFinal[] = $group;
    }
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($groupsFinal);