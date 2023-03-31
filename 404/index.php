<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="description" content="Uh oh!" />
<meta property="og:title" content="Uh oh!" />
<meta property="og:description" content="We couldn't find this page." />
<meta name="twitter:title" content="Uh oh!" />
<meta name="twitter:description" content="We couldn't find this page." />
<title name="title">Uh oh!</title>
<meta name="keywords" content="Oseaki,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="<?= ROOT_URL ?>" />
<link rel="stylesheet" href="/404/404.css" type="text/css">
<?php

font();
css();


notification_system();
fontawesome();

$texts = [
    "uh oh, you seem to be lost!",
    "oops, wrong place!",
    "where've you gotten yourself to?",
    "oops! that doesn't exist!",
    "oh no, that page doesn't exist!",
    "oopsies, this page doesn't exist >w<",
    "oops, that page doesn't exist!",
    "you seem to be lost!",
    "where've you ended up?"
];
$subtexts = [
    "let's get you back on track.",
    "maybe somewhere below is where you want to be?",
    "pick a place from our nice selection below!",
    "let's get you back home... or another app, your choice:",
    "let's get you back on track!",
    "i'm trying my best to figure out where you are, but i just have no clue..."
];
?>

<head>
    <meta charset="utf-8">

    <meta name="description" content="We couldn't find this page." />
    <meta name="keywords" content="We couldn't find this page." />
    <meta property="og:title" content="Uh oh!" />
    <meta property="og:url" content="" />
    <meta property=“og:description“ content="We couldn't find this page." />
    <meta name="twitter:title" content="Uh oh!" />
    <meta name="twitter:description" content="We couldn't find this page." />
    <title></title>
</head>

<body>
    <?php navbar(); ?>
    <div class="nf_background"></div>
    <div class="nf__outer">
    <div class="nf__container">
        <div class="nf__ring">
            <p>?</p>
        </div>
        <div class="nf__texts">
            <h2><?=$texts[array_rand($texts)]?></h2>
            <h3><?=$subtexts[array_rand($subtexts)]?></h3>
            <div class="osekai__button-row">
                <a href="/home" class="osekai__button">Home</a>
                <a href="/rankings" class="osekai__button">Rankings</a>
                <a href="/profiles" class="osekai__button">Profiles</a>
                <a href="/snapshots" class="osekai__button">Snapshots</a>
                <a href="/medals" class="osekai__button">Medals</a>
            </div>
        </div>
    </div>
</div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>

