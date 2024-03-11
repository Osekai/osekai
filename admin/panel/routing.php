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
    page("dashboard", "Dashboard", "dashboard.php"),
    page("notifications", "Notifications", "notifications.php"),
    page("restrictions", "Restrictions", "restrictions.php"),
    page("alerts", "Alerts", "alerts.php"),
    page("images", "Dashboard Images", "dashboard_images.php"),
    page("groups", "Groups", "groups.php"),
]);

addDepthPage("apps", "Apps", [
    page("medals", "Medals", "medals.php"),
]);

addDepthPage("reports", "Reports", [
    page("", "<img src='/admin/panel/public/img/icon.svg' style='height: 44px;'>", "redirect.php"),
    page("open", "Open", "open.php"),
    page("closed", "Closed", "closed.php")
]);

// addPage("tracker", "Tracker"); // probably won't need this
addPage("analytics", "Analytics", "analytics.php");
addPage("logs", "Moderator Logs", "logs.php");
addPage("user", "Users", "users.php");