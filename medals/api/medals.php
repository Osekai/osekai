<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (isset($_POST['strSearch'])) {
    // $cache = Caching::getCache("medals_" . $_POST['strSearch']);
    // if ($cache != null) {
    //     echo $cache;
    // } else {
        $groups = Database::execSelect("SELECT DISTINCT Grouping FROM Medals WHERE name LIKE ? ORDER BY (CASE WHEN grouping = 'Hush-Hush' THEN 1 WHEN grouping = 'Skill' THEN 2 WHEN grouping = 'Dedication' THEN 3 WHEN grouping = 'Beatmap Packs' THEN 4 WHEN grouping = 'Seasonal Spotlights' THEN 5 WHEN grouping = 'Beatmap Spotlights' Then 6 WHEN grouping = 'Mod Introduction' THEN 7 ELSE 8 END)", "s", array("%" . $_POST['strSearch'] . "%"));

        $medals = array();
        foreach ($groups as $key => $value) {
            foreach ($groups[intval($key)] as $k => $v) {
                $medals[$v] = Database::execSelect("CALL FUNC_GetMedals(?,?)", "ss", array($v, $_POST['strSearch']));
            }
        }

        echo json_encode($medals);
        // Caching::saveCache("medals_" . $_POST['strSearch'], 720, json_encode($medals));
        // Caching::cleanCache();
    //}
}

if (isset($_POST['strUserID'])) {
    echo json_encode(getuser($_POST['strUserID']));
}

if (isset($_POST['strNewSolution'])) {
    Caching::wipeCacheFromPrefix("medals_");
    if (isset($_SESSION['osu']['id'])) {
        if ($_SESSION['role']['rights'] > 0) {
            Logging::PutLog("Edited medal using Legacy UI");
            Database::execOperation("INSERT INTO Solutions (medalid, solution, submittedby, mods) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE solution = ?, submittedby = ?, mods = ?", "issssss", array($_POST['nSolutionMedal'], htmlspecialchars($_POST['strNewSolution']), $_SESSION['osu']['username'], $_POST['strSolutionMods'], $_POST['strNewSolution'], $_SESSION['osu']['username'], $_POST['strSolutionMods']));
            Database::execOperation("UPDATE Medals SET packid = ?, video = ?, date = ?, firstachieveddate = ?, firstachievedby = ? WHERE medalid = ?", "ssssii", array($_POST['strSolutionPackID'], $_POST['strSolutionVideo'], $_POST['strSolutionDate'], $_POST['strFirstAchievedDate'], $_POST['strFirstAchievedId'], $_POST['nSolutionMedal']));

            echo json_encode("Success!");
        }
    }
}
