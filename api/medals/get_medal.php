<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

echo json_encode(getmedal(htmlspecialchars($_GET['name'])));
?>