<?php
require_once("osekaiDB.php");
// report error

$oSessionKey = "";
$oSession;
$oSessionStart;
$_SESSION = array();



$started = false;
$justCreated = false;

$abortedSaves = 0;

// print errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

function olog($text) {
    //echo $text . "<br>";
}

function createSession()
{
    //echo "creating session";
    global $oSession;
    global $oSessionKey;
    global $started;
    global $justCreated;
    $justCreated = true;
    if(isset($_COOKIE['osekai__sessionToken'])) {
        return; // already has a session (probably), don't need to make new
    }
    $oSessionKey = "osekai__session_" . bin2hex(openssl_random_pseudo_bytes(16));
    Database::execOperation("INSERT INTO OsekaiSessions (sessionToken) VALUES (?)", "s", array($oSessionKey));
    setcookie("osekai__sessionToken", $oSessionKey, time() + (86400 * 30 * 12), "/");
    $_COOKIE['uname'] = $oSessionKey;
    $started = true;
    $oSession = Database::execSelect("SELECT * FROM OsekaiSessions WHERE sessionToken = ?", "s", array($oSessionKey))[0]["sessionData"];
    olog("Created session");
}

function saveSession()
{
    olog("Saving Session");
    global $oSession;
    global $oSessionKey;
    global $oSessionStart;
    global $abortedSaves;
    global $started;

    if($started == false) {
        olog("Session not started");
        return;
    };

    if ($oSession != $oSessionStart) {
        $oSessionTemp = json_encode($oSession);
        $sql = "UPDATE OsekaiSessions SET sessionData = ? WHERE sessionToken = ?";
        Database::execOperation($sql, "ss", array($oSessionTemp, $oSessionKey));
        $oSessionStart = $oSession;
        olog("Session Saved");
    } else {
        $abortedSaves++;
        olog("Save Aborted");
    }
}

function startSession()
{
    global $oSession;
    global $oSessionKey;
    global $_SESSION;
    global $started;
    global $oSessionStart;
    global $justCreated;

    // pointer to oSession
    $_SESSION = &$oSession;
    $needNewSession = true;

    if (isset($_COOKIE['osekai__sessionToken'])) {
        // cookie is here, let's load data from database
        //echo "you have cooki";
        $oSessionKey = $_COOKIE['osekai__sessionToken'];


        $oSession = Database::execSelect("SELECT * FROM OsekaiSessions WHERE sessionToken = ?", "s", array($oSessionKey));
        //echo "<br>" . count((array)$oSession) . " " . $oSessionKey;
        if (count((array)$oSession) == 1) {
            //echo "<br>you have session!!";
            olog("Started Session");
            $needNewSession = false;
            $oSession = json_decode($oSession[0]["sessionData"], true);
            $oSessionStart = $oSession;
            $started = true;
        } else {
            olog("Session does not exist. Deleted Session Cookie");
            //echo "<br>no session, new cooki '-'";
            // delete the cookie.
           
            if($justCreated == false) {
                flushSession();
            setcookie("osekai__sessionToken", "", time() - 3600);
            }
        }
    } else {
        olog("Cookie is not set");
        if($justCreated == false) {
        flushSession();
        }
    }
}

function flushSession()
{
    global $oSession;
    global $oSessionKey;

    // gonna delete the session
    Database::execOperation("DELETE FROM OsekaiSessions WHERE sessionToken = ?", "s", array($oSessionKey));
    unset($oSession);
    unset($_SESSION);
    setcookie("osekai__sessionToken", "", time() - 3600, "/");
}

