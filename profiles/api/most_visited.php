<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

// select everything newer than a week old
$visits = Database::execSimpleSelect("SELECT * FROM ProfilesVisited WHERE date > DATE_SUB(NOW(), INTERVAL 2 WEEK) AND (visited_by != visited_id) ORDER BY date DESC");




$combinedVisits;


// visits has one entry per visited user. We want to combine them into one entry per user overall. with ['id'] being their id and ['visits'] being the count

for($i = 0; $i < count($visits); $i++) {
    $visit = $visits[$i];
    $visitedId = $visit['visited_id'];
    $visitedBy = $visit['visited_by'];
    $date = $visit['date'];
    
    if(!isset($combinedVisits[$visitedId])) {
        $combinedVisits[$visitedId] = array('visited_id' => $visitedId, 'visits' => 0);
    }
    
    $combinedVisits[$visitedId]['visits'] += 1;
}

// order by most visits
usort($combinedVisits, function($a, $b) {
    return $b['visits'] - $a['visits'];
});


for($i = 0; $i < count($combinedVisits); $i++) {
    $user = Database::execSelect("SELECT * FROM ProfilesUserinfo WHERE osuID = ?", "i", array($combinedVisits[$i]['visited_id']))[0];
    $combinedVisits[$i]['userdata'] = $user;
}

// limit to 8
$combinedVisits = array_slice($combinedVisits, 0, 12);

echo json_encode($combinedVisits);
?>