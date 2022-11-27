<?php
// bridges to osu!api. allows us to not give away my[hubz] osu!api key in the open js

$strKey = OSU_API_V1_KEY;

if(isset($_POST['s'])) {
    print_r(file_get_contents("https://osu.ppy.sh/api/get_beatmaps?k=" . $strKey . "&s=" . $_POST['s']));
}

if(isset($_POST['b']) && isset($_POST['u'])) {
    print_r(file_get_contents("https://osu.ppy.sh/api/get_scores?k=" . $strKey . "&b=" . $_POST['b'] . "&u=" . $_POST['u']));
}
?>