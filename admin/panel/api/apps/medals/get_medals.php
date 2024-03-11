<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// $groups = Database::execSelect("SELECT DISTINCT Grouping FROM Medals WHERE name LIKE ? ORDER BY (CASE WHEN grouping = 'Hush-Hush' THEN 1 WHEN grouping = 'Hush-Hush (Expert)' THEN 2 WHEN grouping = 'Skill & Dedication' THEN 3 WHEN grouping = 'Mod Introduction' THEN 4 WHEN grouping = 'Beatmap Spotlights' Then 5 WHEN grouping = 'Beatmap Challenge Packs' THEN 6 WHEN grouping = 'Beatmap Packs' THEN 7 ELSE 8 END)", "s", array("%" . $_POST['strSearch'] . "%"));

// $medals = array();
// foreach ($groups as $key => $value) {
//     foreach ($groups[intval($key)] as $k => $v) {
//         $medals[$v] = Database::execSelect("CALL FUNC_GetMedals(?,?)", "ss", array($v, ""));
//     }
// }

echo json_encode(Database::execSimpleSelect("SELECT Medals.medalid AS MedalID,
Medals.name AS Name,
Medals.link AS Link, 
Medals.description AS Description,
Medals.restriction AS Restriction,
Medals.grouping AS `Grouping`,
`Medals`.`lazer` AS Lazer,
Medals.instructions AS Instructions,
Solutions.solution AS Solution,
Solutions.mods AS Mods,
MedalStructure.locked AS Locked,
Medals.video AS Video,
Medals.date AS Date,
Medals.packid AS PackID,
Medals.firstachieveddate AS FirstAchievedDate,
Medals.firstachievedby AS FirstAchievedBy,
(CASE WHEN restriction = 'osu' THEN 2
    WHEN restriction = 'taiko' THEN 3
    WHEN restriction = 'fruits' THEN 4
    WHEN restriction = 'mania' THEN 5
    ELSE 1
END) AS ModeOrder,
Medals.ordering AS Ordering,
MedalRarity.frequency AS Rarity
FROM Medals
LEFT JOIN Solutions ON Medals.medalid = Solutions.medalid
LEFT JOIN MedalStructure ON MedalStructure.MedalID = Medals.medalid
LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid
ORDER BY ModeOrder, Ordering DESC, MedalID"));

