<?php
$app = "teams";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include("pages.php");
?>
<!DOCTYPE html>
<html lang="en">

<script id="teams">
const teams_pages = <?= json_encode($pages); ?>;
</script>

<head>
    <?php
    font();
    css();
    notification_system();
    xhr_requests();
    ?>
    <title>Osekai Teams</title>
</head>

<body>
    <?php navbar(); ?>
    <?php include ("templates/tabbed_page.php"); ?>
    <script type="text/javascript" src="/teams/js/routing.js?v=<?= OSEKAI_VERSION ?>"></script>
    <script type="text/javascript" src="/teams/js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>
</body>
<!-- <script src="/global/js/xhr.js"></script> -->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>