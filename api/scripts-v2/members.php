<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$members = Database::execSimpleSelect("SELECT * FROM Members");

echo json_encode($members);
?>