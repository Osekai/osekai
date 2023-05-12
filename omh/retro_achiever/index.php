<?php
$app = "custom";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>


<!DOCTYPE html>
<html lang="en">
<meta name="msapplication-TileColor" content="#5752FF">
<meta name="theme-color" content="#5752FF">
<meta name="description" content="A list of every medal required for the "Retro Achiever" medal on the osu! Medal Hunters Discord server!" />
<meta property="og:title" content="Medals required for Retro Achiever" />
<meta property="og:description" content="A list of every medal required for the "Retro Achiever" medal on the osu! Medal Hunters Discord server!" />
<meta name="twitter:title" content="Medals required for Retro Achiever" />
<meta name="twitter:description" content="A list of every medal required for the "Retro Achiever" medal on the osu! Medal Hunters Discord server!" />
<title name="title">Medals required for Retro Achiever</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="<?= ROOT_URL ?>/legal/privacy" />

<head>
    <meta charset="utf-8" />

    <?php
    font();
    css();
        

    $retromedals = [
        "Video Game Pack vol.1",
        "Video Game Pack vol.2",
        "Video Game Pack vol.3",
        "Video Game Pack vol.4",
        "Anime Pack vol.1",
        "Anime Pack vol.2",
        "Anime Pack vol.3",
        "Anime Pack vol.4",
        "Internet! Pack vol.1",
        "Internet! Pack vol.2",
        "Internet! Pack vol.3",
        "Internet! Pack vol.4",
        "Rhythm Game Pack vol.1",
        "Rhythm Game Pack vol.2",
        "Rhythm Game Pack vol.3",
        "Rhythm Game Pack vol.4",
        "500 Combo",
        "750 Combo",
        "1,000 Combo",
        "2,000 Combo",
        "5,000 Plays",
        "15,000 Plays",
        "25,000 Plays",
        "50,000 Plays",
        "Catch 20,000 fruits",
        "Catch 200,000 fruits",
        "Catch 2,000,000 fruits",
        "30,000 Drum Hits",
        "300,000 Drum Hits",
        "3,000,000 Drum Hits",
        "40,000 Keys",
        "400,000 Keys",
        "4,000,000 Keys",
        "Don't let the bunny distract you!",
        "S-Ranker",
        "Most Improved",
        "Non-stop Dancer",
        "Consolation Prize",
        "Challenge Accepted",
        "Stumbler",
        "Jackpot",
        "Quick Draw",
        "Obsessed",
        "Nonstop",
        "Jack of All Trades",
    ];
    $medals = Database::execSimpleSelect("SELECT * FROM Medals ORDER BY grouping DESC, ordering");
    ?>
</head>

<body>
    <div class="main">
        <div class="main-left">
            <div class="main-left-title">
                <p>Medals required for</p>
                <h1>Retro Achiever</h1>
            </div>
            <div class="main-medals-list">
                <?php
                foreach($medals as $medal) {
                    if(in_array($medal['name'], $retromedals)) {
                        ?>
                        <a class="main-medal" href="/medals?medal=<?= $medal['name'] ?>">
                            <img src="<?= $medal['link'] ?>">
                            <p><?= $medal['name'] ?></p>
                    </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="main-right">
            <img src="./img/retro-achiever-panel-centered.png">
        </div>
    </div>
</body>
<script>
document.onmousemove = function(event) {
    var x_raw = event.clientX;
    console.log(x_raw);
    var y_raw = event.clientY;
    var width = window.visualViewport.width;
    var height = window.visualViewport.height;
    var x = ((x_raw / width)) * 2 - 1;
    var y = ((y_raw / height) * 2) - 1;
    document.body.style = `
        --mousex: ${-x*5}deg;
        --mousey: ${-y*5}deg;
    `;
}
</script>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>