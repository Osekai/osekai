<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] < 1 && $_SESSION['osu']['id'] != 7279762) {
    navbar();
    include($_SERVER['DOCUMENT_ROOT'] . "/404/index.php");
    exit;
}

$endpoints = array();
function addendpoint($name, $file, $needskey = false, $keyperms = 0)
{
    global $endpoints;
    $endpoints[$name] = array("needskey" => $needskey, "keyperms" => $keyperms, "file" => $file);
}

// * Base *
addendpoint("base/comments/delete", "base/comments/delete.php");
addendpoint("base/users/user", "base/users/user.php");

addendpoint("base/notes/save", "base/notes/save.php");
addendpoint("base/notes/get", "base/notes/get.php");

// * Medals *
addendpoint("apps/medals/get/medals", "apps/medals/get_medals.php");
addendpoint("apps/medals/get/medal", "apps/medals/get_medal.php");
addendpoint("apps/medals/save/medal", "apps/medals/save_medal.php");

// ? Medals / Beatmaps ?
addendpoint("apps/medals/beatmap/delete", "apps/medals/beatmap/delete.php");
addendpoint("apps/medals/beatmap/edit", "apps/medals/beatmap/edit.php");
addendpoint("apps/medals/beatmap/restore", "apps/medals/beatmap/restore.php");

// ? Home /  Dashboard Images ?
addendpoint("home/images/upload", "home/images/up.php");

// ? Home /  Restrictions ?
addendpoint("home/restrictions/restrict", "home/restrictions/restrict.php");
addendpoint("home/restrictions/unrestrict", "home/restrictions/unrestrict.php");
addendpoint("home/restrictions/search", "home/restrictions/search.php");

// ? Home /  Alerts ?
addendpoint("home/alerts/get", "home/alerts/get.php");
addendpoint("home/alerts/delete", "home/alerts/delete.php");
addendpoint("home/alerts/modify", "home/alerts/modify.php");
addendpoint("home/alerts/create", "home/alerts/create.php");


// ? Logs ?
addendpoint("logs/get", "logs/get_logs.php");



$requestedPage = $_SERVER['REQUEST_URI'];
$requestedPage = str_replace("/admin/panel/api/", "", $requestedPage);
if (str_contains($requestedPage, "?")) {
    $requestedPage = substr($requestedPage, 0, strpos($requestedPage, "?"));
}
$requestedKey = null;

if (isset($_GET['key'])) {
    $requestedKey = $_GET['key'];
}
if (isset($_POST['key'])) {
    $requestedKey = $_POST['key'];
}


function printerror($title = "Error", $message = "An error has occurred.")
{
    global $requestedPage;
    header("HTTP/1.1 500 Internal Server Error");
    echo "<center>";
    echo "<h1>$title</h1>";
    echo "<hr>";
    echo "<p>$message<p>";
    echo "<small>$requestedPage</small><br>";
    echo "<small>Osekai Admin API</small>";
    echo "</center>";
    exit();
}

if (!isset($endpoints[$requestedPage])) {
    header("HTTP/1.1 404 Not Found");
    printerror("404 Not Found", "The page you requested was not found.");
    print_r($_GET);
    exit();
}

include_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");

$pageinfo = $endpoints[$requestedPage];

// key check
if ($pageinfo["needskey"]) {
    // * note: we'll never need keys, but i'm leaving this here in case we ever want them
    if ($requestedKey == null) {
        header("HTTP/1.1 401 Unauthorized");
        printerror("401 Unauthorized", "You must provide a key to access this page.");
        exit();
    }

    $keys = Database::execSelect("SELECT * FROM ApiKeys WHERE apikey = ?", "s", array($requestedKey));

    if ($keys == null) {
        header("HTTP/1.1 401 Unauthorized");
        printerror("401 Unauthorized", "The key you provided is invalid.");
        exit();
    }

    $key = $keys[0];
    if ($key["keyperms"] < $pageinfo["keyperms"]) {
        header("HTTP/1.1 401 Unauthorized");
        printerror("401 Unauthorized", "You do not have the permissions to access this page.");
        exit();
    }
}
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include($endpoints[$requestedPage]["file"]);
