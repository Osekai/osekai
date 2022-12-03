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
<title name="title">Osekai • other / translators</title>
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
            <div class="misc__header-inner">
                <p>other / </p>
                <h1>user groups</h1>
            </div>
        </div>
        <div class="misc__panel-container">
            <div class="misc__explainer">
                <p>Placeholder Text</p>
            </div>
            <div class="osekai__panel" id="grouplist">
                <div class="osekai__panel-header">Groups</div>
                <div class="osekai__panel-inner groups__list" id="groups__list">

                </div>
            </div>
        </div>
        <div class="misc__panel-container" id="group" style="--colour: 255, 50, 50">
            <div class="osekai__panel">
                <div class="osekai__panel-header">Group Name</div>
                <div class="groups__header">
                    <div class="groups__header-left">
                        <h1 id="title">Osekai Developers</h1>
                        <div class="groups__group-list-item-bottom">
                            <div id="badge" class="osekai__group-badge osekai__group-badge-monochrome osekai__group-badge-large">uwu</div>
                            <small id="users">42 Users</small>
                        </div>
                    </div>
                    <div class="groups__header-right" id="description">
                        <p>Members of this group are developers of Osekai who keep the website running!</p>
                    </div>
                </div>
                <div class="osekai__panel-inner groups__userlist" id="group__user-list">
                    
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="js/functions.js"></script>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>