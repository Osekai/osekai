<?php
$app = "tools";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>


<!DOCTYPE html>
<html lang="en">

<?php
$tool = $_GET['page'];
$tool = rtrim($tool, "/");
include("tools.php");

$toolExists = false;
$toolInfo = null;
foreach ($tools as $_tool) {
    if ($tool == $_tool['Key']) {
        $toolExists = true;
        $toolInfo = $_tool;
    } else {
        //echo $tool . " is not " . $_tool['key'];
    }
}
if ($toolExists == false) {
    echo "tool does not exist";
    include($path . "/404/index.php");
    exit;
}


$meta = '<meta charset="utf-8">
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="' . ROOT_URL .'/rankings" />';

$name = "Osekai Tools • Taiko PP Calculator";
$description = "Taiko PP calc temp description";
$tags = "";

$meta .= '
<meta name="description" content="' . $name . '" />
<meta property="og:title" content="' . $name . '" />
<meta property="og:description" content="' . $description . '" />
<meta name="twitter:title" content="' . $name . '" />
<meta name="twitter:description" content="' . $description . '" />
<title name="title">' . $name . '</title>
<meta name="keywords" content="osekai,pp,calc,tools' . $tags . '">
'
?>

<head>
    <?php
    echo $meta;
    font();
    css();
    dropdown_system();
    fontawesome();

    mobileManager();
    notification_system();
    tippy();
    
    ?>
    <link rel="stylesheet" href="/tools/src/<?= $toolInfo['Key'] ?>/css/main.css">
</head>

<body>
    <div id="oBeatmapInput"></div>

    <?php navbar(); ?>
    <div class="tools__tool-container">
        <div class="tools__tool-header">
            <div class="tools__tool-header-side">
                <div class="tools__tool-header-side-left">
                    <img src="/global/img/branding/vector/white/tools.svg">
                    <p>osekai <strong>tools</strong></p>
                </div>
                <div class="tools__tool-header-side-right">
                    <a class="osekai__button">Go Home</a>
                </div>
            </div>
            <div class="tools__tool-header-middle">
                <div class="tools__tool-header-name">
                    <h1>osekai tools / <strong><?= $toolInfo['Name']; ?></strong></h1>
                    <p><?= $toolInfo['Creators']; ?></p>
                </div>
            </div>
            <div class="tools__tool-header-side">
                <a href="https://github.com/Osekai/osekai/tree/main/tools/src/<?= $toolInfo['Key']; ?>" class="osekai__button">Github</a>
            </div>
        </div>
        <div class="tools__tool-container-inner">
            <?php include("src/". $toolInfo['Key'] . "/index.php"); ?>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=1.0.2"></script>
    <script type="text/javascript" src="/tools/src/<?= $toolInfo['Key'] ?>/js/functions.js"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>