<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>


<!DOCTYPE html>
<html lang="en">
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="description" content="our privacy policy" />
<meta property="og:title" content="Osekai - Privacy Policy" />
<meta property="og:description" content="our privacy policy" />
<meta name="twitter:title" content="Osekai - Privacy Policy" />
<meta name="twitter:description" content="our privacy policy" />
<title name="title">Osekai - Privacy Policy</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="<?= ROOT_URL ?>/legal/privacy" />

<head>
    <meta charset="utf-8" />

    <?php
    font();
    css();
    dropdown_system();
    echo $head;
    //notification_system();
    //
    //
    //
    //comments_system();
    //report_system();
    ?>
</head>

<style>
    .pp html,
    .pp address,
    .pp blockquote,
    .pp body,
    .pp dd,
    .pp div,
    .pp dl,
    .pp dt,
    .pp fieldset,
    .pp form,
    .pp frame,
    .pp frameset,
    .pp h1,
    .pp h2,
    .pp h3,
    .pp h4,
    .pp h5,
    .pp h6,
    .pp noframes,
    .pp ol,
    .pp p,
    .pp ul,
    .pp center,
    .pp dir,
    .pp hr,
    .pp menu,
    .pp pre {
        display: block;
        unicode-bidi: embed
    }

    .pp li {
        display: list-item
    }

    .pp head {
        display: none
    }

    .pp table {
        display: table
    }

    .pp tr {
        display: table-row
    }

    .pp thead {
        display: table-header-group
    }

    .pp tbody {
        display: table-row-group
    }

    .pp tfoot {
        display: table-footer-group
    }

    .pp col {
        display: table-column
    }

    .pp colgroup {
        display: table-column-group
    }

    .pp td,
    .pp th {
        display: table-cell
    }

    .pp caption {
        display: table-caption
    }

    .pp th {
        font-weight: bolder;
        text-align: center
    }

    .pp caption {
        text-align: center
    }

    .pp body {
        margin: 8px
    }

    .pp h1 {
        font-size: 2em;
        margin: .37em 0
    }

    .pp h2 {
        font-size: 1.5em;
        margin: .75em 0
    }

    .pp h3 {
        font-size: 1.17em;
        margin: .83em 0
    }

    .pp h4,
    .pp p,
    .pp blockquote,
    .pp ul,
    .pp fieldset,
    .pp form,
    .pp ol,
    .pp dl,
    .pp dir,
    .pp menu {
        margin: 0.30em 0
    }

    .pp h5 {
        font-size: .83em;
        margin: 1.5em 0
    }

    .pp h6 {
        font-size: .75em !important;
        margin: 1.67em 0
    }

    .pp h1,
    .pp h2,
    .pp h3,
    .pp h4,
    .pp h5,
    .pp h6,
    .pp b,
    .pp strong {
        font-weight: bolder
    }

    .pp blockquote {
        margin-left: 40px;
        margin-right: 40px
    }




    .pp td,
    .pp th,
    .pp tr {
        vertical-align: inherit
    }

    .pp ol ul,
    .pp ul ol,
    .pp ul ul,
    .pp ol ol {
        margin-top: 0;
        margin-bottom: 0
    }

    .pp u,
    .pp ins {
        text-decoration: underline
    }

    .pp br:before {
        content: "\A";
        white-space: pre-line
    }

    .pp center {
        text-align: center
    }

    .pp :link,
    .pp :visited {
        text-decoration: underline
    }

    .pp :focus {
        outline: thin dotted invert
    }

    .pp ul{
        margin-left: 20px;
        padding-bottom: 12px;
    }

    .pp{
        font-family: sans-serif;
    }
</style>

<body>
    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        Privacy Policy
                    </div>
                    <div class="osekai__panel-inner pp">
                        <a href="/legal/privacy-plaintext">For plaintext, see /legal/privacy-plaintext</a>
                        <?php include("privacy-plaintext.php"); // just imports plaintext ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>