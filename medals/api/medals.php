<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (isset($_POST['strSearch']) || count($_POST) == 0) {
    $type = "all";
    if(isset($_POST['type'])) {
        $type = "solutiontracker";
    }
    if(isset($_POST['legacy'])) {
    // $cache = Caching::getCache("medals_" . $_POST['strSearch']);
    // if ($cache != null) {
    //     echo $cache;
    // } else {
        $groups = Database::execSelect("SELECT DISTINCT Grouping FROM Medals WHERE name LIKE ? ORDER BY (CASE WHEN grouping = 'Hush-Hush' THEN 1 WHEN grouping = 'Skill' THEN 2 WHEN grouping = 'Dedication' THEN 3 WHEN grouping = 'Beatmap Packs' THEN 4 WHEN grouping = 'Seasonal Spotlights' THEN 5 WHEN grouping = 'Beatmap Spotlights' Then 6 WHEN grouping = 'Mod Introduction' THEN 7 ELSE 8 END)", "s", array("%" . $_POST['strSearch'] . "%"));

        $medals = array();
        foreach ($groups as $key => $value) {
            foreach ($groups[intval($key)] as $k => $v) {
                $medals[$v] = Database::execSelect("CALL FUNC_GetMedals(?,'')", "s", [$v]);
            }
        }

        echo json_encode($medals);
        // Caching::saveCache("medals_" . $_POST['strSearch'], 720, json_encode($medals));
        // Caching::cleanCache();
    //}
    } else {
        echo json_encode(Database::execSimpleSelect("SELECT Medals.medalid AS MedalID
        , Medals.name AS Name
        , Medals.link AS Link
        , Medals.description AS Description
        , Medals.restriction AS Restriction
        , Medals.grouping AS `Grouping`
        , Medals.instructions AS Instructions
        , Medals.solutionfound AS SolutionFound
        , Solutions.solution AS Solution
        , Solutions.mods AS Mods
        , MedalStructure.Locked AS Locked
        , Medals.video AS Video
        , Medals.date AS Date
        , Medals.packid as PackID
        , Medals.firstachieveddate as FirstAchievedDate
        , Medals.firstachievedby as FirstAchievedBy
        , (CASE WHEN restriction = 'osu' THEN 2 WHEN restriction = 'taiko' THEN 3 WHEN restriction = 'fruits' THEN 4 WHEN restriction = 'mania' THEN 5 ELSE 1 END) AS ModeOrder 
        , Medals.ordering AS Ordering
        , MedalRarity.frequency As Rarity
    FROM Medals LEFT JOIN Solutions ON Medals.medalid = Solutions.medalid 
    LEFT JOIN MedalStructure ON MedalStructure.MedalID = Medals.medalid 
    LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid "
    . ($type != "solutiontracker" ? "" : " WHERE Medals.solutiontrackerenabled = 1 ") .
    "ORDER BY ModeOrder, Ordering DESC, MedalID"));
    }
}

if (isset($_POST['strUserID'])) {
    echo json_encode(getuser($_POST['strUserID']));
}

if (isset($_POST['strNewSolution'])) {
    Caching::wipeCacheFromPrefix("medals_");
    if (isset($_SESSION['osu']['id'])) {
        if (checkPermission("apps.medals.medal.legacyEdit")) {
            Logging::PutLog("Edited medal using Legacy UI");
            Database::execOperation("INSERT INTO Solutions (medalid, solution, submittedby, mods) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE solution = ?, submittedby = ?, mods = ?", "issssss", array($_POST['nSolutionMedal'], htmlspecialchars($_POST['strNewSolution']), $_SESSION['osu']['username'], $_POST['strSolutionMods'], $_POST['strNewSolution'], $_SESSION['osu']['username'], $_POST['strSolutionMods']));
            Database::execOperation("UPDATE Medals SET packid = ?, video = ?, date = ?, firstachieveddate = ?, firstachievedby = ? WHERE medalid = ?", "ssssii", array($_POST['strSolutionPackID'], $_POST['strSolutionVideo'], $_POST['strSolutionDate'], $_POST['strFirstAchievedDate'], $_POST['strFirstAchievedId'], $_POST['nSolutionMedal']));

            echo json_encode("Success!");
        }
    }
}
