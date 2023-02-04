<?php


require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
// report errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR);

if (isset($_POST['App'])) {
    // <hubz> cache loading
    //if(file_exists("cache/" . $_POST['App'] . ".json")){
    //    $file = file_get_contents("cache/" . $_POST['App'] . ".json");
    //    $fileDecoded = json_decode($file, true);
    //    $now = strtotime("-30 minutes");
    //    if($now < $fileDecoded['date'])
    //    {
    //        echo json_encode($fileDecoded['data']);
    //        exit;
    //    }
    //}
    //Caching::cleanCache();
    $cache = Caching::getCache("rankings_" . $_POST['App']);
    if ($cache != null) {
        echo $cache;
        exit;
    }
    // </hubz>

    $Rankings;
    if ($_POST['App'] == "Users") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.medal_count AS medalCount, " .
            "Medals.name AS rarestmedal, " .
            "Medals.link, " .
            "Ranking.id AS userid, " .
            "ROUND(Ranking.medal_count * 100 / (SELECT COUNT(Medals.medalid) FROM Medals), 2) AS completion " .
            "FROM Ranking " .
            "INNER JOIN Medals ON Ranking.rarest_medal = Medals.medalid " .
            "INNER JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.medal_count DESC, MedalRarity.frequency, Ranking.rarest_medal_achieved " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Rarity") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Medals.link, " .
            "Medals.name AS medalname, " .
            "Medals.medalid AS medalid, " .
            "Medals.description, " .
            "Round(MedalRarity.frequency, 2) AS possessionRate, " .
            "REPLACE(Medals.restriction, 'fruits', 'catch') AS gameMode " .
            "FROM Medals " .
            "INNER JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
            "ORDER BY MedalRarity.frequency, Medals.ordering " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Standard Deviation") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.stdev_pp AS spp, " .
            "Ranking.total_pp AS tpp, " .
            "Ranking.standard_pp AS osupp, " .
            "Ranking.taiko_pp AS taikopp, " .
            "Ranking.ctb_pp AS catchpp, " .
            "Ranking.mania_pp AS maniapp, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.stdev_pp DESC, Ranking.total_pp DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Total pp") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.total_pp AS tpp, " .
            "Ranking.standard_pp AS osupp, " .
            "Ranking.taiko_pp AS taikopp, " .
            "Ranking.ctb_pp AS catchpp, " .
            "Ranking.mania_pp AS maniapp, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.total_pp DESC, Ranking.stdev_pp DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Replays") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.replays_watched AS replays, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.replays_watched DESC, Ranking.stdev_pp DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Ranked Mapsets") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.ranked_maps AS ranked, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.ranked_maps DESC, Ranking.id DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Loved Mapsets") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.loved_maps AS loved, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "WHERE Ranking.loved_maps > 0 " .
            "ORDER BY Ranking.loved_maps DESC, Ranking.id DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Badges") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.badge_count AS badges, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.badge_count DESC, Ranking.id DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    } elseif ($_POST['App'] == "Subscribers") {
        $Rankings = Database::execSimpleSelect("SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT Ranking.country_code AS countrycode, " .
            "Countries.name_long AS country, " .
            "Ranking.name AS username, " .
            "Ranking.subscribers AS subscribers, " .
            "Ranking.id AS userid " .
            "FROM Ranking " .
            "INNER JOIN Countries ON Ranking.country_code = Countries.name_short " .
            "ORDER BY Ranking.subscribers DESC, Ranking.ranked_maps DESC, Ranking.loved_maps DESC, Ranking.id DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 2500");
    }
    echo json_encode($Rankings);

    // <hubz> cache saving
    //$cache = array();
    //$cache['date'] = time();
    //$cache['data'] = $Rankings;
    //file_put_contents("cache/" . $_POST['App'] . ".json", json_encode($cache));

    // cache for 2 hours
    Caching::saveCache("rankings_" . $_POST['App'], 7200, json_encode($Rankings));
    Caching::cleanCache();
    // </hubz>
}

if (isset($_POST['Member']) && !isRestricted() && loggedin()) {
    //echo "this endpoint is temporarily disabled";
    //exit;
    $inDb = count(Database::execSelect("SELECT * FROM Members WHERE id = ?", "i", [$_POST['Member']]));
    $user = v2_getUser($_POST['Member'], "osu", false, false);
    //print_r($user);
    if ($user != null && $inDb == 0) {
        Logging::PutLog("Added user " . $_POST['Member'] . " to rankings", 1, 1);
        Database::execOperation("INSERT INTO Members (id) VALUES (?)", "i", array($_POST['Member']));
        echo json_encode("Success!");
    } else {
        if ($user == null) {
            echo json_encode("User does not exist");
        } else {
            echo json_encode("User is already added!");
        }
    }
}

if (isset($_POST['State'])) {
    echo json_encode(Database::execSimpleSelect("SELECT * FROM RankingLoopInfo")[0]);
}

if (isset($_POST['StateHistory'])) {
    echo json_encode(Database::execSimpleSelect("SELECT * FROM RankingLoopHistory ORDER BY Time DESC LIMIT 10"));
}


/*
SELECT @r := @r+1 AS rank, t1.*
FROM (
    SELECT Ranking.country_code AS country,
        Ranking.name AS username,
        Ranking.medal_count AS medalCount,
        Medals.name AS medalName,
        Medals.link,
        ROUND(Ranking.medal_count * 100 / (SELECT COUNT(Medals.medalid) FROM Medals), 2) AS completion
    FROM Ranking
    INNER JOIN Medals ON Ranking.rarest_medal = Medals.medalid
    INNER JOIN MedalRarity ON MedalRarity.id = Medals.medalid
    ORDER BY Ranking.medal_count DESC, MedalRarity.frequency
) t1, (SELECT @r:=0) t2
*/
