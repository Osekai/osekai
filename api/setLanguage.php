<?php
// report errors

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//echo "setting!";
setCurrentLocale($_GET['language']);