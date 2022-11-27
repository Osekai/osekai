<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<?php
font();
css();
dropdown_system();
mobileManager();
echo "test";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../global/php/localization.php");



echo GetStringRaw("medals", "searchbar.placeholder");
echo "<br>";
echo GetString("medals.searchbar.placeholder");
echo "<br>";
echo LocalizeText("50 ??medals.searchbar.placeholder?? something something ??medals.searchbar.placeholder??");

echo "done";
?>
