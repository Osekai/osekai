<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['strSearch'])) {
    $beatmaps = array();
    $beatmaps = Database::execSelect("CALL FUNC_GetBeatmaps(?,?)", "is", array($_SESSION['osu']['id'] ?: 1, $_POST['strSearch']));

    echo json_encode($beatmaps);
}

if(isset($_POST['nObject'])) {
    $hasVoted = array();
    $hasVoted = Database::execSelect("SELECT Vote AS HasVoted FROM Votes Where UserID = ? AND ObjectID = ? AND Type = '0' UNION SELECT 0 AS HasVoted LIMIT 1", "ii", array($_SESSION['osu']['id'] ?: 1, $_POST['nObject']));

    if ($hasVoted[0]['HasVoted'] == 1) {
        Database::execOperation("DELETE FROM Votes WHERE UserID = ? AND ObjectID = ? AND Type = '0'", "ii", array($_SESSION['osu']['id'], $_POST['nObject']));
    } else {
        Database::execOperation("INSERT INTO Votes (UserID, ObjectID, Vote, Type) VALUES (?, ?, 1, '0')", "ii", array($_SESSION['osu']['id'], $_POST['nObject']));
    }
    
    echo json_encode($hasVoted);
}

if(isset($_POST['strDeletion'])) {
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("INSERT INTO DeletedMaps (MedalName, BeatmapID, MapsetID, Gamemode, SongTitle, Artist, Mapper, Source, bpm, Difficulty, DifficultyName, DownloadUnavailable, DeletedMaps.Votes, SubmittedBy, DeletionDate, Note) SELECT MedalName, BeatmapID, MapsetID, Gamemode, SongTitle, Artist, Mapper, Source, bpm, Difficulty, DifficultyName, DownloadUnavailable, SUM(Votes.Vote), SubmittedBy, NOW(), Note FROM Beatmaps LEFT JOIN Votes ON Votes.ObjectID = Beatmaps.ID AND Votes.Type = '0' WHERE BeatmapID = ? AND MedalName = ?", "ss", array($_POST['strDeletion'], $_POST['strMedalName']));
        if($_SESSION['role']['rights'] > 0) {
            Database::execOperation("DELETE FROM Beatmaps WHERE BeatmapID = ? AND MedalName = ?", "ss", array($_POST['strDeletion'], $_POST['strMedalName']));
            echo json_encode("Success!");
        } else {
            Database::execOperation("DELETE FROM Beatmaps WHERE BeatmapID = ? AND MedalName = ? AND SubmittedBy = ?", "ssi", array($_POST['strDeletion'], $_POST['strMedalName'], $_SESSION['osu']['id']));
            echo json_encode("Success!");
        }
    }
}

if(isset($_POST['strBeatmap'])) {
    if (isRestricted()) return;
    $mapLink = strval($_POST['strBeatmap']);
    if (strpos($mapLink, "beatmapsets/") !== false) {
        $mapInformation = explode("beatmapsets/", $mapLink);
        if (strpos($mapLink, "#") !== false) {
            $beatmapInfo = explode("/", $mapInformation[1]);
            $mapsetInfo = explode("#", $beatmapInfo[0]);
            if ($_POST['strMedalMode'] === 'NULL' || $_POST['strMedalMode'] === $mapsetInfo[1]) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/get_beatmaps?k=".OSU_API_V1_KEY."&b=" . $beatmapInfo[1]);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $output = json_decode(curl_exec($curl));
                curl_close($curl);
                if (intval($output[0]->approved) > 0) {
                    Database::execOperation("INSERT INTO Beatmaps (MedalName, BeatmapID, MapsetID, Gamemode, SongTitle, Artist, MapperID, Mapper, Source, bpm, Difficulty, DifficultyName, DownloadUnavailable, SubmittedBy, SubmissionDate, Note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)", "siisssissddsiis", array($_POST['strMedalName'], $beatmapInfo[1], $mapsetInfo[0], $mapsetInfo[1], $output[0]->title, $output[0]->artist, $output[0]->creator_id, $output[0]->creator, $output[0]->source, $output[0]->bpm, $output[0]->difficultyrating, $output[0]->version, $output[0]->download_unavailable, $_SESSION['osu']['id'], $_POST['strNote']));
                    if(isset($_SESSION['osu']['id'])) {
                        echo json_encode("Success!");
                    } else {
                        echo json_encode("Log-in required");
                    }
                } else {
                    echo json_encode("Invalid map provided. Beatmap cannot be unranked.");
                }
            }
        } else {
            echo json_encode("Invalid link provided. Must contain mode and beatmapid.");
        }
    } else {
        echo json_encode("Invalid link provided. Must contain beatmapset.");
    }
}

if(isset($_POST['bCheckLock'])) {
    $bLocked = Database::execSelect("SELECT Locked FROM MedalStructure WHERE MedalID = ?", "i", array($_POST['nMedalID']));
    echo json_encode($bLocked[0]['Locked']);
}

if(isset($_POST['bCurrentlyLocked'])) {
    if(isset($_SESSION['osu']['id'])) {
        if($_SESSION['role']['rights'] > 0) {
            if($_POST['bCurrentlyLocked'] == "false") {
                Database::execOperation("INSERT IGNORE INTO MedalStructure (Locked, MedalID) VALUES ('1', ?)", "i", array($_POST['nMedalID']));
            } else {
                Database::execOperation("DELETE FROM MedalStructure Where MedalID = ?", "i", array($_POST['nMedalID']));
            }
            echo json_encode("true");
        }
    }
}

if(isset($_POST['strNoteChange'])) {
    if (isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        if($_SESSION['role']['rights'] > 0) {
            Logging::PutLog("<h1>Updated note on {$_POST['strMedalName']} (map ID {$_POST['strMapID']})</h1>");
            Database::execOperation("UPDATE Beatmaps SET Note = ? WHERE BeatmapID = ? AND MedalName = ?", "sss", array($_POST['strNoteChange'], $_POST['strMapID'], $_POST['strMedalName']));
            echo json_encode("Success!");
        } else {
            Database::execOperation("UPDATE Beatmaps SET Note = ? WHERE BeatmapID = ? AND MedalName = ? AND SubmittedBy = ?", "sssi", array($_POST['strNoteChange'], $_POST['strMapID'], $_POST['strMedalName'], $_SESSION['osu']['id']));
            echo json_encode("Success!");
        }
    }
}
?>