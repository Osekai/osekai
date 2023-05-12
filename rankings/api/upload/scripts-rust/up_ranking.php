<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$data = json_decode($_POST['data'], true);

$columns = ["id", "name", "stdev_pp", "standard_pp", "taiko_pp", "ctb_pp", "mania_pp",
"medal_count", "rarest_medal", "country_code", "standard_global", "taiko_global", "ctb_global", "mania_global",
"badge_count", "ranked_maps", "loved_maps", "subscribers", "followers", "replays_watched", "avatar_url",
"rarest_medal_achieved", "restricted",
"stdev_acc","standard_acc","taiko_acc","ctb_acc","mania_acc",
"stdev_level","standard_level","taiko_level","ctb_level","mania_level", "kudosu"];

$sql = sqlbuilder("Ranking", $columns);
$types = "isiiiiiiisiiiiiiiiiissidddddiiiiii";

foreach($data as $user)
{
    //error_log(print_r($user));
    $data = [];
    foreach($columns as $column)
    {
        $data[] = $user[$column];
    }
    Database::execOperation($sql, $types, $data);
}

exit;
// THIS DOESN'T WORK
$current_champion = Database::execSimpleSelect("SELECT * FROM RankingMedalChampionHistory ORDER BY Date LIMIT 1")[0];

$new_champion = Database::execSimpleSelect("SELECT * FROM Ranking ORDER BY medal_count DESC LIMIT 1")[0];

if($current_champion['UserId'] != $new_champion['Id']) {
    Database::execOperation("INSERT INTO `RankingMedalChampionHistory` (`UserId`, `Date`)
    VALUES (?, now());", "i", [$new_champion['Id']]);
}