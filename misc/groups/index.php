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
    <?php
    DoMeta("groups", "all the groups on osekai!", "groups");
    ?>
</head>

<body>
    <?php navbar(); ?>
    <div class="osekai__panel-container misc__container">
        <div class="misc__header">
            <div class="misc__header-inner misc__header-inner-clickable" onclick="goHome()">
                <p><?= GetStringRaw("misc/global", "title") ?> / </p>
                <h1><?= GetStringRaw("misc/groups", "title") ?></h1>
</div>
        </div>
        <div class="misc__panel-container">
            <div class="misc__explainer">
                <p><?= GetStringRaw("misc/groups", "description") ?></p>
            </div>
            <div class="osekai__panel" id="grouplist">
                <div class="osekai__panel-header"><?= GetStringRaw("misc/groups", "groupList.title") ?></div>
                <div class="osekai__panel-inner groups__list" id="groups__list">

                </div>
            </div>
        </div>
        <div class="misc__panel-container" id="group" style="--colour: 255, 50, 50">
            <div class="osekai__panel">
                <div class="osekai__panel-header"><?= GetStringRaw("misc/groups", "groupInfo.title") ?></div>
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