<?php
function GetBearer()
{
    $post = [
        'client_id' => OSU_OAUTH_CLIENT_ID,
        'client_secret' => OSU_OAUTH_CLIENT_SECRET,
        'grant_type' => 'client_credentials',
        'scope' => 'public'
    ];

    $oAccess = curl_init('https://osu.ppy.sh/oauth/token');
    curl_setopt($oAccess, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($oAccess, CURLOPT_POSTFIELDS, $post);

    $response = json_decode(curl_exec($oAccess), true);

    curl_close($oAccess);

    $date = new DateTime();

    $_SESSION['access_token_timer'] = $date->getTimestamp();
    $_SESSION['access_token'] = $response['access_token'];
    $_SESSION['access_token_expiration'] = $response['expires_in'];
}

function IsExpired()
{
    if (isset($_SESSION['access_token_expiration'])) {
        $timer = intval($_SESSION['access_token_timer']);
        $expiration = $_SESSION['access_token_expiration'] + $timer;
        $date = new DateTime();
        return $expiration < $date->getTimestamp();
    } else {
        return true;
    }
}

function GetHeaders()
{
    return [
        'Authorization: Bearer ' . $_SESSION['access_token'],
        'Content-Type: application/json'
    ];
}

function curlRequestUser($strSearch)
{
    if (IsExpired() == true) GetBearer();
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, "https://osu.ppy.sh/api/v2/users/" . $strSearch);
    curl_setopt($handle, CURLOPT_HTTPHEADER, GetHeaders());
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

    $result = curl_exec($handle);
    $response_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    if ($response_code == 404)
        return null;

    return json_decode($result, true);
}

