<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
if (isset($_GET['key'])) {
    if ($_GET['key'] == EXPERIMENTAL_KEY) {
        if (isset($_SESSION['osu'])) {
            Database::execOperation("UPDATE Members SET OPT_Experimental = 1 WHERE id = ?", "i", array($_SESSION['osu']['id']));
            $oSession['options']['experimental'] = 1;
            saveSession();
            echo "Experimental mode enabled. Enjoy!";
        } else {
            echo "You must be logged in to use this feature.";
            die();
        }
    } else {
        // return 403
        echo "You are not authorized to use this feature.";
        header("HTTP/1.1 403 Forbidden");
        die();
    }
} else {
    // return 403
    echo "You are not authorized to use this feature.";
    header("HTTP/1.1 403 Forbidden");
    die();
}
