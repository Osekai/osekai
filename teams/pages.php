<?php
$pages = [];

function addPage($name, $displayname, $page="404.php", $template="none") {
    global $pages;
    $pages[$name] = [
        "name" => $name,
        "display_name" => $displayname,
        "page" => $page,
        "template" => $template
    ];
}

function addDepthPage($name, $displayname, $rpages) {
    global $pages;
    $pages[$name] = [
        "name" => $name,
        "display_name" => $displayname,
        "pages" => $rpages,
        "template" => "tabbed_page"
    ];
}

function page($name, $displayname, $page) {
    return [
        "name" => $name,
        "display_name" => $displayname,
        "page" => $page,
        "template" => "none"
    ];
}

addDepthPage("home", "Home", [
    page("dashboard", "Dashboard", "home/dashboard.php"),
    page("rankings", "Rankings", "home/rankings.php"),
    page("settings", "User Settings", "home/settings.php")
]);

addDepthPage("team", "Teampage", [
    page("home", "Home", "team/home.php"),
    page("members", "Members", "team/members.php"),
    page("settings", "User Settings", "team/settings.php")
]);