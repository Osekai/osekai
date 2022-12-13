<?php
// * Tanza 2022-12-13: this is temporary until jiniux finishes the new page systems
function DoMeta($title, $description, $tags) {
    global $actual_link;
    echo '<meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#353d55">
    <meta name="theme-color" content="#353d55">
    <meta name="description" content="Osekai • other / '.$title.'" />
    <meta property="og:title" content="Osekai • other / '.$title.'" />
    <meta property="og:description" content="'.$description.'" />
    <meta name="twitter:title" content="Osekai • other / '.$title.'" />
    <meta name="twitter:description" content="'.$description.'" />
    <title name="title">Osekai • other / '.$title.'</title>
    <meta name="keywords" content="osekai,other,misc,'.$tags.'">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="'.$actual_link.'" />';
}
?>
<link rel="stylesheet" href="/misc/css/main.css">