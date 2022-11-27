<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
if(isset($_SESSION['osu'])) {
    redirect("/profiles/?user=" . $_SESSION['osu']['id']);
} else {
    redirect("/profiles/");
}
?>