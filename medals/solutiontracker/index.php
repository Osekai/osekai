<?php
$app = "medals";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<?php
echo '<style>
html{
    background-color: #000;
}
</style>';
echo $head;

if (isset($_GET['medal'])) {
    $colMedals = Database::execSelect("SELECT * FROM Medals WHERE `name` = ?", "s", array($_GET['medal']));

    $final_array = array();

    $ver;


    foreach ($colMedals as $medal) {
        $title = $medal['name'] . " osu! Medal solution! • Osekai Medals";
        $desc = $medal['description'];
        $keyword = $medal['name'];
        $keyword2 = $medal['grouping'];

        $imgurl = '/api/embedProxy.php?type=medals/' . htmlspecialchars($medal['name']);


        $meta = '<meta charset="utf-8" />
        <meta name="msapplication-TileColor" content="#ff66aa">
        <meta name="theme-color" content="#ff66aa">
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="achievements,osekai,solutions,tips,osu,medals,solution,osu!,osugame,game,achievement,' . $keyword . ',' . $keyword2 . '">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:image" content="' . $imgurl . '">
        <meta property="og:image" content="' . $imgurl . '">';
    }
} else {
    $meta = '<meta charset="utf-8" />
    <meta name="description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <meta name="msapplication-TileColor" content="#ff66aa">
    <meta name="theme-color" content="#ff66aa">
    <meta property="og:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta property="og:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <meta name="twitter:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta name="twitter:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you\'re looking for!" />
    <title name="title">Osekai Medals • Need a medal solution? We have it!</title>
    <meta name="keywords" content="achievements,osekai,solutions,tips,osu,medals,solution,osu!,osugame,game">
    <meta property="og:url" content="/medals" />';
}

?>

<!DOCTYPE html>
<html lang="en">

<head class="<?= $app; ?>">
    <!--<meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#ff66aa">
    <meta name="theme-color" content="#ff66aa">
    <meta name="description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <meta property="og:title" content="Osekai Medals • Need a medal solution? We have it!" />
    <meta property="og:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <meta name="twitter:title" content="Osekai Medals • The solution to almost all of the osu! medals, right at your fingertips!" />
    <meta name="twitter:description" content="How to get a certain medal? How difficult is that medal? Any ideas that can move medal hunting further? This is the place you're looking for!" />
    <title name="title">Osekai Medals • Need a medal solution? We have it!</title>
    <meta name="keywords" content="achivements,osekai,solutions,tricks,tips,osu,medal,medals,solution,solutions,osu!,osu!game,osugame,game,video game,award,achievement ">
    <meta charset="utf-8">
    <meta property="og:url" content="/medals" />-->

    <?= $meta; ?>

    <?php
    css();
    font();
    init3col();

    xhr_requests();
    notification_system();
    ?>
</head>

<body>
    <?php navbar(); ?>
    <div id="st_home" class="st__page">
        <h1>home</h1>
        <div id="st_home_medal_grid" class="st__home-medal-grid">

        </div>
    </div>
    <div id="st_medal" class="st__page">
        <h1>medal</h1>
    </div>
    <?php tippy(); ?>
    <script type="text/javascript" src="./js/functions.js?v=<?= OSEKAI_VERSION ?>"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>