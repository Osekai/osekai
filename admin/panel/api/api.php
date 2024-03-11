<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (!checkPermission("apps.admin")) {
    echo "no rights";
    exit;
}

$endpoints = array();
function addendpoint($name, $file, $perms = "apps.admin")
{
    global $endpoints;
    $endpoints[$name] = array("perms" => $perms, "file" => $file);
}

// * Base *
addendpoint("base/comments/delete", "base/comments/delete.php");
addendpoint("base/comments/comment", "base/comments/get.php");
addendpoint("base/users/user", "base/users/user.php");


addendpoint("base/notes/save", "base/notes/save.php", "apps.admin");
addendpoint("base/notes/get", "base/notes/get.php", "apps.admin");

// * Medals *
addendpoint("apps/medals/get/medals", "apps/medals/get_medals.php", "apps.admin.apps.medals");
addendpoint("apps/medals/get/medal", "apps/medals/get_medal.php", "apps.admin.apps.medals");
addendpoint("apps/medals/save/medal", "apps/medals/save_medal.php", "apps.admin.apps.medals");

// ? Medals / Beatmaps ?
addendpoint("apps/medals/beatmap/delete", "apps/medals/beatmap/delete.php", "apps.admin.apps.medals");
addendpoint("apps/medals/beatmap/edit", "apps/medals/beatmap/edit.php", "apps.admin.apps.medals");
addendpoint("apps/medals/beatmap/restore", "apps/medals/beatmap/restore.php", "apps.admin.apps.medals");

addendpoint("beatmaps/beatmap", "beatmaps/get.php");

// ? Home /  Dashboard Images ?
addendpoint("home/images/upload", "home/images/up.php", "apps.admin.home.dashboardImages");

// ? Home /  Restrictions ?
addendpoint("home/restrictions/restrict", "home/restrictions/restrict.php", "apps.admin.home.restrictions");
addendpoint("home/restrictions/unrestrict", "home/restrictions/unrestrict.php", "apps.admin.home.restrictions");
addendpoint("home/restrictions/search", "home/restrictions/search.php", "apps.admin.home.restrictions");

// ? Home /  Alerts ?
addendpoint("home/alerts/get", "home/alerts/get.php", "apps.admin.home.alerts");
addendpoint("home/alerts/delete", "home/alerts/delete.php", "apps.admin.home.alerts");
addendpoint("home/alerts/modify", "home/alerts/modify.php", "apps.admin.home.alerts");
addendpoint("home/alerts/create", "home/alerts/create.php", "apps.admin.home.alerts");

// ? Home / Groups ? 
addendpoint("home/groups/add", "home/groups/add.php", "apps.admin.home.groups");
addendpoint("home/groups/delete", "home/groups/remove.php", "apps.admin.home.groups");
addendpoint("home/groups/groups", "home/groups/groups.php", "apps.admin.home.groups");
addendpoint("home/groups/users", "home/groups/users.php", "apps.admin.home.groups");

// ? Reports ? 
addendpoint("reports/get/open", "reports/get_open.php");
addendpoint("reports/get/closed", "reports/get_closed.php");
addendpoint("reports/modify/status", "reports/set_status.php");
addendpoint("reports/get/report", "reports/get_report.php");


// ? Logs ?
addendpoint("logs/get", "logs/get_logs.php", "apps.admin.logs");

// ? Users ?
addendpoint("users/user", "users/user.php", "apps.admin.user");

// ? Analytics ?
addendpoint("analytics/views", "analytics/get_page_views.php", "apps.admin.analytics");
addendpoint("analytics/average_loadtime", "analytics/get_page_average_loadtime.php", "apps.admin.analytics");

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

// PERM CHECK
if(!checkPermission($pageinfo['perms'])) {
    printerror();
    return;
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include($endpoints[$requestedPage]["file"]);
