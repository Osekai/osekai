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

xhr_requests();
osu_api();

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
    <script type="text/javascript" src="./js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>