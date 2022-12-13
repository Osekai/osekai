<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");

if(!loggedin()) {
    echo json_encode("Logged out");
    exit;
}

$groups = Database::execSimpleSelect("SELECT DISTINCT Grouping FROM Medals ORDER BY (CASE WHEN grouping = 'Hush-Hush' THEN 1 WHEN grouping = 'Skill' THEN 2 WHEN grouping = 'Dedication' THEN 3 WHEN grouping = 'Beatmap Packs' THEN 4 WHEN grouping = 'Seasonal Spotlights' THEN 5 WHEN grouping = 'Beatmap Spotlights' Then 6 WHEN grouping = 'Mod Introduction' THEN 7 ELSE 8 END)");

$user = json_decode(v2_getUser($_SESSION['osu']['id'], null, false), true);
$achieved = array();
foreach($user['user_achievements'] as $key => $value) {
    $achieved[$key] = $user['user_achievements'][intval($key)]['achievement_id'];
}

$medals = array();
$return = array();
$count = 0;
foreach($groups as $key => $value) {
    foreach($groups[intval($key)] as $k => $v) {
        $medals[$v] = Database::execSelect("CALL FUNC_GetMedals(?,'')", "s", [$v]);
        foreach($medals[$v] as $innerKey => $innerVal) {
            if(in_array($medals[$v][$innerKey]['MedalID'], $achieved)) {
                $return[$count] = $medals[$v][$innerKey]['MedalID'];
                $count += 1;
            }
        }
    }
}

echo json_encode($return);
