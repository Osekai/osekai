<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<html>
<script type="module" type="text/javascript" src="/tools/src/cheese/js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>

<body>
    <input id="uid1"></input>
    <input id="uid2"></input>
    <button id="thebuttonomg">Do the thing</button>

    <p>Both Have:</p>
    <ul id="bothhave">
    </ul>


    <p id="onlyuid1has"></p>
    <ul id="u1has">
    </ul>

    <p id="onlyuid2has"></p>
    <ul id="u2has">
    </ul>
</body>
<?php
xhr_requests();
osu_api();
?>

</html>