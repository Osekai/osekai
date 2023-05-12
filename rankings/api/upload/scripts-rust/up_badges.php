<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

Database::execSimpleOperation("DELETE FROM Badges");

$data = json_decode($_POST['data'], true);

$columns = ["id", "name", "image_url", "description", "awarded_at", "users"];

$sql = sqlbuilder("Badges", $columns);
$types = "isssss";

foreach($data as $user)
{
    $data = [];
    foreach($columns as $column)
    {
        $data[] = $user[$column];
    }
    Database::execOperation($sql, $types, $data);
}