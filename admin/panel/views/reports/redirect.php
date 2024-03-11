<?php
if(!$_GET['id'])
{
    redirect("/admin/panel/reports/open");
    die();
}

$reportStatus = Database::execSelect("SELECT STATUS FROM Reports WHERE Id = ?", "i", [$_GET['id']]);
// pending and open 0-1, 
// note, change how this works later on...
 if($reportStatus[0]["STATUS"] <= 1)
 {
    redirect("/admin/panel/reports/open?id=".$_GET['id']); 
    die();
 } else { // closed, resolved, 2-3
    redirect("/admin/panel/reports/closed?id=".$_GET['id']); 
    die();
 }