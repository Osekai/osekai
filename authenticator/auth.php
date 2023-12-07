<?php
$app = "custom";

$apps['custom']['logo'] = "other/authenticator";

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$apps['custom']['logo'] = "other/authenticator";

$type = $_GET['type'];

if(isset($type) && $type == "minecraft") {
    // TODO: minecraft type is deprecated
    $token = $_GET['token'];
    $mcUuid = Database::execSelect("SELECT * FROM AuthenticatorTokens WHERE type = 'minecraft' AND token = ?", "s", array($token))[0]['minecraft_uuid'];
    if (isset($mcUuid)) {
        $existingUser = Database::execSelect("SELECT * FROM AuthenticatorUsers WHERE type = 'minecraft' AND MinecraftUUID = ?", "i", array($mcUuid))[0];

        if (isset($existingUser['osuID'])) {
            $success = false;
            $error = "this minecraft user is already authenticated!";
        } else {
            $existingUser = Database::execSelect("SELECT * FROM AuthenticatorUsers WHERE type = 'minecraft' AND osuID = ?", "i", array($_SESSION['osu']['id']))[0];
            if (isset($existingUser['osuID'])) {
                $success = false;
                $error = "this osu! user is already authenticated!";

            } else {
                $success = true;
            }
        }
    } else {
        $success = false;
        $error = "invalid token";
    }
} else {
if (loggedin()) {
    $token = $_GET['token'];
    $discordId = Database::execSelect("SELECT * FROM AuthenticatorTokens WHERE type = 'discord' AND token = ?", "s", array($token))[0]['discord_id'];
    if (isset($discordId)) {
        $existingUser = Database::execSelect("SELECT * FROM AuthenticatorUsers WHERE type = 'discord' AND DiscordID = ?", "i", array($discordId))[0];

        if (isset($existingUser['osuID'])) {
            $success = false;
            $error = "this discord user is already authenticated!";
        } else {
            $existingUser = Database::execSelect("SELECT * FROM AuthenticatorUsers WHERE type = 'discord' AND osuID = ?", "i", array($_SESSION['osu']['id']))[0];
            if (isset($existingUser['osuID'])) {
                $success = false;
                $error = "this osu! user is already authenticated!";

            } else {
                // all checks passed (for now? might add more)
                // add to database and shout to web client to start bot

                //Database::execOperation("INSERT INTO `AuthenticatorUsers` (`osuID`, `DiscordID`, `UpdateID`)
                //VALUES (?, ?, ?);", "iii", array($_SESSION['osu']['id'], $discordId, 0));
                $success = true;

                file_get_contents("http://".VPS_IP.":16582/authenticateUser?osuId=" . $_SESSION['osu']['id'] . "&discordId=" . $discordId);
            }
        }
    } else {
        $success = false;
        $error = "invalid token";
    }
}
}
?>

<!DOCTYPE html>
<html lang="en" <?php if ($success == false) {
                    echo 'class="errorbg"';
                } ?>>

<head>
    <meta name="msapplication-TileColor" content="#353d55">
    <meta name="theme-color" content="#353d55">
    <meta name="description" content="authenticate your osu! account with the osu! medal hunters server!" />
    <meta property="og:title" content="Osekai Authenticator" />
    <meta property="og:description" content="authenticate your osu! account with the osu! medal hunters server!" />
    <meta name="twitter:title" content="Osekai Authenticator" />
    <meta name="twitter:description" content="authenticate your osu! account with the osu! medal hunters server!" />
    <title name="title">Osekai Authenticator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= ROOT_URL ?>/legal/contact" />

    <?php
    echo $head;
    font();
    css();
            ?>
</head>

<body>
    <?php navbar();

    if (loggedin()) { ?>

        <div class="osekai__panel-container authenticator__panel loggedin">
            <?php if ($success == true) { ?>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                    <circle class="path circle" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                    <polyline class="path check" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                </svg>
                <h1>your account has been successfully authenticated!</h1>
                <p>you should recieve a message on discord in a few seconds.</p>
            <?php } else { ?>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                    <circle class="path circle" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                    <line class="path line" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
                    <line class="path line" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
                </svg>
                <h1>oh, something went wrong!</h1>
                <p>please contact us in the Osekai Authenticator Support channel and we'll try to sort this out ASAP!</p>
                <p>error: <?= $error; ?></p>
            <?php } ?>
        </div>

    <?php  /* </loggedin> */
    } else { ?>

        <div class="osekai__panel-container authenticator__panel">
            <img src="../global/img/branding/vector/other/authenticator.svg">
            <h1>to authenticate your account, you'll to log in with osu!</h1>
            <p>don't worry though, if you're already logged into the osu! site on this browser, it'll be a breeze!</p>
            <a href="<?= $loginurl; ?>" onclick="openLoader('Logging you in...'); hide_dropdowns();" class="osekai__button">log in with osu!</a>
        </div>

    <?php } ?>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>


<style>
    * {
        --accentdark: 71, 94, 140
    }
</style>