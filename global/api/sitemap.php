<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

header("Content-type: text/xml; charset=utf-8");

$template_start = '<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$template_end = '
</urlset>';

function createUrl($url, $priority)
{
    return '<url>

        <loc>' . $url . '</loc>

        <changefreq>monthly</changefreq>

        <priority>' . $priority . '</priority>

    </url>
    ';
}


$string = $template_start;

$root = "/";

$string .= createUrl($root . "home", 1);

$string .= createUrl($root . "medals", 1);
$string .= createUrl($root . "rankings", 1);
$string .= createUrl($root . "snapshots", 1);
$string .= createUrl($root . "profiles", 1);

$string .= createUrl($root . "donate", 0.6);

$preloadimg = Database::execSimpleSelect("SELECT name FROM Medals");

foreach ($preloadimg as $a) {
    $string .= createUrl($root . "medals/?medal=" . $a['name'], 0.5);
}

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY `release` DESC");

foreach($test as $t){
    $string .= createUrl($root . "snapshots/?version=" . $t['id'], 0.5);
}

$string .= createUrl($root . "rankings/?ranking=Medals&amp;type=Users", 0.6);
$string .= createUrl($root . "rankings/?ranking=Medals&amp;type=Rarity", 0.6);
$string .= createUrl($root . "rankings/?ranking=All+Mode&amp;type=Standard+Deviation", 0.6);
$string .= createUrl($root . "rankings/?ranking=All+Mode&amp;type=Total+pp", 0.6);
$string .= createUrl($root . "rankings/?ranking=All+Mode&amp;type=Replays", 0.6);
$string .= createUrl($root . "rankings/?ranking=Mappers&amp;type=Ranked+Mapsets", 0.6);
$string .= createUrl($root . "rankings/?ranking=Mappers&amp;type=Loved+Mapsets", 0.6);
$string .= createUrl($root . "rankings/?ranking=Badges&amp;type=Badges", 0.6);

$string .= createUrl($root . "legal/contact", 0.1);
$string .= createUrl($root . "legal/privacy", 0.1);

$string .= $template_end;

echo $string;