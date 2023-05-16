<?php
$app = "teams";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<?php
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?');

$request = str_replace("teams/", "", $request);

$request = ltrim($request, "/");

$request = (array) explode("/", $request);

$arguments = $request;
array_shift($arguments);

$templates = [
    "test" => [
        "name" => "test",
        "path" => "test.php"
    ],
    "tabbed_page" => [
        "name" => "tabbed_page",
        "path" => "tabbed_page.php"
    ]
];

include("pages.php");

$page_name = $request[0];
$true_page_name = $page_name;
$ref_page;

if ($page_name == "team") {
    return;
}

$is_team = false;

if (str_starts_with($page_name, "@")) {
    $is_team = true;
    // TODO: call db to check if team exists, if so:
    $page_name = "team";
    $team = [
        "Tag" => "@osekai"
    ];
}

$meta_template = [
    "name" => "Osekai Teams - Unknown Page",
    "description" => "We don't know what this page is."
];

$found = false;


function AddCss($link)
{
    echo '<link rel="stylesheet" href="/teams/css/?' . $link . '?v=' . OSEKAI_VERSION . '">';
}
function AddJs($path)
{
    echo '<script src="/teams/js/' . $path . '?v=' . OSEKAI_VERSION . '"></script>';
}


foreach ($pages as $page) {
    if ($page['name'] == $page_name) {
        $found = true;
        $ref_page = $page;
    }
}
if (isset($ref_page['pages'])) {
    $ref_page_inner = null;
    foreach ($ref_page['pages'] as $page_inner) {
        if ($page_inner['name'] == $arguments[0]) {
            $ref_page_inner = $page_inner;
            break;
        }
    }
    if ($ref_page_inner == null) {
        if ($arguments[0] == null) {
            // show the first page anyway...
            $ref_page_inner = $ref_page['pages'][0];
        } else if ($arguments[0] != null) {
            // nothing to show
            echo "not found";
            exit;
        }
    }
}
?>

<head>
    <?php
    font();
    css();
    notification_system();
    xhr_requests();

    AddCss($ref_page['name'] . "/" . $ref_page['name'] . ".css");
    if (!isset($ref_page['page'])) {
        AddCss($ref_page['name'] . "/" . $ref_page_inner['name'] . "/" . $ref_page_inner['name'] . ".css");
    }
    ?>
    <title>Osekai Teams</title>
</head>

<body>
    <?php navbar(); ?>
    <div class="teams__page">
        <?php
        if ($found == true) {
            ob_start();
            if (isset($ref_page['page'])) {
                include "views/" . $ref_page['page'];
            } else {
                include "views/" . $ref_page_inner['page'];
            }
            $meta = $meta_template;
            $page = ob_get_clean();
            ob_end_flush();
        }
        ob_start();
        if ($found == true) {
            if (isset($ref_page['template']) && $ref_page['template'] != "none") {
                // if 404 is called it wipes all page content
                $template = $templates[$ref_page['template']];
                include("templates/" . $template['path']);
            } else {
                echo $page;
            }

            AddJs($ref_page['name'] . "/" . $ref_page['name'] . ".js");
            if (!isset($ref_page['page'])) {
                AddJs($ref_page['name'] . "/" . $ref_page_inner['name'] . "/" . $ref_page_inner['name'] . ".js");
            }
        } else {
            echo "404";
        }
        ?>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>
</body>
<!-- <script src="/global/js/xhr.js"></script> -->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>