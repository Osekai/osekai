<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function GetCount($id) {
    $req = Database::execSelect("SELECT * FROM MedalsBeatmapPacks WHERE Id = ?", "i", [$id]);
    if(count($req) == 0)
    {
        $page = file_get_contents("https://osu.ppy.sh/beatmaps/packs/" . $id);
        $count = substr_count($page, '<li class="beatmap-pack-items__set">');
        Database::execOperation("INSERT INTO `MedalsBeatmapPacks` (`Id`, `Count`)
        VALUES (?, ?);", "ii", [$id, $count]);
        return $count;
    }
    else {
        return $req[0]['Count'];
    }
}

$req = $_GET['id'];
$requests = explode(",", $req);
$counts = [];
foreach($requests as $request)
{
    $counts[] = GetCount(intval($request));
}

echo json_encode($counts);