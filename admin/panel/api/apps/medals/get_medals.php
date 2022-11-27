<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

$groups = Database::execSelect("SELECT DISTINCT Grouping FROM Medals WHERE name LIKE ? ORDER BY (CASE WHEN grouping = 'Hush-Hush' THEN 1 WHEN grouping = 'Skill' THEN 2 WHEN grouping = 'Dedication' THEN 3 WHEN grouping = 'Beatmap Packs' THEN 4 WHEN grouping = 'Seasonal Spotlights' THEN 5 WHEN grouping = 'Beatmap Spotlights' Then 6 WHEN grouping = 'Mod Introduction' THEN 7 ELSE 8 END)", "s", array("%" . $_POST['strSearch'] . "%"));

$medals = array();
foreach ($groups as $key => $value) {
    foreach ($groups[intval($key)] as $k => $v) {
        $medals[$v] = Database::execSelect("CALL FUNC_GetMedals(?,?)", "ss", array($v, ""));
    }
}

echo json_encode($medals);