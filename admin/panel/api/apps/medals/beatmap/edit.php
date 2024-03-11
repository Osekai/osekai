<?php

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/get_beatmaps?k=" . OSU_API_V1_KEY . "&s=" . $_POST['nDifficultyId']);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = json_decode(curl_exec($curl))[0];
curl_close($curl);

$sql = "UPDATE `Beatmaps` SET
    `BeatmapID` = ?,
    `MapsetID` = ?,
    `Gamemode` = ?,
    `SongTitle` = ?,
    `Artist` = ?,
    `MapperID` = ?,
    `Mapper` = ?,
    `Source` = ?,
    `bpm` = ?,
    `Difficulty` = ?,
    `DifficultyName` = ?,
    `DownloadUnavailable` = ?,
    `Note` = ?
    WHERE `ID` = ?;";

$types = 'iisssissddsis';
$modes = [
    0 => "osu",
    1 => "taiko",
    2 => "fruits",
    3 => "mania",
];
$vars = [$_POST['nDifficultyId'], $output['beatmapset_id'], $modes[$output['mode']], $output['title'], $output['artist'], $output['creator_id'], $output['creator'], $output['source'], $output['bpm'], $output['difficultyrating'], $output['version'], $output['download_unavailable'], $_POST['strNote'], $_POST['nBeatmapId']];

Database::execOperation($sql, $types, $vars);