<?php
include("../config.php");
$url = "http://".VPS_IP.":8120/report?";
foreach ($_GET as $key => $value) {
    $url .= $key . "=" . $value . "&";
}
$url = substr($url, 0, -1);
echo "<p>$url</p>";
file_get_contents($url);