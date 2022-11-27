<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (isset($_POST['userid'])) {
    $userid = intval($_POST['userid']);
    $data = Database::execSelect("SELECT * FROM ProfilesBanners WHERE UserID = ?", "i", [$userid])[0];
    if ($data == null || count($data) == 0) {
        Database::execOperation("INSERT INTO `ProfilesBanners` (`UserID`, `Background`, `Foreground`, `CustomGradient`, `CustomSolid`, `CustomImage`) VALUES (?, 'clubglows', 'medal-oriented', '', '', '');", "i", array($userid));
        $data = Database::execSelect("SELECT * FROM ProfilesBanners WHERE UserID = ?", "i", [$userid])[0];
    }

    if ($data['Background'] == "custom" && $data['CustomStyle'] == null) {
        $data['CustomStyle'] = "gradient";
    }
    
    echo json_encode($data);
}
