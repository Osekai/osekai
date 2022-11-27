<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

$solutions_old = Database::execSelect("SELECT * FROM Solutions WHERE medalid = ?", "i", [$_POST['nMedalId']])[0];
$medals_old = Database::execSelect("SELECT * FROM Medals WHERE medalid = ?", "i", [$_POST['nMedalId']])[0];

Database::execOperation("INSERT INTO Solutions (medalid, solution, submittedby, mods) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE solution = ?, submittedby = ?, mods = ?", "issssss", array($_POST['nMedalId'], htmlspecialchars($_POST['strNewSolution']), $_SESSION['osu']['username'], $_POST['strSolutionMods'], $_POST['strNewSolution'], $_SESSION['osu']['username'], $_POST['strSolutionMods']));
Database::execOperation("UPDATE Medals SET packid = ?, video = ?, date = ?, firstachieveddate = ?, firstachievedby = ? WHERE medalid = ?", "ssssii", array($_POST['strSolutionPackID'], $_POST['strSolutionVideo'], $_POST['strSolutionDate'], $_POST['strFirstAchievedDate'], $_POST['strFirstAchievedId'], $_POST['nMedalId']));
if ($_POST['bBeatmapLockState'] == "true") {
    Database::execOperation("INSERT IGNORE INTO MedalStructure (Locked, MedalID) VALUES ('1', ?)", "i", array($_POST['nMedalId']));
} else {
    Database::execOperation("DELETE FROM MedalStructure Where MedalID = ?", "i", array($_POST['nMedalId']));
}

$solutions_new = Database::execSelect("SELECT * FROM Solutions WHERE medalid = ?", "i", [$_POST['nMedalId']])[0];
$medals_new = Database::execSelect("SELECT * FROM Medals WHERE medalid = ?", "i", [$_POST['nMedalId']])[0];

$logtext = "<h1>Updated medal <strong>" . $medals_new['name'] . "</strong></h1>";
$logtext .= Logging::ReadChanges($solutions_old, $solutions_new);
$logtext .= Logging::ReadChanges($medals_old, $medals_new);
Logging::PutLog($logtext);
Caching::wipeCacheFromPrefix("medals_");