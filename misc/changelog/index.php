<?php
// osekai home [dev]
// this dev page is for testing controls, in prod it hsould redirect to /home
// /home has actual home content on it
// read the html to see what i mean i guess

$app = "home";
$app_extra = "other";
$accent_override = [[137, 113, 254], [53, 46, 80]];
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="description" content="Osekai • other / translators" />
<meta property="og:title" content="Osekai • other / translators" />
<meta property="og:description" content="everyone who've dedicated their time to help translate Osekai into their native language!" />
<meta name="twitter:title" content="Osekai • other / translators" />
<meta name="twitter:description" content="everyone who've dedicated their time to help translate Osekai into their native language!" />
<title name="title">Osekai • other / changelog</title>
<meta name="keywords" content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="<?= ROOT_URL ?>" />

<?php
font();
css();
dropdown_system();
mobileManager();
xhr_requests();
notification_system();
?>

<head>
    <meta charset="utf-8">

    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="" />
    <meta property=“og:description“ content="" />
    <meta name="twitter:title" content="" />
    <meta name="twitter:description" content="" />
    <title></title>
</head>

<body>
    <?php navbar(); ?>
    <div class="osekai__panel-container misc__container">
        <div class="misc__header">
            <div class="misc__header-inner misc__header-inner-clickable" onclick="goHome()">
                <p><?= GetStringRaw("misc/global", "title") ?> / </p>
                <h1>changelog</h1>
            </div>
        </div>
        <div class="misc__panel-container">
            <div class="misc__explainer">
                <p>Placeholder</p>
            </div>
            <div class="osekai__panel" id="grouplist">
                <div class="osekai__panel-header">Temporary</div>
                <div class="osekai__panel-inner">

                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="js/functions.js"></script>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>