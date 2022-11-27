<?php

$offset = 0;
if(isset($_REQUEST['offset'])) {
    $offset = $_REQUEST['offset'];
}

$data = [];

$data['data'] = Database::execSelect("SELECT * FROM Alerts LIMIT 50 OFFSET ?", "i", [$offset]);

$data['offset'] = $offset += count($data['data']);

echo json_encode($data);