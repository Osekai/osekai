<?php
$app = "tools";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>


<!DOCTYPE html>
<html lang="en">

<?php

$meta = '<meta charset="utf-8">
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="' . ROOT_URL . '/tools" />';

$name = "Osekai Tools";
$description = "temp";
$tags = "";

$meta .= '
<meta name="description" content="' . $name . '" />
<meta property="og:title" content="' . $name . '" />
<meta property="og:description" content="' . $description . '" />
<meta name="twitter:title" content="' . $name . '" />
<meta name="twitter:description" content="' . $description . '" />
<title name="title">' . $name . '</title>
<meta name="keywords" content="Oseaki,pp,calc,tools' . $tags . '">
'
?>

<head>
    <?php
    echo $meta;
    font();
    css();
        fontawesome();

        notification_system();
    tippy();
    
    ?>
</head>



<body>
    <div id="oBeatmapInput"></div>

    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <section class="osekai__panel">
            <div class="osekai__panel-header">
                <p>Osekai Tools (WIP)</p>
            </div>
            <div class="osekai__panel-inner">
            </div>
        </section>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>