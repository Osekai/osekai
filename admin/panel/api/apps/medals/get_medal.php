<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);


$medals = Database::execSelect("CALL FUNC_GetMedals(?,?)", "ss", array("", ""));

$medal = [];
foreach($medals as $_medal) {
    if($_medal['MedalID'] == $_GET['id']) {
        $medal = $_medal;
    }
}

$medal['Beatmaps'] = Database::execSelect("SELECT Beatmap.*, Ranking.name as SubmittedByUsername, Restriction.Active as SubmittedByRestrictionStatus
FROM Beatmaps AS Beatmap
LEFT JOIN Ranking AS Ranking ON Ranking.id = Beatmap.SubmittedBy
LEFT JOIN MembersRestrictions AS Restriction ON Restriction.UserId = Beatmap.SubmittedBy
WHERE Beatmap.MedalName = ?", "s", [$medal['Name']]);
$medal['DeletedBeatmaps'] = Database::execSelect("SELECT Beatmap.*, Ranking.name as SubmittedByUsername, Restriction.Active as SubmittedByRestrictionStatus
FROM DeletedMaps AS Beatmap
LEFT JOIN Ranking AS Ranking ON Ranking.id = Beatmap.SubmittedBy
LEFT JOIN MembersRestrictions AS Restriction ON Restriction.UserId = Beatmap.SubmittedBy
WHERE Beatmap.MedalName = ?", "s", [$medal['Name']]);

echo json_encode($medal);