function logIn($code)
{
    olog("Logging in");
    global $oSession;

    $post = [
        'client_id' => OSU_OAUTH_CLIENT_ID,
        'client_secret' => OSU_OAUTH_CLIENT_SECRET,
        'code'   => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => OSU_OAUTH_REDIRECT_URI
    ];

    $ch = curl_init('https://osu.ppy.sh/oauth/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $response = curl_exec($ch);
    $response = json_decode($response, true);

    curl_close($ch);

    $oSession['token'] = "";
    $oSession['token'] = $response['access_token'];
    if (isset($response['refresh_token'])) {
        $oSession['refreshToken'] = $response['refresh_token'];
    }
    $oSession['lastTokenUpdate'] = time();
    saveSession();
    updateData();
}

function refreshToken()
{
    return;
    global $oSession;

    if (isset($oSession['refreshToken'])) {
        $post = [
            'client_id' => OSU_OAUTH_CLIENT_ID,
            'client_secret' => OSU_OAUTH_CLIENT_SECRET,
            'grant_type' => 'refresh_token',
            'refresh_token' => $oSession['refreshToken']
        ];

        $ch = curl_init('https://osu.ppy.sh/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = json_decode(curl_exec($ch), true);

        curl_close($ch);

        $oSession['token'] = $response['access_token'];
        $oSession['refreshToken'] = $response['refresh_token'];
        $oSession['lastTokenUpdate'] = time();
        updateData();

        saveSession();
    }
}

function updateData()
{
    olog("Updating Data");
    global $oSession;

    if (isset($oSession['token'])) {
        // if older than 12 hours, refresh token
        if ($oSession['lastTokenUpdate']) {
            if (time() - $oSession['lastTokenUpdate'] > 43200) {
                refreshToken();
            }
        }
        $headers = [
            'Authorization: Bearer ' . $oSession['token'],
            'Content-Type: application/json'
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/v2/me/osu");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $userDataOsu = json_decode(curl_exec($curl), true);

        curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/v2/me/fruits");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $userDataFruits = json_decode(curl_exec($curl), true);

        curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/v2/me/taiko");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $userDataTaiko = json_decode(curl_exec($curl), true);

        curl_setopt($curl, CURLOPT_URL, "https://osu.ppy.sh/api/v2/me/mania");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $userDataMania = json_decode(curl_exec($curl), true);
        curl_close($curl);
        if (!isset($userDataOsu['id'])) redirect("https://osu.ppy.sh/oauth/authorize?response_type=code&client_id=OSU_OAUTH_CLIENT_ID&redirect_uri=https%3A%2F%2Fosekai.net%2Fglobal%2Fphp%2Flogin.php");

        $userDataOsu['page'] = "";
        $userDataFruits['page'] = "";
        $userDataTaiko['page'] = "";
        $userDataMania['page'] = "";

        $userDataOsu['monthly_playcounts'] = [];
        $userDataFruits['monthly_playcounts'] = [];
        $userDataTaiko['monthly_playcounts'] = [];
        $userDataMania['monthly_playcounts'] = [];

        $userDataOsu['user_achievements'] = [];
        $userDataFruits['user_achievements'] = [];
        $userDataTaiko['user_achievements'] = [];
        $userDataMania['user_achievements'] = [];

        $userDataOsu['rankHistory'] = [];
        $userDataFruits['rankHistory'] = [];
        $userDataTaiko['rankHistory'] = [];
        $userDataMania['rankHistory'] = [];

        $userDataOsu['rank_history'] = [];
        $userDataFruits['rank_history'] = [];
        $userDataTaiko['rank_history'] = [];
        $userDataMania['rank_history'] = [];

        $userDataOsu['statistics_rulesets'] = [];
        $userDataFruits['statistics_rulesets'] = [];
        $userDataTaiko['statistics_rulesets'] = [];
        $userDataMania['statistics_rulesets'] = [];

        $userDataOsu['profile_order'] = [];
        $userDataFruits['profile_order'] = [];
        $userDataTaiko['profile_order'] = [];
        $userDataMania['profile_order'] = [];

        $unneeded = [
            "avatar_url", "country_code", "default_group", "id", "is_active", "is_bot", "is_deleted", "is_online", "is_supporter",
            "last_visit", "pm_friends_only", "username", "cover_url", "discord", "has_supported", "interests", "join_date",
            "kudosu", "location", "max_blocks", "max_friends", "occupation", "post_count", "profile_order", "title",
            "title_url", "twitter", "website", "country", "cover", "is_restricted", "active_tournament_banner", "groups",
            "page", "pending_beatmapset_count", "previous_usernames", "support_level"
        ];
        // all of these are the same across all gamemodes
        // so we can remove them on all but standard

        // there are some im missing dont care

        foreach ($unneeded as $key) {
            unset($userDataFruits[$key]);
            unset($userDataTaiko[$key]);
            unset($userDataMania[$key]);
        }

        $oSession['osu'] = $userDataOsu;
        $oSession['fruits'] = $userDataFruits;
        $oSession['taiko'] = $userDataTaiko;
        $oSession['mania'] = $userDataMania;

        // echo "<pre>";
        // echo json_encode($oSession, JSON_PRETTY_PRINT);
        // echo "</pre>";
        // exit;

        $arrUser = Database::execSelect("SELECT RoleName, Rights FROM Roles WHERE UserID = ?", "i", array($oSession['osu']['id']));

        $userCount = 0;
        foreach ($arrUser as $user) {
            $userCount++;
        }

        if ($userCount > 0) {
            $oSession['role'] = array();
            $oSession['role']['name'] = $arrUser[0]['RoleName'];
            $oSession['role']['rights'] = $arrUser[0]['Rights'];
        }

        Database::execOperation("INSERT INTO Members (id) VALUES (?) ON DUPLICATE KEY UPDATE id = id", "i", array($oSession['osu']['id']));
        $arrMembers = Database::execSelect("SELECT * FROM Members WHERE id = ?", "i", array($oSession['osu']['id']));
        $oSession['options']['experimental'] = $arrMembers[0]['OPT_Experimental'];

        saveSession();
    }
}
