<?php
$app = "badges";
$manual_frontend = true;

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">



<head>
    <?php echo $head; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


    <?php
    // print errors

        if (isset($_GET['badge'])) {

            $colBadges = (array)Database::execSelect("SELECT * FROM Badges where id = ?", "i", array($_GET['badge']));

            if (count($colBadges) == 1) {
                $badge = $colBadges[0];

                $title = "Badge: " . $badge['description'];
                $desc = $badge['name'] . " - owned by " . count((array)$badge['users']) . " users - first achieved on " . $badge['awarded_at'];
                $keyword = $badge['name'] . "," . $badge['description'];
                $keyword2 = $badge['name'] . "," . $badge['description'];

                $meta = '<meta charset="utf-8" />
                <meta name="msapplication-TileColor" content="#533b65">
                <meta name="theme-color" content="#533b65">
                <meta property="og:image" content="' . $badge['image_url'] . '">
                <meta name="description" content="' . htmlspecialchars($desc) . '" />
                <meta property="og:title" content="' . htmlspecialchars($title) . '" />
                <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
                <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
                <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
                <title name="title">' . htmlspecialchars($title) . '</title>
                <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,award,' . $keyword . ',' . $keyword2 . ',badge,badges">';
            } else {
                http_response_code(404);
                frontend();
                include($_SERVER['DOCUMENT_ROOT'] . "/404/index.php");
                exit;      
            }
        } else {
            $meta = '<meta charset="utf-8" />
            <meta name="description" content="Want to find almost every badge which osu! has to offer? This is the place to find them!" />
            <meta name="msapplication-TileColor" content="#533b65">
            <meta name="theme-color" content="#533b65">
            <meta property="og:title" content="Osekai Badges • All of osu!\'s Badges!" />
            <meta property="og:description" content="Want to find almost every badge which osu! has to offer? This is the place to find them!" />
            <meta name="twitter:title" content="Osekai Badges • All of osu!\'s Badges!" />
            <meta name="twitter:description" content="Want to find almost every badge which osu! has to offer? This is the place to find them!" />
            <title name="title">Osekai Badges • All of osu!\'s Badges!</title>
            <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,award,badge,badges">
            <meta property="og:url" content="/badges" />';
        }
        frontend();
    echo $meta;

    ?>
    <?php

    font();
    css();
    dropdown_system();
    mobileManager();

    user_hover_system();
    medal_hover_system();
    tooltip_system();
    report_system();
    notification_system();
    fontawesome();


    ?>
</head>

<body>
    <div class="badges__badge-overlay badges__badge-overlay_hidden" id="bop_overlay" onclick="hideOverlay()">

    </div>
    <div id="bop_panel" class="badges__badge-overlay-panel badges__badge-panel_hidden">
        <div class="badges__bop-left">
            <img id="bop_img2" class="badges__bopl-img badges__bopl-img-glow" src="">
            <img id="bop_img" class="badges__bopl-img" src="">
            <div class="badges__bopl-img-1x-cont" id="1x_var">
                <img id="bop_img_1x" class="badges__bopl-img-1x" src="">
                <p><?php echo GetStringRaw("badges", "badge.popup.1xVariant"); ?></p>
            </div>
        </div>
        <div class="badges__bop-right">
            <h3 id="bop_name" class="badges__bopr-name">name</h3>
            <h1 id="bop_desc" class="badges__bopr-desc">description</h1>
            <p id="bop_achieved" class="badges__bopr-first-achieved">first achieved</p>
            <p id="bop_amount" class="badges__bopr-amount">amount</p>
            <div class="osekai__divider"></div>
            <p class="badges__bopr-players-header"><?php echo GetStringRaw("badges", "badge.popup.playersWhoHave"); ?></p>
            <div class="badges__bopr-players-list" id="bop_users">

            </div>
        </div>
    </div>

    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col_col1">
                <!-- <div class="osekai__generic-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?php echo GetStringRaw("badges", "paused", ["2022-01-03"]); ?></p>
                </div> -->
                <br>
                <section class="osekai__panel">
                    <div class="osekai__panel-header-with-buttons">
                        <div class="osekai__panel-hwb-left">
                            <p id="title"><?php echo GetStringRaw("badges", "badges.title", ["loading..."]); ?></p>
                        </div>
                        <div class="osekai__panel-hwb-right">
                            <div class="osekai__panel-header-button osekai__dropdown-opener" id="sort" onclick="openSortDropdown()">
                                <p class="osekai__panel-header-dropdown-text" id="sort_activeItem">Username</p>
                                <i class="fas fa-chevron-down osekai__panel-header-dropdown-icon" aria-hidden="true"></i>
                                <div class="osekai__dropdown osekai__dropdown-hidden" id="sort_items">

                                </div>
                            </div>
                            <div class="badges__panel-header-viewtypes badges__panel-header-viewtypes-scale">
                                <!-- "grid_large", "list_2wide", "list_1wide" -->
                                <div class="badges__panel-header-viewtype badges__panel-header-viewtype-active" onclick="setImageSize('1x')" id="1x">
                                    @1x
                                </div>
                                <div class="badges__panel-header-viewtype" onclick="setImageSize('2x')" id="2x">
                                    @2x
                                </div>
                            </div>
                            <div class="badges__panel-header-viewtypes badges__panel-header-viewtypes-size">
                                <!-- "grid_large", "list_2wide", "list_1wide", "ultra_compact" -->
                                <div tooltip-content="<?php echo GetStringRaw("badges", "viewtype.grid"); ?>" class="tooltip-v2 badges__panel-header-viewtype badges__panel-header-viewtype-active" onclick="changeViewtype('grid_large')" id="viewtype-grid_large">
                                    <i class="fas fa-th-large"></i>
                                </div>
                                <div tooltip-content="<?php echo GetStringRaw("badges", "viewtype.compactList"); ?>" class="tooltip-v2 badges__panel-header-viewtype desktop" onclick="changeViewtype('list_2wide')" id="viewtype-list_2wide">
                                    <i class="fas fa-th-list"></i>
                                </div>
                                <div tooltip-content="<?php echo GetStringRaw("badges", "viewtype.list"); ?>" class="tooltip-v2 badges__panel-header-viewtype" onclick="changeViewtype('list_1wide')" id="viewtype-list_1wide">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div tooltip-content="<?php echo GetStringRaw("badges", "viewtype.ultraCompact"); ?>" class="tooltip-v2 badges__panel-header-viewtype" onclick="changeViewtype('ultra_compact')" id="viewtype-ultra_compact">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="osekai__panel-header-input">
                                <i class="fas fa-search osekai__panel-header-button-icon" aria-hidden="true"></i>
                                <p class="osekai__panel-header-button-text">
                                    <label class="osekai__panel-header-input__sizer">
                                        <input id="search" type="text" size="<?php echo (strlen(GetStringRaw("badges", "search.placeholder")) / 1.2); ?>" placeholder="<?php echo GetStringRaw("badges", "search.placeholder"); ?>" maxlength="40">
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="osekai__panel-inner osekai__badge-list" id="content">

                    </div>
                </section>
            </div>
        </div>
    </div>

</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>

<script>
    function testDialog() {
        openDialog("Test Title", "Test Header", "Test Message", "Button 1", function() {
            document.getElementById("testdialog_text").innerHTML = "you clicked button 1!";
        }, "Button 2", function() {
            document.getElementById("testdialog_text").innerHTML = "you clicked button 2!";
        });
    }
</script>

<script type="text/javascript" src="./js/functions.js?v=1.0.2"></script>