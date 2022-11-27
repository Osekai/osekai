<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_GET['id']))
{
    $id = $_GET['id'];
    $data = Database::execSelect("SELECT * FROM ProfilesShowcasePanel WHERE UserID = ?", "i", array($id));
    if(count($data) > 0)
    {
        $panelInfo = $data[0];
        $infoIDs = json_decode($panelInfo['Ids']);
        $panelInfo['Info'] = array();
        for($i = 0; $i < count($infoIDs); $i++)
        {
            if($panelInfo['Type'] == "medal")
            {
                $panelInfo['Info'][] = Database::execSelect("SELECT * FROM Medals WHERE medalid = ?", "i", array($infoIDs[$i]))[0];
            }
        }
        echo json_encode($panelInfo);
    }
    else
    {
        echo "none";
    }
}
else {
    echo "No ID";
    exit;
}