function v2_getUser($userID, $mode = null, $sendMedals = true, $useAllMedals = true)
{
    $useAllMedals = filter_var($useAllMedals, FILTER_VALIDATE_BOOLEAN);
    if (IsExpired() == true) GetBearer();
    if ($userID == 727) $userID = 124493; //chocomint exception

    $strSearch = $userID;
    if ($mode == "null" || $mode == "undefined") {
        return curlRequestUser($userID . "/");
    }
    if ($mode != null && $mode != "all") {
        $strSearch .= "/" . $mode;
    }

    $oUserGroups = Database::execSelect("SELECT * FROM GroupAssignments WHERE UserId = ?", "i", array($userID));

    $oMedals = Database::execSimpleSelect("SELECT * From Medals LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
        "LEFT JOIN (SELECT COUNT(medalid) AS MedalCount FROM Medals) t ON 1 = 1 ");

    if ($mode != "all") {
        $colData = curlRequestUser($strSearch);

        if (!isset($colData))
            return null;

        $oUserGoals = Database::execSelect("SELECT * FROM Goals WHERE UserID = ? AND Gamemode = ? Order by Claimed", "is", array($userID, $mode));
        $oUserTimeline = Database::execSelect("SELECT * FROM Timeline WHERE UserID = ? AND Mode = ?", "is", array($userID, $mode)) ?? array();

        if ($sendMedals == true) {
            $oUserMedals = Database::execSelect("SELECT * FROM ( " .
                "SELECT @r := @r+1 AS rank, t1.* FROM ( " .
                "SELECT Ranking.id " .
                "FROM Ranking " .
                "INNER JOIN Medals ON Ranking.rarest_medal = Medals.medalid " .
                "LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
                "ORDER BY Ranking.medal_count DESC, MedalRarity.frequency  " .
                ") t1, (SELECT @r:=0) t2 LIMIT 3000 " .
                ") t3 WHERE id = ?", "i", array($userID));

            if ($useAllMedals == false) {
                $oMedals = Database::execSelect("SELECT * From Medals LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
                    "LEFT JOIN (SELECT COUNT(medalid) AS MedalCount FROM Medals WHERE restriction = 'NULL' Or restriction = ?) t ON 1 = 1 " .
                    "WHERE (restriction = 'NULL' Or restriction = ?)", "ssssssssss", array($mode, $mode));
            }
            $colData['max_medals'] = $oMedals[0]['MedalCount'];

            foreach ($colData['user_achievements'] as $key => $osumedal) {
                $bInMode = false;
                foreach ($oMedals as $medalkey => $medal) {
                    if ($osumedal['achievement_id'] == $medal['medalid']) {
                        $colData['user_achievements'][$key]['name'] = $medal['name'];
                        $colData['user_achievements'][$key]['link'] = $medal['link'];
                        $colData['user_achievements'][$key]['description'] = $medal['description'];
                        $colData['user_achievements'][$key]['mode'] = $medal['restriction'];
                        $colData['user_achievements'][$key]['grouping'] = $medal['grouping'];
                        $colData['user_achievements'][$key]['instructions'] = $medal['instructions'];
                        $colData['user_achievements'][$key]['ordering'] = $medal['ordering'];
                        $colData['user_achievements'][$key]['frequency'] = $medal['frequency'] ?? 0;
                        $bInMode = true;
                        unset($oMedals[$medalkey]);
                    }
                }
                if ($bInMode == false && $useAllMedals == false) unset($colData['user_achievements'][$key]);
            }

            sort($colData['user_achievements']);

            $colData['user_achievements_total']['global_rank'] = isset($oUserMedals[0]['rank']) ? $oUserMedals[0]['rank'] : 0;
            $colData['user_achievements_total']['completion'] = round(count($colData['user_achievements']) * 100 / $colData['max_medals'], 2) ?: 0;

            $colData['unachieved'] = [];
            foreach ($oMedals as $medalkey => $medal) {
                $colData['unachieved'][$medalkey]['medalid'] = $medal['medalid'];
                $colData['unachieved'][$medalkey]['name'] = $medal['name'];
                $colData['unachieved'][$medalkey]['link'] = $medal['link'];
                $colData['unachieved'][$medalkey]['description'] = $medal['description'];
                $colData['unachieved'][$medalkey]['mode'] = $medal['restriction'];
                $colData['unachieved'][$medalkey]['grouping'] = $medal['grouping'];
                $colData['unachieved'][$medalkey]['instructions'] = $medal['instructions'];
                $colData['unachieved'][$medalkey]['ordering'] = $medal['ordering'];
                $colData['unachieved'][$medalkey]['frequency'] = $medal['frequency'] ?? 0;
            }

            sort($colData['unachieved']);
        }

        array_unshift($oUserTimeline, [
            "id" => 10000000,
            "UserID" => $userID,
            "Date" => date_format(date_create_from_format('Y-m-d\TH:i:sP', $colData['join_date']), "Y-m-d"),
            "Note" => "Joined",
            "Mode" => "all",
            "Changeable" => "false"
        ]);
        array_push($oUserTimeline, [
            "id" => 10000001,
            "UserID" => $userID,
            "Date" => date("Y-m-d"),
            "Note" => "Now",
            "Mode" => "all",
            "Changeable" => "false"
        ]);

        $colData['goals'] = $oUserGoals;
        $colData['timeline'] = $oUserTimeline;
        $colData['usergroups'] = $oUserGroups; // would use 'groups' but osu! already uses that for their groups

        return json_encode($colData);
    } else {
        //Random order in order to mix up requests to the osu api and osekai db in order to decrease stress on either of them
        $oUserGoals = Database::execSelect("SELECT * FROM Goals WHERE UserID = ? Order by Claimed", "i", array($userID));
        $oUserTimeline = Database::execSelect("SELECT * FROM Timeline WHERE UserID = ?", "i", array($userID)) ?? array();

        $colOsu = curlRequestUser($userID . "/osu");

        // If osu user is found, the other modes should be found too (?)
        // So the null check is just done once
        if (!isset($colOsu))
            return null;

        if ($sendMedals == true) {
            $oUserMedals = Database::execSelect("SELECT * FROM ( " .
                "SELECT @r := @r+1 AS rank, t1.* FROM ( " .
                "SELECT Ranking.id, " .
                "ROUND(Ranking.medal_count * 100 / (SELECT COUNT(Medals.medalid) FROM Medals), 2) AS completion " .
                "FROM Ranking " .
                "INNER JOIN Medals ON Ranking.rarest_medal = Medals.medalid " .
                "LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid " .
                "ORDER BY Ranking.medal_count DESC, MedalRarity.frequency  " .
                ") t1, (SELECT @r:=0) t2 LIMIT 3000 " .
                ") t3 WHERE id = ?", "i", array($userID));
        }

        $colCatch = curlRequestUser($userID . "/fruits");

        $oUserSPP = Database::execSelect("SELECT * FROM ( " .
            "SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT id, total_pp AS tpp " .
            "FROM Ranking " .
            "ORDER BY total_pp DESC, stdev_pp DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 3000 " .
            ") t3 WHERE id = ?", "i", array($userID));

        $colTaiko = curlRequestUser($userID . "/taiko");

        $oUserSPPCountry = Database::execSelect("SELECT * FROM ( " .
            "SELECT @r := @r+1 AS rank, t1.* FROM ( " .
            "SELECT id, total_pp AS tpp " .
            "FROM Ranking " .
            "WHERE Ranking.country_code = ? " .
            "ORDER BY total_pp DESC, stdev_pp DESC " .
            ") t1, (SELECT @r:=0) t2 LIMIT 1000 " .
            ") t3 WHERE id = ?", "si", array($colOsu['country_code'], $userID));

        $colMania = curlRequestUser($userID . "/mania");

        $colOsu['max_medals'] = $oMedals[0]['MedalCount'];
        if ($sendMedals == true) {
            foreach ($colOsu['user_achievements'] as $key => $osumedal) {
                foreach ($oMedals as $medalkey => $medal) {
                    if ($osumedal['achievement_id'] == $medal['medalid']) {
                        $colOsu['user_achievements'][$key]['name'] = $medal['name'];
                        $colOsu['user_achievements'][$key]['link'] = $medal['link'];
                        $colOsu['user_achievements'][$key]['description'] = $medal['description'];
                        $colOsu['user_achievements'][$key]['mode'] = $medal['restriction'];
                        $colOsu['user_achievements'][$key]['grouping'] = $medal['grouping'];
                        $colOsu['user_achievements'][$key]['instructions'] = $medal['instructions'];
                        $colOsu['user_achievements'][$key]['ordering'] = $medal['ordering'];
                        $colOsu['user_achievements'][$key]['frequency'] = $medal['frequency'] ?? 0;
                        unset($oMedals[$medalkey]);
                    }
                }
            }

            sort($colOsu['user_achievements']);

            $colOsu['user_achievements_total']['global_rank'] = $oUserMedals[0]['rank'] ?: 0;
            $colOsu['user_achievements_total']['completion'] = $oUserMedals[0]['completion'] ?: round(count($colOsu['user_achievements']) * 100 / $colOsu['max_medals'], 2) ?: 0;

            $colOsu['unachieved'] = [];
            foreach ($oMedals as $medalkey => $medal) {
                $colOsu['unachieved'][$medalkey]['medalid'] = $medal['medalid'];
                $colOsu['unachieved'][$medalkey]['name'] = $medal['name'];
                $colOsu['unachieved'][$medalkey]['link'] = $medal['link'];
                $colOsu['unachieved'][$medalkey]['description'] = $medal['description'];
                $colOsu['unachieved'][$medalkey]['mode'] = $medal['restriction'];
                $colOsu['unachieved'][$medalkey]['grouping'] = $medal['grouping'];
                $colOsu['unachieved'][$medalkey]['instructions'] = $medal['instructions'];
                $colOsu['unachieved'][$medalkey]['ordering'] = $medal['ordering'];
                $colOsu['unachieved'][$medalkey]['frequency'] = $medal['frequency'] ?? 0;
            }

            sort($colOsu['unachieved']);
        }

        array_unshift($oUserTimeline, [
            "id" => 10000000,
            "UserID" => $userID,
            "Date" => date_format(date_create_from_format('Y-m-d\TH:i:sP', $colOsu['join_date']), "Y-m-d"),
            "Note" => "Joined",
            "Mode" => "all",
            "Changeable" => "false"
        ]);
        array_push($oUserTimeline, [
            "id" => 10000001,
            "UserID" => $userID,
            "Date" => date("Y-m-d"),
            "Note" => "Now",
            "Mode" => "all",
            "Changeable" => "false"
        ]);

        $colOsu['taiko'] = $colTaiko;
        $colOsu['fruits'] = $colCatch;
        $colOsu['mania'] = $colMania;
        $colOsu['osu'] = $colOsu;

        $OsuPP = $colOsu['statistics']['pp'];
        $TaikoPP = $colTaiko['statistics']['pp'];
        $CatchPP = $colCatch['statistics']['pp'];
        $ManiaPP = $colMania['statistics']['pp'];

        // I dont like this here, but its the only place in code where its used
        // so its better to have it here than on the global scope
        function spp($arr)
        {
            $arr_size = count($arr);
            $mu = array_sum($arr) / $arr_size;
            $ans = 0;
            foreach ($arr as $elem) {
                $ans += pow(($elem - $mu), 2);
            }
            return array_sum($arr) - (sqrt($ans / ($arr_size - 1))) * 2;
        }

        $colOsu['statistics']['std_dev_pp'] = spp([$OsuPP, $TaikoPP, $CatchPP, $ManiaPP]);

        $colOsu['goals'] = $oUserGoals;
        $colOsu['timeline'] = $oUserTimeline;
        $colOsu['usergroups'] = $oUserGroups;

        $colOsu['statistics']['global_rank'] = $oUserSPP[0]['rank'] ?: 0;
        unset($colOsu['rank_history']['data']);
        $colOsu['statistics']['country_rank'] = $oUserSPPCountry[0]['rank'] ?: 0;
        $colOsu['statistics']['pp'] = $OsuPP + $TaikoPP + $CatchPP + $ManiaPP;
        $colOsu['statistics']['play_time'] = $colOsu['statistics']['play_time'] + $colCatch['statistics']['play_time'] + $colMania['statistics']['play_time'] + $colTaiko['statistics']['play_time'];
        $colOsu['statistics']['play_count'] = $colOsu['statistics']['play_count'] + $colCatch['statistics']['play_count'] + $colMania['statistics']['play_count'] + $colTaiko['statistics']['play_count'];
        $colOsu['statistics']['grade_counts']['ssh'] = $colOsu['statistics']['grade_counts']['ssh'] + $colCatch['statistics']['grade_counts']['ssh'] + $colMania['statistics']['grade_counts']['ssh'] + $colTaiko['statistics']['grade_counts']['ssh'];
        $colOsu['statistics']['grade_counts']['ss'] = $colOsu['statistics']['grade_counts']['ss'] + $colCatch['statistics']['grade_counts']['ss'] + $colMania['statistics']['grade_counts']['ss'] + $colTaiko['statistics']['grade_counts']['ss'];
        $colOsu['statistics']['grade_counts']['sh'] = $colOsu['statistics']['grade_counts']['sh'] + $colCatch['statistics']['grade_counts']['sh'] + $colMania['statistics']['grade_counts']['sh'] + $colTaiko['statistics']['grade_counts']['sh'];
        $colOsu['statistics']['grade_counts']['s'] = $colOsu['statistics']['grade_counts']['s'] + $colCatch['statistics']['grade_counts']['s'] + $colMania['statistics']['grade_counts']['s'] + $colTaiko['statistics']['grade_counts']['s'];
        $colOsu['statistics']['grade_counts']['a'] = $colOsu['statistics']['grade_counts']['a'] + $colCatch['statistics']['grade_counts']['a'] + $colMania['statistics']['grade_counts']['a'] + $colTaiko['statistics']['grade_counts']['a'];
        if ($colOsu['statistics']['pp'] && $colCatch['statistics']['pp'] && $colMania['statistics']['pp'] && $colTaiko['statistics']['pp']) {
            $colOsu['statistics']['hit_accuracy'] = (($OsuPP * $colOsu['statistics']['hit_accuracy']) + ($colCatch['statistics']['pp'] * $colCatch['statistics']['hit_accuracy']) + ($colMania['statistics']['pp'] * $colMania['statistics']['hit_accuracy']) + ($colTaiko['statistics']['pp'] * $colTaiko['statistics']['hit_accuracy'])) / $colOsu['statistics']['pp'];
        } else {
            $colOsu['statistics']['hit_accuracy'] = 0;
        }

        return json_encode($colOsu);
    }
}

function v2_search($query)
{
    if (IsExpired() == true) GetBearer();

    $strSearch = "?mode=user&query=" . str_replace(' ', '%20', $query);
    $oUser = curl_init();
    curl_setopt($oUser, CURLOPT_URL, "https://osu.ppy.sh/api/v2/search/" . $strSearch);
    curl_setopt($oUser, CURLOPT_HTTPHEADER, GetHeaders());
    curl_setopt($oUser, CURLOPT_RETURNTRANSFER, TRUE);

    return curl_exec($oUser);
}

function v2_recent_scores($gamemode = "osu", $user = null)
{
    if (IsExpired() == true) GetBearer();
    if ($user == null) $user = $_SESSION['osu']['id'];

    $oUser = curl_init();
    //echo "https://osu.ppy.sh/api/v2/users/" . $user . "/scores/recent?gamemode=" . $gamemode;
    curl_setopt($oUser, CURLOPT_URL, "https://osu.ppy.sh/api/v2/users/" . $user . "/scores/recent?mode=" . $gamemode);
    curl_setopt($oUser, CURLOPT_HTTPHEADER, GetHeaders());
    curl_setopt($oUser, CURLOPT_RETURNTRANSFER, TRUE);

    echo curl_exec($oUser);
}
