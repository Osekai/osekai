<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode($_POST['data'], true);

$sql = "INSERT INTO `Medals` (`medalid`, `name`, `link`, `description`, `restriction`, `grouping`, `instructions`, `ordering`) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `medalid`=VALUES(`medalid`), `name`=VALUES(`name`), `link`=VALUES(`link`), `description`=VALUES(`description`), `restriction`=VALUES(`restriction`), `grouping`=VALUES(`grouping`), `ordering`=VALUES(`ordering`), `instructions`=VALUES(`instructions`);";
$types = "issssssi";

foreach($data as $medal)
{
    $data = [$medal['medalid'], $medal['name'], $medal['link'], $medal['description'], $medal['restriction'], $medal['grouping'], $medal['instructions'], $medal['ordering']];
    Database::execOperation($sql, $types, $data);
}