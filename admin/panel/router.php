<!DOCTYPE html>
<html>

<?php

$time_start = microtime(true);
$request_time = $_SERVER['REQUEST_TIME_FLOAT'];

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

include_once("php/functions.php");

fontawesome();
xhr_requests();


echo "<style>body { background-color: #222; }</style>";

if (!checkPermission("apps.admin")) {
    include("noaccess.php");
    exit;
}

$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?');

$request = str_replace("admin/panel", "", $request);

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

include("routing.php");

$page_name = $request[0];
$ref_page;

$meta_template = [
    "name" => "Osekai Admin Panel - Unknown Page",
    "description" => "We don't know what this page is."
];

$found = false;

function genPerms($ref_page, $ref_page_inner = null)
{
    if ($ref_page_inner != null) {
        return "apps.admin." . $ref_page['name'] . "." . $ref_page_inner['name'];
    } else {
        return "apps.admin." . $ref_page['name'];
    }
}

foreach ($pages as $page) {
    if ($page['name'] == $page_name) {
        $found = true;
        $ref_page = $page;
    }
}
$permreq = "admin";
if (isset($ref_page['pages'])) {
    $ref_page_inner = null;
    foreach ($ref_page['pages'] as $page_inner) {
        if ($page_inner['name'] == $arguments[0]) {
            $ref_page_inner = $page_inner;
            break;
        }
    }
    if ($ref_page_inner == null) {
        // redirect to first
        //exit;
        redirect("/admin/panel/" . $ref_page['name'] . "/" . $ref_page['pages'][0]['name']);
    }
    $permreq = genPerms($ref_page, $ref_page_inner);
} else {
    $permreq = genPerms($ref_page);
}
?>

<head>
    <?php
    // TODO: print meta here
    ?>
    <link rel="stylesheet" href="/admin/panel/css/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <?php
    aCss($ref_page['name'] . "/" . $ref_page['name'] . ".css");
    if (!isset($ref_page['page'])) {
        aCss($ref_page['name'] . "/" . $ref_page_inner['name'] . "/" . $ref_page_inner['name'] . ".css");
    }
    ?>
    <title>Osekai Admin</title>
    <link rel="alternate icon" type="image/svg" href="/admin/panel/public/img/icon-col.svg">
</head>

<body>
    <?php
    include("components/navbar.php");
    ?>
    <div class="page">
        <?php
        if (!checkPermission($permreq)) {
            ?>
            <style>
                .page {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 30vh;
                }

                .permerror {
                    color: #ff2244;
                    font-size: 30px !important;
                }
            </style>
            <?php
            echo '<p class="permerror">Need permission <strong>' . $permreq . "</strong></p>";
            exit;
        }

        if ($found == true) {
            ob_start();
            if (isset($ref_page['page'])) {
                include "views/" . $ref_page['page'];
            } else {
                include "views/" . $ref_page['name'] . "/" . $ref_page_inner['page'];
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
        } else {
            echo "404";
        }
        ?>
    </div>
</body>
<!-- <script src="/global/js/xhr.js"></script> -->

<script src="/global/js/variables.js"></script>
<script src="/admin/panel/js/dropdown_system.js"></script>
<script src="/admin/panel/js/modals.js"></script>
<script src="/admin/panel/js/loader.js"></script>
<?php
tippy_headless();
?>
<script src="/admin/panel/js/userButton.js" defer></script>
<script>
    const oUsername = "<?php echo $_SESSION['osu']['username']; ?>";
    const oUserId = "<?php echo $_SESSION['osu']['id']; ?>";
</script>
<script src="/admin/panel/js/notes.js"></script>
<script src="/admin/panel/js/beatmap_card.js"></script>
<?php
// js/app/app.js
// js/app/page/page.js

Js($ref_page['name'] . "/" . $ref_page['name'] . ".js");
if (!isset($ref_page['page'])) {
    Js($ref_page['name'] . "/" . $ref_page_inner['name'] . "/" . $ref_page_inner['name'] . ".js");
}

$time = microtime(true) - $time_start;
$time = round($time, 4);

?>
<meta charset="utf-8">
<div class="debug">
    <div class="debug__arrow">
        <i class="fas fa-caret-left"></i>
    </div>
    <div class="debug__inner">
        <div class="debug__header">
            <h1>Admin Debug</h1>
        </div>
        <p>
            <?php echo GetStringRaw("general", "page.generatedIn", ["<strong>" . $time . "</strong>"]); ?>
        </p>
    </div>
</div>

<script>
    console.log("generated in: " + <?php echo $time; ?> + " seconds");
</script>


</html>