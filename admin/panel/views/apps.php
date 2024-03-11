<?php

//echo "selected app is " .$arguments[0];

switch($arguments[0]) {
    case "medals":
        // TODO: remove this if unused, probably won't be used so
        AddCss("/medals/css/main.css");
        include("apps/medals.php");
        break;
    default:
        // show dashboard
        echo "apps screen dashboard";
}