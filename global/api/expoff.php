<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

Database::execOperation("UPDATE Members SET OPT_Experimental = 0 WHERE id = ?", "i", array($_SESSION['osu']['id']));
$oSession['options']['experimental'] = 0;
saveSession();
echo "Experimental mode has been disabled. <a href=\"/home\">Go Home?</a>";
