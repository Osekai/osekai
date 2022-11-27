<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$author = "Anonymous";

if (isset($_SESSION['osu']['id'])) {
    $author = $_SESSION['osu']['username'];
}

$pfp = getpfp();
$webhookurl = "https://discord.com/api/webhooks/848292789803941940/hGDxt__HkeP6TkslkR7e7FacxaveG56NGLzS-V0pSOrB3JHqg7nVV2_VuAvN6V22oMda";

$type = 0;
$type = $_POST["errortype"];
$color = "FF0000";

if ($type == 0) {
    $text = "Report this beatmap";
    $type_name = "beatmap";
    $color = "00FF00";
}

if ($type == 1) {
    $text = "Report this comment";
    $type_name = "comment";
    $color = "0000FF";
}

if ($type == 2) {
    $text = "Report a bug on this page";
    $type_name = "bug";
    $color = "FF0000";
}

$text = $_POST['report_text'];
$url = $_POST['currenturl'];
$id = $_POST['oid'];

//=======================================================================================================
// Compose message. You can use Markdown
// Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
//========================================================================================================

$timestamp = date("c", strtotime("now"));

$json_data = json_encode([
    // Message
    "content" => "@everyone",

    // Username
    "username" => $author,

    // Avatar URL.
    // Uncoment to replace image set in webhook
    "avatar_url" => $pfp,

    // Text-to-speech
    "tts" => false,

    // File upload
    // "file" => "",

    // Embeds Array
    "embeds" => [
        [
            // Embed Title
            "title" => "ID " . $id . " - " . $type_name . " [" . $type . "] at " . $url,

            // Embed Type
            "type" => "rich",

            // Embed Description
            "description" => $text,

            // URL of title link
            "url" => $url,

            "footer" => [
                "text" => $author,
                "icon_url" => $pfp
            ],

            // Timestamp of embed must be formatted as ISO8601
            //"timestamp" => $timestamp

            // Embed left border color in HEX
            //"color" => hexdec( $color )
        ]
    ]

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


//$ch = curl_init( $webhookurl );
//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
//curl_setopt( $ch, CURLOPT_POST, 1);
//curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt( $ch, CURLOPT_HEADER, 0);
//curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
//
//$response = curl_exec( $ch );
//// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
//echo $response;
//curl_close( $ch );

$ch = curl_init();


echo $json_data . "<br><br>";


curl_setopt_array($ch, [
    CURLOPT_URL => $webhookurl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $json_data,
    CURLOPT_HTTPHEADER => [
        "Length" => strlen($json_data),
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
echo $response;
curl_close($ch);
