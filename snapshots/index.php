<?php
$app = "snapshots";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<?php
echo '<style>
html{
    background-color: #000;
}
</style>';

$meta = '<meta name="msapplication-TileColor" content="#ff66aa">
<meta name="theme-color" content="#262c7c">';

if (isset($_GET['version'])) {
    $test = Database::execSelect("SELECT * FROM SnapshotVersions WHERE `id` = ?", "i", array($_GET['version']));

    $final_array = array();

    $ver;

    foreach ($test as $t) { 
        $temp = $t['json'];
        $temp = json_decode($temp, true);
        $temp["stats"]["views"] = $t['views'];
        $temp["stats"]["downloads"] = $t['downloads'];
        $temp["version_info"]["id"] = $t['id'];
        $ver = $temp;

        $title =  "Osekai Snapshots • " . $ver['version_info']['name'] . " (" . $ver['version_info']['version'] . ")";

        $desc = $ver['archive_info']['description'];

        $img = ROOT_URL . "/snapshots/versions/" . $ver['version_info']['version'] . "/" . $ver['screenshots'][0];


        $meta .= '<meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:image" content="' . $img . '">
        <meta property="og:image" content="' . $img . '">

        
        
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="osekai,snapshots,version,' . $ver['version_info']['version'] . ',from,' . $ver['version_info']['name'] . '">
        <meta property="og:url" content="' . ROOT_URL . '/snapshots?version=' . htmlspecialchars($_GET['version']) . '" />';
    }
} else {
    $meta .= '<meta name="description" content="we\'ve got everything, from triangles and benchmarks to coins and holiday themes! pop on down and experience some nostalgia!" />
    <meta property="og:title" content="Osekai Snapshots • Archiving osu! versions from 2007 to now!" />
    <meta property="og:description" content="we\'ve got everything, from triangles and benchmarks to coins and holiday themes! pop on down and experience some nostalgia!" />
    <meta name="twitter:title" content="Osekai Snapshots • Archiving osu! versions from 2007 to now!" />
    <meta name="twitter:description" content="we\'ve got everything, from triangles and benchmarks to coins and holiday themes! pop on down and experience some nostalgia!" />
    <title name="title">Osekai Snapshots • Archiving osu! versions from 2007 to now!</title>
    <meta name="keywords" content="snapshots">
    <meta property="og:url" content="' . ROOT_URL . '/snapshots" />';
}



if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $head; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php
    font();
    css();
    dropdown_system();
    init3col();

    notification_system();
    
    
    
    xhr_requests();
    comments_system();
    report_system();
    mobileManager();

    echo $meta;
    ?>
</head>



