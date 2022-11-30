<?php
$api = file_get_contents(ROOT_URL . "/snapshots/api/api.php");
$api = json_decode($api, true);
$views = 0;
foreach($api as $app)
{
    
    $views += $app['stats']['views'];
}
echo $views;