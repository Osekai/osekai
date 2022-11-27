<?php
// print errors
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

$app = "admin";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<style>
    .stats-h1 {
        color: #fff !important;
    }
    .stats-p {
        font-weight: 100;
        color: #fffa;
    }

    .stats-p strong {
        font-weight: 900;
        color: #fff;
    }

    .statspanel {
        margin: 10px;
        padding: 18px;
        padding-right: 40px;
        min-width: 30vw;
        border-radius: 10px;
        background-color: rgba(var(--accentdark), 0.8);
        max-width: 45vw;
    }
    .medals__bmp3-panel {
        margin-bottom: 6px;
    }

    .app-views {
        background-color: rgb(var(--col));
        display: flex;
        padding: 10px;
        border-radius: 0px;
        border-bottom: 1px solid white;
        display: flex;
        align-items: center;
    }

    .app-views img {
        height: 40px;
        margin-right: 15px;
    }

    .app-views h1 {
        font-size: 16px;
    }

    .app-views p {
        font-size: 12px;
        font-weight: 100;
    }

    .not-visible {
        opacity: 0.7;
    }

    .experimental {
        border-left: 8px solid red;
    }

    .stat {
        margin-bottom: 8px;
    }

    .stats-h3 {
        margin-top: 8px;
    }

    .comments__comment-div {
        width: 30% !important;
    }

    .comments__comment-top {
        padding: 12px !important;
    }

    .comments__comment-vote {
        font-size: 14px;
    }

    .stats-h2 {
        margin-bottom: 4px;
    }
</style>

<head>
    <?php
    font();
    css();
    dropdown_system();
    init3col();

    notification_system();
    user_hover_system();
    medal_hover_system();
    tooltip_system();
    xhr_requests();
    report_system();
    mobileManager();
    chart_js();


    ?>
</head>

<link rel="stylesheet" href="/global/css/comments.css">

<link rel="stylesheet" href="/medals/css/main.css">

