<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['Message'])) {
    $Message = $_POST['Message'];
    $Anonymous = true;
    if(isset($_SESSION['osu']['id']) && !isset($_POST['Anonymous'])) $Anonymous = false;
    $OsuID = 0;
    $Username = "";
    if(!$Anonymous) $OsuID = $_SESSION['osu']['id'];
    if(!$Anonymous) $Username = $_SESSION['osu']['username'];
    $Code = "";
    if(isset($_POST['Code'])) $Code = str_replace("-", "", str_replace(" ", "", $_POST['Code']));

    Database::execOperation("INSERT INTO Donations (osuID, Username, DonoDate, Code, Message) VALUES (?, ?, NOW(), ?, ?)", "isis", array($OsuID, $Username, $Code, $Message));
}

if(!isset($_POST['Code'])) {
    redirect('https://paypal.me/osekai/5');
} else {
    pushnotification("Thank you!", "Your donation has been received and is getting verified!", $OsuID);
    redirect('/donate');
}
?>