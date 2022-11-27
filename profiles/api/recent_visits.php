<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(loggedin()) {
    $visits = Database::execSelect("SELECT * FROM ProfilesVisited WHERE visited_by = ? ORDER BY date DESC", "i", array($_SESSION['osu']['id']));
    
    $final = array();

    
    //for($i = 0; $i < count($visits); $i++) {
    //    $user = Database::execSelect("SELECT * FROM ProfilesUserinfo WHERE osuID = ?", "i", array($visits[$i]['visited_id']))[0];
    //    $visits[$i]['userdata'] = $user;
    //}
    
    foreach($visits as $visit) {
        foreach($final as $f) {
            if($f['visited_id'] == $visit['visited_id']) {
                continue 2;
            }
        }
        $user = Database::execSelect("SELECT * FROM ProfilesUserinfo WHERE osuID = ?", "i", array($visit['visited_id']))[0];
        $final[] = array(
            'visited_id' => $visit['visited_id'],
            'userdata' => $user
        );
    }


    echo json_encode($final);
}