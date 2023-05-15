<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(!loggedin()) {
    echo json_encode(["error" => "not logged in"]);
    exit;
}

$reporterId = $_SESSION['osu']['id'];
$type = $_REQUEST['reportType'];
$status = 1;
$text = $_REQUEST['reportText'];
$link = $_REQUEST['reportLink'];
$referenceId = $_REQUEST['refId'];

$typeMapping = ["Beatmap", "Comment", "Bug"];

print_r($type);

Database::execOperation("INSERT INTO `Reports` (`ReporterId`, `Type`, `Status`, `Text`, `Link`, `ReferenceId`, `Date`)
VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP());", "iiissi", [$reporterId, $type, $status, $text, $link, $referenceId]);
$id = Database::execSimpleSelect("SELECT * FROM Reports ORDER BY Id DESC LIMIT 1")[0]['Id'];

$json_data = json_encode([
    "username" => $reporterId,
    "avatar_url" => "https://a.ppy.sh/" . $reporterId,
    "content" => "",
    "tts" => false,
    "embeds" => [
      [
        "id" => 471418559,
        "description" => $text,
        "fields" => [],
        "author" => [
          "name" => "Report - " . $typeMapping[$type],
          "url" => "https://osekai.net/admin/panel/reports?id=" . $id,
          "icon_url" => "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/Exclamation_Circle_Red.svg/1024px-Exclamation_Circle_Red.svg.png"
        ],
        "title" => "On page " . $link,
        "url" => $link,
        "color" => 16737962
      ]
    ],
    "components" => [],
    "actions" => []
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init( MODERATION_WEBHOOK );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
echo $response;
curl_close( $ch );