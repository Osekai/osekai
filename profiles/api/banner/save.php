<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (isRestricted()) exit;

$background = $_POST['background'];
$foreground = $_POST['foreground'];

echo $background;

$sql = "REPLACE INTO `ProfilesBanners` (`UserID`, `Background`, `Foreground`, `CustomGradient`, `CustomSolid`, `CustomImage`, `CustomStyle`)
VALUES (?,?,?,?,?,?,?)";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$types = "issssss";
$userid = $_SESSION['osu']['id'];

$vars = [$userid, $background, $foreground, null, null, null, null];

// TODO: add sanity checks on background and foreground.
// ! probably should have a global var file somewhere with those in it, shared with the actual banner svg generator

if ($background == "custom") {
    $customStyle = $_POST['custom_style'];
    $vars[6] = $customStyle;
    echo $customStyle;
    if($customStyle == "gradient")
    {
        $col1 = $_POST['custom_col1'];
        $col2 = $_POST['custom_col2'];
        $angle = $_POST['custom_angle'];
        $vars[3] = "[\"$col1\",\"$col2\",$angle]";
        echo "saving as gradient";
    }
    if($customStyle == "solid")
    {
        $col1 = $_POST['custom_col1'];
        $vars[4] = $col1;
    }
    if($customStyle == "image")
    {
        $vars[5] = "";
        // TODO: add custom images
    }
}

Database::execOperation($sql, $types, $vars);