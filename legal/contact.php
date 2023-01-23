<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#353d55">
    <meta name="theme-color" content="#353d55">
    <meta name="description" content="need to get in contact with us? this is the place for you." />
    <meta property="og:title" content="Osekai Contact" />
    <meta property="og:description" content="need to get in contact with us? this is the place for you." />
    <meta name="twitter:title" content="Osekai Contact" />
    <meta name="twitter:description" content="need to get in contact with us? this is the place for you." />
    <title name="title">Osekai Contact</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= ROOT_URL ?>/legal/contact" />

    <?php
    font();
    css();
    dropdown_system();
    //notification_system();
    //
    //
    //
    //comments_system();
    //report_system();
    ?>
</head>

<body>
    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p><?= GetStringRaw("contact", "title"); ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <p class="osekai__h2"><?= GetStringRaw("contact", "header"); ?></p>
                        <p style="margin: 20px 0px;" class="osekai__p"><?= GetStringRaw("contact", "body.p1"); ?></p>
                        <p class="osekai__p"><?= GetStringRaw("contact", "body.p2"); ?></p>
                    </div>
                </section>
            </div>
            <p style="opacity: 0.5;"><?= GetStringRaw("contact", "siteOwner", ["Frederik Reidinger • Wacholderweg 6, 84032 Landshut, Deutschland • frederik.reidinger@gmx.de • +49 0871 79287"]); ?></p>
            <p style="opacity: 0.5;"><?= GetStringRaw("contact", "snapshots"); ?></p>
            <p style="opacity: 0.5; margin-top: 8px;">©️ Osekai 2019-<?= date("Y") ?></p>
        </div>
    </div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>
