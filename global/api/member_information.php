<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['update_experimental'])) {
    Database::execOperation("UPDATE Members SET OPT_Experimental = !OPT_Experimental WHERE id = ?", "i", array($_SESSION['osu']['id']));
    $arrOptions = Database::execSelect("SELECT * FROM Members WHERE id = ?", "i", array($_SESSION['osu']['id']));
    $oSession['options']['experimental'] = $arrOptions[0]['OPT_Experimental'];
    saveSession();
}
?>