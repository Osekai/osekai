<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
// ! codename azelia
// ? rewriting snapshots. with cleaner code, better ui, and better performance.

// hopefully this will go well. we'll see.
// - Hubz, 22/01/2022

$app = "azelia";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">


<?php
font();
css();
dropdown_system();
mobileManager();

xhr_requests();
osu_api();
user_hover_system();
medal_hover_system();
tooltip_system();
report_system();
notification_system();
//comments_system();
fontawesome();

?>

<head>

</head>

<body>
    <?php
    navbar();
    include("home.php");
    include("versionlisting.php");
    include("versioninfo.php");
    ?>
    <script type="text/javascript" src="./js/functions.js?0.1"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>