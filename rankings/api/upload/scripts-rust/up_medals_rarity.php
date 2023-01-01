<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$data = json_decode($_POST['data'], true);

$sql = "INSERT INTO `MedalRarity` (`id`, `frequency`, `count`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `id`=VALUES(`id`), `frequency`=VALUES(`frequency`), `count`=VALUES(`count`)";
$types = "idi";

foreach($data as $medal)
{
    //error_log($medal['frequency']);
    $data = [$medal['medalid'], $medal['frequency'], $medal['count']];
    Database::execOperation($sql, $types, $data);
}