<body>
    <div style="--accentdark: 255, 50, 60" id="submitWarning" class="snapshots__submission-verification snapshots__submission-verification-closed">
        <img class="snapshots__submit-warning-icon" src="img/upload_warning.svg">
        <div class="snapshots__submit-warning-texts">
            <h1 class="osekai__h1"><?php echo GetStringRaw("snapshots", "submit.warning.header"); ?></h1>
            <h3 class="osekai__h3"><?php echo GetStringRaw("snapshots", "submit.warning.body"); ?></h3>
        </div>
        <div class="snapshots__file-row">
            <div class="snapshots__submit-warning-file">
                <i class="fas fa-file"></i>
                <span class="snapshots__submit-warning-file-name">osu!.cfg</span>
            </div>
            <div class="snapshots__submit-warning-file">
                <i class="fas fa-file"></i>
                <span class="snapshots__submit-warning-file-name">osu!.username.cfg</span>
            </div>
        </div>
        <a onclick="submitSubmission();" class="osekai__button" style="margin-bottom: 6px;"><?php echo GetStringRaw("snapshots", "submit.warning.confirm"); ?></a>
        <a onclick="cancelWarning();" class="osekai__button osekai__button_solid"><?php echo GetStringRaw("snapshots", "submit.warning.cancel"); ?></a>
        <p class="snapshots__submit-warning-bottomtext"><?php echo GetStringRaw("snapshots", "submit.warning.footer"); ?></p>
    </div>
    <div id="osekai__popup_overlay">
        <div id="submission_overlay" class="osekai__overlay osekai__overlay-hidden">
            <section class="osekai__panel osekai__overlay__panel">
                <div class="osekai__panel-header">
                    <p><?php echo GetStringRaw("snapshots", "submit.title"); ?></p>
                </div>
                <div class="osekai__panel-inner">
                    <div class="osekai__input-area">
                        <h1 class="osekai__h1"><?php echo GetStringRaw("snapshots", "submit.versionName.title"); ?></h1>
                        <p><?php echo GetStringRaw("snapshots", "submit.versionName.description"); ?></p>
                        <input id="submission_versionName" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="<?php echo GetStringRaw("snapshots", "submit.versionName.placeholder"); ?>">
                    </div>
                    <div class="osekai__input-area">
                        <h1 class="osekai__h1"><?php echo GetStringRaw("snapshots", "submit.versionFile.title"); ?></h1>
                        <p><?php echo GetStringRaw("snapshots", "submit.versionFile.description"); ?></p>
                        <input id="submission_versionFile" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="https://drive.google.com/file/d/1WuAOKu98FfUH7KbgKgUAMilsuuXZKeYt/">
                    </div>
                    <div class="osekai__input-area">
                        <h1 class="osekai__h1"><?php echo GetStringRaw("snapshots", "submit.versionInfo.title"); ?></h1>
                        <p><?php echo GetStringRaw("snapshots", "submit.versionInfo.description"); ?></p>
                        <input id="submission_versionInfo" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="<?php echo GetStringRaw("snapshots", "submit.versionInfo.placeholder"); ?>">
                    </div>
                    <div class="osekai__flex_row">
                        <a class="osekai__button" onclick="closeSubmission();"><?php echo GetStringRaw("general", "cancel"); ?></a>
                        <div class="osekai__left osekai__center-flex-row">
                            <p id="error_message" style="margin-right: 10px; text-align: right; color: red;"></p>
                            <a class="osekai__button" onclick="openWarning();"><?php echo GetStringRaw("snapshots", "submit.submit"); ?></a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php
        if ($admin_access == true) { ?>
            <form id="uploadForm" method="post" action="api/upload.php" enctype="multipart/form-data" class="hidden">
                <div class="submission_navbar">
                    <p>You are currently uploading a version.</p>
                    <div class="osekai__left osekai__center-flex-row">
                        <p id="error_message" style="margin-right: 10px; text-align: right; color: red;"></p>
                        <a class="osekai__button" onclick="cancelVersion();">Cancel</a>
                        <input type="submit">
                    </div>
                </div>
                <div id="admin_add_overlay" class="osekai__overlay osekai__overlay-scroll">
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Version Info</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Release Date</h1>
                                <p>This is the release date of the version. The version name usually include this. Example: b20190425.2 = second release of 25/04/2019</p>
                                <input name="releaseDate" type="date" class="osekai__vertical-padding osekai__date osekai__fullwidth">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Name</h1>
                                <p>this is usually "osu!{year}", "osu!lazer {year}", etc. though in some cases, like in triangles, we'll use "osu!2015 triangles" for example. use your own judgement.</p>
                                <input name="Name" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="osu!2019 triangles">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Version</h1>
                                <p>Version name</p>
                                <input name="Version" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Archive Info</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Archiver Name</h1>
                                <p>osu! username. this might be deprecated soon.</p>
                                <input name="archiverName" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Archiver ID</h1>
                                <p>osu! id</p>
                                <input name="archiverID" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="number">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">description</h1>
                                <input name="description" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Video</h1>
                                <p>youtube embed link.</p>
                                <input name="video" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="https://www.youtube.com/embed/dQw4w9WgXcQ">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Extra Info</h1>
                                <p>anything extra</p>
                                <input name="extraInfo" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Group</h1>
                                <p>this is the group. pick now.</p>
                                <select name="group" class="osekai__vertical-padding osekai__input osekai__fullwidth">
                                    <option value="stable">osu!stable</option>
                                    <option value="lazer">osu!lazer</option>
                                </select>
                            </div>
                            <div class="osekai__flex_row osekai__fr_centered">
                                <input name="autoUpdate" class="osekai__checkbox" id="styled-checkbox-1" type="checkbox">
                                <label for="styled-checkbox-1"></label>
                                <p class="osekai__checkbox-label">Automatically Updates</p>
                            </div>
                            <div class="osekai__flex_row osekai__fr_centered">
                                <input name="requiresServer" class="osekai__checkbox" id="styled-checkbox-2" type="checkbox">
                                <label for="styled-checkbox-2"></label>
                                <p class="osekai__checkbox-label">Requires Server/Requires Supporter</p>
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Downloads</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="snapshots__add-group">
                                <div class="snapshots__add-group-nav">
                                    <a class="osekai__button" onclick="upload_adddownload()">Add Download</a>
                                </div>
                                <div class="snapshots__add-group-container" id="upload_download_group">
                                    <div class="snapshots__group">
                                        <h1>Osekai Server</h1>
                                        <input name="downloadFile" type="file" id="downloadFile">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Screenshots</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="snapshots__add-group">
                                <div class="snapshots__add-group-nav">
                                    <a class="osekai__button" onclick="upload_addscreenshot()">Add Image</a>
                                </div>
                                <div class="snapshots__add-group-container" id="upload_screenshot_group">
                                    <div class="snapshots__group">
                                        <input type="file" id="myFile" name="screenshots[]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            </form>


            <form id="screenshotAddForm" method="post" action="api/admin_uploadscreenshot.php" enctype="multipart/form-data" class="hidden">
                <div id="admin_add_overlay" class="osekai__overlay osekai__overlay-scroll">
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>add new screenshot</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            this is kind of scuffed since i cant make it work so enjoy
                            <input id="screenshot_id" name="id" type="number" value="52" hidden>
                            <input type="file" name="screenshot">
                            <input type="submit">
                        </div>
                    </section>
                </div>
            </form>

            <form id="mirrorAddForm" method="post" action="api/admin_addmirror.php" enctype="multipart/form-data" class="hidden">
                <div id="admin_add_overlay" class="osekai__overlay osekai__overlay-scroll">
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Add a new mirror</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            <input id="mirror_id" name="id" type="number" value="52" hidden>
                            <input type="input" name="name" placeholder="name">
                            <input type="input" name="link" placeholder="link">
                            <input type="submit">
                        </div>
                    </section>
                </div>
            </form>

            <form id="editForm" method="post" action="api/edit.php" enctype="multipart/form-data" class="hidden">
                <div class="submission_navbar">
                    <p id="editBarText">You are currently editing version {}.</p>
                    <div class="osekai__left osekai__center-flex-row">
                        <p id="error_message" style="margin-right: 10px; text-align: right; color: red;"></p>
                        <a class="osekai__button" onclick="cancelEdit();">Cancel</a>
                        <input type="submit">
                    </div>
                </div>
                <div id="admin_add_overlay" class="osekai__overlay osekai__overlay-scroll">
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Version Info</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            <input id="edit_id" name="id" type="number" value="52" hidden>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Release Date</h1>
                                <p>This is the release date of the version. The version name usually include this. Example: b20190425.2 = second release of 25/04/2019</p>
                                <input id="edit_releasedate" name="releaseDate" type="date" class="osekai__vertical-padding osekai__date osekai__fullwidth">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Name</h1>
                                <p>this is usually "osu!{year}", "osu!lazer {year}", etc. though in some cases, like in triangles, we'll use "osu!2015 triangles" for example. use your own judgement.</p>
                                <input id="edit_name" name="Name" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="osu!2019 triangles">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Version</h1>
                                <p>Version name</p>
                                <input id="edit_version" name="Version" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Archive Info</p>
                        </div>
                        <div class="osekai__panel-inner snapshots__add-inner">
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Archiver Name</h1>
                                <p>osu! username. this might be deprecated soon.</p>
                                <input id="edit_arch_name" name="archiverName" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Archiver ID</h1>
                                <p>osu! id</p>
                                <input id="edit_arch_id" name="archiverID" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="number">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">description</h1>
                                <input id="edit_description" name="description" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Video</h1>
                                <p>youtube embed link.</p>
                                <input id="edit_video" name="video" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="https://www.youtube.com/embed/dQw4w9WgXcQ">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Extra Info</h1>
                                <p>anything extra</p>
                                <input id="edit_extrainfo" name="extraInfo" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Group</h1>
                                <p>this is the group. pick now.</p>
                                <select id="edit_group" name="group" class="osekai__vertical-padding osekai__input osekai__fullwidth">
                                    <option value="stable">osu!stable</option>
                                    <option value="lazer">osu!lazer</option>
                                </select>
                            </div>
                            <div class="osekai__flex_row osekai__fr_centered">
                                <input id="edit_autoupdate" name="autoUpdate" class="osekai__checkbox" type="checkbox">
                                <label for="edit_autoupdate"></label>
                                <p class="osekai__checkbox-label">Automatically Updates</p>
                            </div>
                            <div class="osekai__flex_row osekai__fr_centered">
                                <input id="edit_requireserver" name="requiresServer" class="osekai__checkbox" type="checkbox">
                                <label for="edit_requireserver"></label>
                                <p class="osekai__checkbox-label">Requires Server/Requires Supporter</p>
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        <?php } ?>
    </div>
    <div id="screenshot_overlay" class="snapshots__screenshotoverlay snapshots__screenshotoverlay-hidden">
        <div onclick="hideScreenshotOverlay()" class="snapshots__screenshot-dim-overlay">
            <div class="snapshots__screenshot-close" onclick="hideScreenshotOverlay()">X</div>
        </div>
        <img id="screenshot_overlay_img" src="" class="snapshots__screenshotoverlay-img">
    </div>
    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <div class="osekai__3col-panels">
            <div class="osekai__3col_col1">
                <div class="snapshots__scroller">
                    <div class="snapshots__search__area">
                        <div class="snapshots__search__bar">
                            <div class="snapshots__search">
                                <i class="fas fa-search"></i>
                                <input oninput="doSearch(this.value)" type="text" id="txtMedalSearch" class="snapshots__search-input" placeholder="<?php echo GetStringRaw("snapshots", "sidebar.search.placeholder"); ?>">
                            </div>
                        </div>
                    </div>
                    <?php if (loggedin()) { ?>
                        <div onclick="openSubmission()" class="snapshots__version-submission osekai__button"><?php echo GetStringRaw("snapshots", "sidebar.submission"); ?></div>
                    <?php } else { ?>
                        <div style="pointer-events: all;" class="tooltip-v2" tooltip-content="Please log in to submit a version">
                            <div class="snapshots__version-submission osekai__button osekai__input-disabled"><?php echo GetStringRaw("snapshots", "sidebar.submission"); ?></div>
                        </div>
                    <?php } ?>

                    <?php if ($admin_access == true) { ?>
                        <div onclick="openAdminPanel()" class="snapshots__version-submission osekai__button">Admin Panel</div>
                    <?php } ?>
                    <section id="oVersionSection">
                        <div class="osekai__replace__loader">
                            <svg viewBox="0 0 50 50" class="spinner">
                                <circle class="ring" cx="25" cy="25" r="22.5"></circle>
                                <circle class="line" cx="25" cy="25" r="22.5"></circle>
                            </svg>
                        </div>
                    </section>
                </div>
            </div>
            <div class="osekai__3col_col1_spacer"></div>
            <div class="osekai__3col_right hidden" id="version">

                <div class="osekai__3col_col2">

                    <section class="osekai__panel" style="overflow: hidden;">
                        <div id="version_header" class="snapshots__version-header" style="">
                            <div class="snapshots__version-header-overlay">
                                <div class="snapshots__version-header-left">
                                    <p id="version_name" class="snapshots__version-hdl-versionname">loading...</p>
                                    <p id="release_date" class="snapshots__version-hdl-releasedate">loading...</p>
                                    <p id="archived_by" class="snapshots__version-hdl-archiver">loading...</p>
                                </div>
                                <div class="snapshots__version-header-right">
                                    <div class="snapshots__version-hdr-views">
                                        <p id="views">loading...</p> <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="snapshots__version-hdr-downloads">
                                        <p id="downloads">loading...</p> <i class="fas fa-download"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="osekai__panel-inner snapshots__version-header-desc" id="descriptionInner">
                            <div>
                                <p class="snapshots__version-header-description-header"><?php echo GetStringRaw("snapshots", "version.description.title"); ?></p>
                                <p id="description" class="snapshots__version-header-description">loading...</p>
                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <?php if ($admin_access == false) { ?>
                            <div class="osekai__panel-header">
                                <?php echo GetStringRaw("snapshots", "version.downloads.title"); ?>
                            </div>
                        <?php } else { ?>
                            <div class="osekai__panel-header-with-buttons" id="AddMapPanel">
                                <div class="osekai__panel-hwb-left">
                                <?php echo GetStringRaw("snapshots", "version.downloads.title"); ?>
                                </div>
                                <div class="osekai__panel-hwb-right" id="AddMapButton">
                                    <div onclick="addMirrorToCurrentVersion();" class="osekai__panel-header-button">
                                        <i class="fas fa-plus-circle osekai__panel-header-button-icon" aria-hidden="true"></i>
                                        <p class="osekai__panel-header-button-text">Add Mirror</p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="osekai__panel-inner">
                            <div class="snapshots__downloads-list" id="downloads_list">

                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel" id="extra_info_panel">
                        <div class="osekai__panel-header">
                        <?php echo GetStringRaw("snapshots", "version.extraInfo.title"); ?>
                        </div>
                        <div class="osekai__panel-inner" id="extra_info">
                            loading...
                        </div>
                    </section>
                    <?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/commentsPanel.php"); ?>
                </div>
                <div class="osekai__3col_col3">
                    <?php if ($admin_access == true) { ?>
                        <section class="osekai__panel">
                            <div class="osekai__panel-header">
                                Version Controls
                            </div>
                            <div class="osekai__panel-inner">
                                <div class="osekai__button-row">
                                    <div onclick="admin_deleteVer()" class="osekai__button">Delete Version</div>
                                    <div onclick="openEditPopup();" class="osekai__button">Edit Version</div>
                                </div>
                            </div>
                        </section>
                    <?php } ?>
                    <section class="osekai__panel" id="video_panel">
                        <div class="osekai__panel-header">
                        <?php echo GetStringRaw("general", "video.title"); ?>
                        </div>
                        <div class="osekai__panel-inner snapshots__youtube-embed">
                            <iframe id="video" width="100%" height="100%" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </section>
                    <section class="osekai__panel" id="warnings_panel">
                        <div class="osekai__panel-header">
                        <?php echo GetStringRaw("snapshots", "version.warnings.title"); ?>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="snapshots__warning-list" id="warnings_list">

                            </div>
                        </div>
                    </section>
                    <section class="osekai__panel">
                        <?php if ($admin_access == false) { ?>

                            <div class="osekai__panel-header">
                            <?php echo GetStringRaw("snapshots", "version.screenshots"); ?>
                            </div>
                        <?php } else { ?>
                            <div class="osekai__panel-header-with-buttons" id="AddMapPanel">
                                <div class="osekai__panel-hwb-left">
                                <?php echo GetStringRaw("snapshots", "version.screenshots"); ?> {Admin Edition}
                                </div>
                                <div class="osekai__panel-hwb-right" id="AddMapButton">
                                    <div onclick="addScreenshotToCurrentVersion();" class="osekai__panel-header-button"><i class="fas fa-plus-circle osekai__panel-header-button-icon" aria-hidden="true"></i>
                                        <p class="osekai__panel-header-button-text">Add Screenshot</p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="osekai__panel-inner">
                            <div class="snapshots__screenshot-grid" id="screenshot_grid">

                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="osekai__3col_right" id="home">
                <div class="app__home-panel">
                    <img class="app__home-logo" src="../global/img/branding/vector/white/snapshots.svg">
                    <div class="app__home-texts">
                        <p class="app__home-header">osekai <strong>snapshots</strong></p>
                        <p class="app__home-slogan" id="home_slogan"><?php echo GetStringRaw("snapshots", "home.slogan.unloaded"); ?></p>
                        <p class="app__home-splash" id="splash">loading splash...</p>
                    </div>
                </div>

                <div class="osekai__3col_col2">
                    <section class="osekai__panel">
                        <div class="osekai__panel-header">
                        <?php echo GetStringRaw("snapshots", "home.welcome.title"); ?>
                        </div>
                        <div class="osekai__panel-inner">
                            <h1 class="osekai__h1"><?php echo GetStringRaw("snapshots", "home.welcome"); ?></h1>
                            <?php echo GetStringRaw("snapshots", "home.welcome.description"); ?>
                        </div>
                    </section>
                </div>
                <?php if (loggedin()) { ?>
                    
                <?php } else { ?>
                    <div class="osekai__3col_col3">
                        <section class="osekai__panel">
                            <div class="osekai__panel-header">
                                You're not logged in!
                            </div>
                            <div class="osekai__panel-inner osekai__flex-vertical-container">
                                <p class="osekai__h1">When you log into Osekai, you can have a better experience across our apps!</p>
                                <p>You can post comments, report, upvote, get notifications, submit versions, and also get yourself onto Osekai Rankings! And don’t worry, it’s all done securely through osu!’s own servers. All we get is your osu! profile info!</p>
                                <a class="osekai__button" href="https://osu.ppy.sh/oauth/authorize?response_type=code&amp;client_id=5878&amp;redirect_uri=https%3A%2F%2Fosekai.net%2Fglobal%2Fphp%2Flogin.php" onclick="openLoader('Logging you in...'); hide_dropdowns();">Log In with osu!</a>
                            </div>
                        </section>
                    </div>
                <?php } ?>

            </div>
            <?php
            if ($admin_access == true) { ?>
                <div id="submission_status_overlay" class="osekai__overlay osekai__overlay-hidden">
                    <section class="osekai__panel osekai__overlay__panel">
                        <div class="osekai__panel-header">
                            <p>Change Version Status</p>
                        </div>
                        <div class="osekai__panel-inner">
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Status</h1>
                                <p>1 = deny, 2 = accept</p>
                                <input id="submission_status_status" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="number" placeholder="2">
                            </div>
                            <div class="osekai__input-area">
                                <h1 class="osekai__h1">Notification Text</h1>
                                <p>Leave blank to not send</p>
                                <input id="submission_status_notification" class="osekai__vertical-padding osekai__input osekai__fullwidth" type="text" placeholder="This should be filled in by default.">
                            </div>
                            <div class="osekai__flex_row">
                                <a class="osekai__button" onclick="closeSubmissionStatus();">Cancel</a>
                                <div class="osekai__left osekai__center-flex-row">
                                    <p id="error_message" style="margin-right: 10px; text-align: right; color: red;"></p>
                                    <a class="osekai__button" onclick="changeSubmissionStatus(submissions[submissionIndexChangingId]['id'],submissions[submissionIndexChangingId]['userid']);;">Submit Version</a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="osekai__3col_right hidden" id="admin">
                    <div class="osekai__3col_col2">
                        <section class="osekai__panel">
                            <div class="osekai__panel-header-with-buttons" id="AddMapPanel">
                                <div class="osekai__panel-hwb-left">
                                    Submissions
                                </div>
                                <div class="osekai__panel-hwb-right" id="AddMapButton">
                                    <div onclick="refreshSubmissions();" class="osekai__panel-header-button"><i class="fas fa-sync-alt osekai__panel-header-button-icon" aria-hidden="true"></i>
                                        <p class="osekai__panel-header-button-text">Refresh</p>
                                    </div>
                                </div>
                            </div>
                            <div class="osekai__panel-inner">
                                <div id="submission_list" class="snapshots__submissions-list">

                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="osekai__3col_col3">
                        <section class="osekai__panel">
                            <div class="osekai__panel-header">
                                Controls
                            </div>
                            <div class="osekai__panel-inner">
                                <a onclick="openUploadPopup();" class="osekai__button">Add New Version</a>
                            </div>
                        </section>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <script type="text/javascript" src="./js/functions.js?v=1.0.3"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>