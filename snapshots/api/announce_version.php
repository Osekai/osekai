<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

print_r($_GET);
echo "<br><br>";

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions ORDER BY archive_date DESC LIMIT 1");

$thisver = null;

foreach($test as $t){
    $thisver = $t;
}

$webhookurl = SNAPSHOTS_WEBHOOK;

//=======================================================================================================
// Compose message. You can use Markdown
// Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
//========================================================================================================

$timestamp = date("c", strtotime("now"));
print_r(json_decode($t['json'], true));

$json_data = json_encode([
    // Message
    "content" => "New version! - https://osekai.net/snapshots/?version=" . $t['id'],
    
    // Username
    "username" => "(archived by) : " . json_decode($t['json'], true)['archive_info']['archiver'],

    // Avatar URL.
    // Uncoment to replace image set in webhook
    //"avatar_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=512",

    // Text-to-speech
    "tts" => false,

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init( $webhookurl );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
echo $response;
curl_close( $ch );