<body>
    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col_col1">
                <div class="osekai__panel">
                    <div class="osekai__panel-header">
                        Statistics
                    </div>
                    <div class="osekai__panel-inner">
                        <?php
                        // print errors
                        ini_set('memory_limit', '8000M');
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);

                        $stats = Database::execSimpleSelect("SELECT date, app, devicetype FROM StatsPageViews t WHERE date > DATE_SUB(NOW(), INTERVAL 3 MONTH) ORDER BY date DESC;");
                        // round date to nearest day
                        foreach ($stats as $key => $stat) {
                            $stats[$key]["date"] = date("Y-m-d", strtotime($stat["date"]));
                        }
                        // combine data by date
                        $combined = array();
                        foreach ($stats as $stat) {
                            if (array_key_exists($stat["date"], $combined)) {
                                $combined[$stat["date"]]["count"] += 1;
                            } else {
                                $combined[$stat["date"]] = array("date" => $stat["date"], "count" => 1);
                            }
                        }

                        // reverse
                        $combined = array_reverse($combined);

                        //unset($stats);

                        $stats_loadtime = Database::execSimpleSelect("SELECT date, app, generation_time FROM StatsPageViews t WHERE date > DATE_SUB(NOW(), INTERVAL 3 MONTH) ORDER BY date DESC;");
                        // remove any 0 values
                        foreach ($stats_loadtime as $key => $stat) {
                            if ($stat["generation_time"] == 0) {
                                unset($stats_loadtime[$key]);
                            }
                        }
                        // remove any with app "admin"
                        foreach ($stats_loadtime as $key => $stat) {
                            if ($stat["app"] == "Array" || $stat["app"] == "admin") {
                                unset($stats_loadtime[$key]);
                            }
                        }
                        $stats_loadtime_split = array();
                        foreach ($stats_loadtime as $stat) {
                            $date_notime = date("Y-m-d", strtotime($stat["date"]));
                            $stats_loadtime_split[$date_notime][] = $stat["generation_time"];
                        }
                        unset($stats_loadtime);
                        // average
                        $stats_loadtime_average = array();
                        foreach ($stats_loadtime_split as $date => $times) {
                            $stats_loadtime_average[$date] = array_sum($times) / count($times);
                        }
                        $stats_loadtime_cleaned = array();
                        unset($stats_loadtime_split);
                        // for chart.js
                        foreach ($stats_loadtime_average as $date => $time) {
                            $stats_loadtime_cleaned[] = array("x" => $date, "y" => $time);
                        }

                        //$stats_ipgrabtime = Database::execSimpleSelect("SELECT date, app, ip_grab_time FROM StatsPageViews ORDER BY date DESC;");
                        //// remove any 0 values
                        //foreach ($stats_ipgrabtime as $key => $stat) {
                        //    if ($stat["ip_grab_time"] == 0) {
                        //        unset($stats_ipgrabtime[$key]);
                        //    }
                        //}
                        //// remove any with app "admin"
                        //foreach ($stats_ipgrabtime as $key => $stat) {
                        //    if ($stat["app"] == "Array" || $stat["app"] == "admin") {
                        //        unset($stats_ipgrabtime[$key]);
                        //    }
                        //}
                        //$stats_ipgrabtime_split = array();
                        //foreach ($stats_ipgrabtime as $stat) {
                        //    $date_notime = date("Y-m-d", strtotime($stat["date"]));
                        //    $stats_ipgrabtime_split[$date_notime][] = $stat["ip_grab_time"];
                        //}
                        //unset($stats_ipgrabtime);
                        //// average
                        //$stats_ipgrabtime_average = array();
                        //foreach ($stats_ipgrabtime_split as $date => $times) {
                        //    $stats_ipgrabtime_average[$date] = array_sum($times) / count($times);
                        //}
                        //$stats_ipgrabtime_cleaned = array();
                        //// for chart.js
                        //foreach ($stats_ipgrabtime_average as $date => $time) {
                        //    $stats_ipgrabtime_cleaned[] = array("x" => $date, "y" => $time);
                        //}


                        //print_r($stats_loadtime_average);
                        ?>


                        <h1 class="stats-h1">>Last 3 Months / Graph</h1>
                        <canvas id="statsChart"></canvas>
                        <?php
                        // print most visited Apps
                        function countstat($title, $sql)
                        {
                            return "<p class=\"stat stats-p\">" . $title . ": <strong>" . count(Database::execSimpleSelect($sql)) . "</strong></p>";
                        }
                        function base($stats)
                        {
                            $apps = Database::execSimpleSelect("SELECT * FROM Apps");

                            for ($x = 0; $x < count($apps); $x++) {
                                $apps[$x]['views'] = 0;
                                foreach ($stats as $stat) {
                                    if ($stat["app"] == $apps[$x]["simplename"]) {
                                        $apps[$x]["views"] += 1;
                                    }
                                }
                                //echo "<p><light>" . $app["name"] . "</light>: <strong>" . $app["views"] . "</strong></p>";

                            }
                            $apps = (array)$apps;
                            // TODO: sort
                            usort($apps, function ($first, $second) {
                                return intval($first['views']) > intval($second['views']) ? 1 : -1;
                            });
                            foreach ($apps as $app) {
                                if ($app['views'] == 0) continue;
                                $classes = "";
                                if ($app['visible'] == 0) $classes .= " not-visible";
                                if ($app['experimental'] == 1) $classes .= " experimental";
                                if (($app['experimental'] == 1 || $app['visible'] == 0) && isset($_GET['public'])) continue;
                                echo '<div style="--col: ' . $app['color_dark'] . ';" class="app-views ' . $classes . '"><img src="/global/img/branding/vector/' . $app['logo'] . '.svg"><div class="stats-p app-views-text"><p>' . $app['name'] . '</p><h1 class="stats-h1">' . $app['views'] . ' views</h1></div></div>';
                            }


                            // select * where devicetype isn't null and isn't ""
                            $pageviewsWithDeviceType = Database::execSimpleSelect("SELECT date, devicetype FROM StatsPageViews t WHERE devicetype != \"\" AND devicetype != \"null\" AND date > DATE_SUB(NOW(), INTERVAL 3 MONTH)");
                            $count = count($pageviewsWithDeviceType);
                            $count_mobile = 0;
                            $count_desktop = 0;
                            foreach ($pageviewsWithDeviceType as $pageview) {
                                if ($pageview["devicetype"] == "desktop") {
                                    $count_desktop += 1;
                                } else if ($pageview["devicetype"] == "mobile") {
                                    $count_mobile += 1;
                                }
                            }
                            $percentage_mobile = $count_mobile / $count * 100;
                            $percentage_desktop = $count_desktop / $count * 100;
                            echo "<br><br><p class=\"stats-p\">Percentage of pageviews with mobile device: <strong>" . round($percentage_mobile, 2) . "</strong>%</p>";
                            echo "<br><p class=\"stats-p\">Percentage of pageviews with desktop device: <strong>" . round($percentage_desktop, 2) . "</strong>%</p>";
                            echo "<br><p class=\"stats-p\">Overall Views: <strong>" . count($stats) . "</strong></p>";
                        }
                        ?>
                        <div class="stats" style="display: flex;">
                            <div class="statspanel">
                                <h1 class="stats-h1">Past 3 months</h1>
                                <?php
                                base($stats);

                                echo "<br>";


                                // print average pageviews in 30 day period
                                $average = 0;
                                $cut = array_slice($combined, -30);
                                foreach ($cut as $stat) {
                                    $average += $stat["count"];
                                }
                                $average = $average / 30;
                                echo "<br>";
                                echo "<p class=\"stats-p\">Average pageviews in 30 days: <strong>" . $average . "</strong></p>";
                                echo "<br>";
                                // print average loadtime in 30 day period
                                $average = 0;
                                $cut = array_slice($stats_loadtime_average, -30);
                                foreach ($cut as $stat) {
                                    $average += $stat;
                                }
                                $average = $average / count($cut);
                                echo "<p class=\"stats-p\">Average loadtime in 30 days: <strong>" . $average . "</strong></p>";
                                ?>
                            </div>
                            <div class="statspanel">
                                <h1>Past 7 days</h1>

                                <?php
                                $stats1week = Database::execSimpleSelect("SELECT date, app, devicetype FROM StatsPageViews t WHERE date > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY date DESC;");
                                base($stats1week); ?>
                            </div>
                        </div>
                        <div class="stats" style="display: flex;">
                            <div class="statspanel-multiple">
                                <div class="statspanel">
                                    <h1 class="stats-h1">App Stats</h1>
                                    <?php
                                    echo "<h3>Osekai Medals</h3>";
                                    echo countstat("Comments on Osekai Medals", "SELECT * FROM `Comments` WHERE `MedalID` IS NOT NULL");
                                    echo countstat("Beatmaps on Osekai Medals", "SELECT * FROM `Beatmaps`");
                                    echo countstat("Deleted Beatmaps on Osekai Medals", "SELECT * FROM `DeletedMaps`");
                                    echo "<h3>Osekai Profiles</h3>";
                                    echo countstat("Comments on Osekai Profiles", "SELECT * FROM `Comments` WHERE `ProfileID` IS NOT NULL");
                                    echo countstat("Custom Profile Banners on Osekai Profiles", "SELECT * FROM `ProfilesBanners` WHERE `Background` = 'custom'");
                                    echo "<h3>Osekai Snapshots</h3>";
                                    echo countstat("Comments on Osekai Snapshots", "SELECT * FROM `Comments` WHERE `VersionID` IS NOT NULL");
                                    echo countstat("Versions on Osekai Snapshots", "SELECT * FROM `SnapshotVersions`");
                                    ?>
                                </div>
                                <div class="statspanel">
                                    <h1 class="stats-h1">Beatmap Stats</h1>
                                    <h2 class="stats-h2">Top 10 beatmaps</h2>
                                    <?php
                                    $data = Database::execSimpleSelect('SELECT SUM(Vote) As Votes, Beatmaps.SubmissionDate, Beatmaps.BeatmapID, Beatmaps.MapsetID, Beatmaps.SubmittedBy, Beatmaps.DifficultyName, Beatmaps.Mapper, Beatmaps.Artist, Beatmaps.SongTitle, Medals.name, Ranking.name AS Username
                                    FROM Votes
                                    INNER JOIN Beatmaps ON Beatmaps.ID = Votes.ObjectID
                                    LEFT JOIN Medals ON Medals.name = Beatmaps.MedalName
                                    LEFT JOIN Ranking ON Ranking.id = Beatmaps.SubmittedBy
                                    GROUP BY ObjectID, Type, Medals.name, Beatmaps.SongTitle, Beatmaps.Artist
                                    HAVING Type = 0
                                    ORDER BY SUM(Vote) DESC
                                    LIMIT 10');
                                    foreach ($data as $beatmap) {
                                        //echo json_encode($beatmap);
                                        //echo "<br><br>";
                                        echo '<h3>on ' . $beatmap['name'] . "</h3>";
                                        echo '<div id="desktop" class="medals__bmp3-panel-outer">
                                        <div class="medals__bmp3-panel" style="background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%), url(https://assets.ppy.sh/beatmaps/' . $beatmap['MapsetID'] . '/covers/cover.jpg), linear-gradient(#240d19,#240d19);">
                                            <a class="medals__bmp3-top" href="https://osu.ppy.sh/beatmapsets/' . $beatmap['MapsetID'] . '#osu/' . $beatmap['MapsetID'] . '">
                                                <div class="medals__bmp3-top-left">
                                                    <p class="medals__bmp3-tl-bmname">' . $beatmap['SongTitle'] .'</p>
                                                    <p class="medals__bmp3-tl-artist">by <span class="medals__bmp3-bold">' . $beatmap['Artist'] .'</span></p>
                                                </div>
                                                <div class="medals__bmp3-top-right">
                                                    <p class="medals__bmp3-tr-difficulty">[' . $beatmap['DifficultyName'] .']</p>
                                                    <p class="medals__bmp3-tr-mapper">mapped by <span class="medals__bmp3-bold">' . $beatmap['Mapper'] .'</span></p>
                                                </div>
                                            </a>
                                            <div class="medals__bmp3-bottom">
                                                <p class="medals__bmp2-submitter">
                                                    submitted <span class="medals__bmp3-bold" tooltip-content="Mon Jan 10 2022">' . $beatmap['SubmissionDate'] .'</span></p>
                                                <div class="medals__bmp3-right">
                                                    <div class="medals__bmp3-r-note">submitted by <strong>' . $beatmap['Username'] . '</strong></div>
                                                    <div class="medals__bmp3-r-vote ">' . $beatmap['Votes'] . ' votes</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="statspanel">
                                <h1 class="stats-h1">Comment Stats</h1>
                                <h2 class="stats-h2">Top 10 comments</h2>
                                <?php
                                $data = Database::execSimpleSelect('SELECT SUM(Vote) AS Votes, Comments.PostText, Comments.UserID, Comments.Username, Medals.name
                                FROM Votes
                                INNER JOIN Comments ON Comments.ID = ObjectID
                                LEFT JOIN Medals ON Medals.medalid = Comments.MedalID
                                GROUP BY ObjectID, Type, Comments.PostText, Medals.name
                                HAVING Type = 1
                                ORDER BY SUM(Vote) DESC
                                LIMIT 10');
                                //print_r($data);
                                echo '<div class="comments__main-comment-area">';
                                foreach ($data as $comment) {
                                    echo '<h3 style="margin-bottom: -10px; margin-top: -4px;"><light>On: </light>' . $comment['name'] . '</h3>';
                                    echo '<div class="comments__comment">
                                  <a href="/profiles?user=' . $comment['UserID'] . '">
                                      <img src="https://a.ppy.sh/' . $comment['UserID'] . '?1595902675.jpeg" class="comments__pb-user-pfp">
                                  </a>
                                  <div class="comments__comment-div">
                                      <div class="comments__comment-top">
                                          <div class="comments__comment-top-username_area">
                                              <a href="/profiles?user=' . $comment['UserID'] . '">
                                                  <p class="comments__comment-top-username">' . $comment['Username'] . '</p>
                                              </a>
                                          </div>
                                          <p class="comments__comment-top-text">' . $comment['PostText'] . '</p>
                                      </div>
                                      <div class="comments__comment-bottom" style="display: flex;">
                                          <div class="comments__comment-vote" style="margin-left: auto;height: 30px; width: 100px; display: flex; align-items: center; justify-content: center;">
                                              <p class="comments__comment-vote-text">' . $comment['Votes'] . ' Votes</p>
                                          </div>
                                      </div>
                                  </div>
                              </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
<script>
                            const data = <?php echo json_encode($combined); ?>;
                            const loadtime = <?php echo json_encode($stats_loadtime_cleaned); ?>;
                            /* const ipgrabtime = <?php //echo json_encode($stats_ipgrabtime_cleaned); 
                                                    ?>; */
                            const ctx = document.getElementById("statsChart").getContext('2d');

                            const chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: Object.keys(data),
                                    datasets: [{
                                            label: 'Page Views',
                                            data: Object.values(data).map(x => x["count"]),
                                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Load Time',
                                            data: Object.values(loadtime),
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1,
                                            /* make taller */
                                            yAxisID: 'y-axis-2'
                                        }
                                        /* {
                                            label: 'IP Grab Time',
                                            data: Object.values(ipgrabtime),
                                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                            borderColor: 'rgba(255, 206, 86, 1)',
                                            borderWidth: 1,
                                            yAxisID: 'y-axis-2'
                                        } */
                                    ]
                                },

                            });
                        </script>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>

</html>