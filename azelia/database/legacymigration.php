
<style>
    * {
        padding: 0px;
        margin: 0px;
        /* use monospace */
        font-family: monospace;
        background-color: #000;
        color: #f55;
    }
    b {
        color: inherit;
    }
    p {
        margin: 4px;
        padding: 4px;
        background-color: #111;
    }
    .big {
        background-color: #0c0c0c;
        font-size: 18px;
    }
</style>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
function migrationLog($text, $title = "log", $color = "#000", $big = false)
{
    echo "<p style='color: $color;'";
    if ($big) {
        echo " class='big'";
    }
    echo ">";

    echo "<b>" . $title . "</b>: " . $text;
    echo "</p>";
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// report using migrationError()
set_error_handler("migrationError");
function migrationError($errno, $errstr, $errfile, $errline)
{
    $reportstr = $errstr . " in " . $errfile . " on line " . $errline;
    migrationLog($reportstr, "ERROR", "#f00");
}

// print current root
migrationLog("Current root: " . $_SERVER['DOCUMENT_ROOT'], "info", "#aaa");
$legacyroot = $_SERVER['DOCUMENT_ROOT'] . "snapshots/";
$azeliaroot = $_SERVER['DOCUMENT_ROOT'] . "azelia/";
migrationLog("Legacy root: " . $legacyroot, "info", "#ccc");
migrationLog("Azelia root: " . $azeliaroot, "info", "#ccc");

$legacyVersions = array();
$legacyVersionsRaw = file_get_contents("/snapshots/api/api");
$legacyVersions = json_decode($legacyVersionsRaw, true);
// log, but only first 10 characters
migrationLog("Legacy versions: " . count($legacyVersions) . ". Check CONSOLE for more", "data_legacy", "#294", true);
echo "<script>var legacyVersions = " . json_encode($legacyVersions) . ";</script>";
echo "<script>console.log(legacyVersions);</script>";

migrationLog("Wiping new database", "info", "#42c", true);
// wipe SnapshotsAzeliaVersions, SnapshotsAzeliaDownloads, SnapshotsAzeliaScreenshots
Database::execOperation("DELETE FROM SnapshotsAzeliaDownloads WHERE ? = ?", "ii", array(1, 1));
Database::execOperation("DELETE FROM SnapshotsAzeliaScreenshots WHERE ? = ?", "ii", array(1, 1));
Database::execOperation("DELETE FROM SnapshotsAzeliaVersions WHERE ? = ?", "ii", array(1, 1));

migrationLog("Ignore above errors. They are expected", "info", "#aaa");

$newVersions = array();
// {SECTION} Clean up legacy data
migrationLog("Cleaning up legacy data and inserting into db", "info", "#42c", true);
for($i = 0; $i < count($legacyVersions); $i++)
{
    $thisVer = $legacyVersions[$i];
    
    // {SECTION} place into database
    $sql = "INSERT INTO `SnapshotsAzeliaVersions` (`Id`, `Name`, `Title`, `ReleaseDate`, `ArchivalDate`, `Archiver`, `ArchiverID`, `Description`, `ExtraInfo`, `Note`, `AutoUpdates`, `Video`, `Views`, `Downloads`, `Group`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `Id` = `Id`";

    $params_types = "isssssisssisiii";
    $none = null;
    if($thisVer["archive_info"]["auto_update"] == "true")
    {
        $autoUpdates = 1;
    }
    else
    {
        $autoUpdates = 0;
    }

    $video = $thisVer["archive_info"]["video"];
    // strip https://youtube.com/embed/
    $video = str_replace("https://youtube.com/embed/", "", $video);
    // strip ?rel=0
    $video = str_replace("?rel=0", "", $video);
    $none_int = 0;

    // convert release to timestamp which sql will understand
    $releaseDate = $thisVer["version_info"]["release"];
    $archivalDate = $thisVer["archive_info"]["upload_date"];
    // release date is a unix timestamp, while archival date is YYYY-MM-DD HH:MM:SS. if we use strtotime on the release date, it will return a wildly incorrect value

    $archivalDate = strtotime($archivalDate);
    $releaseDate = date("Y-m-d H:i:s", $releaseDate);
    $archivalDate = date("Y-m-d H:i:s", $archivalDate);


    $releaseDate = strtotime($releaseDate);
    $archivalDate = strtotime($archivalDate);
    $releaseDate = date("Y-m-d H:i:s", $releaseDate);
    $archivalDate = date("Y-m-d H:i:s", $archivalDate);
    // i honestly don't know why this is necessary, but it is

    //$releaseDate = date("Y-m-d H:i:s", $releaseDate);
    //$archivalDate = date("Y-m-d H:i:s", $archivalDate);

    $group = $thisVer["archive_info"]["group"] - 1;

    $params = array(
        intval($thisVer["version_info"]["id"]),
        $thisVer["version_info"]["version"],
        $thisVer["version_info"]["name"],
        $releaseDate,
        $archivalDate,
        $thisVer["archive_info"]["archiver"],
        intval($thisVer["archive_info"]["archiver_id"]),
        $thisVer["archive_info"]["description"],
        $thisVer["archive_info"]["extra_info"],
        $none,
        $autoUpdates,
        $video,
        intval($thisVer["stats"]["views"]),
        intval($thisVer["stats"]["downloads"]),
        $group
    );

    // print params and their types
    for($j = 0; $j < count($params); $j++)
    {
        echo "[type: " . gettype($params[$j]) . " : " . substr(strval($params_types), $j, 1) . "] : " . $params[$j] . "<br>";
    }

    $ex = Database::execOperation($sql, $params_types, $params);
    migrationLog("Inserted version " . $thisVer["version_info"]["version"] . " - " . $ex, "info", "#aaa");

    $screenshots = $thisVer["screenshots"];
    $downloads = $thisVer["downloads"];
    $order = 0;
    for($x = 0; $x < count($screenshots); $x++)
    {
        $thisScreenshot = $screenshots[$x];

        $sql = "INSERT INTO `SnapshotsAzeliaScreenshots` (`ReferencedVersion`, `Order`, `ImageLink`)
        VALUES (?, ?, ?)";

        $params_types = "iis";
        $params = array(
            intval($thisVer["version_info"]["id"]),
            $order,
            $thisScreenshot
        );

        $ex = Database::execOperation($sql, $params_types, $params);
        
        $order++;
    }
    foreach($downloads as $thisDownload)
    {
        $sql = "INSERT INTO `SnapshotsAzeliaDownloads` (`ReferencedVersion`, `Name`, `Link`, `Recommended`)
        VALUES (?, ?, ?, ?)";

        $params_types = "issi";

        $recommended = 0;
        if($thisDownload["name"] == "Osekai Servers") {
            $recommended = 1;
        }

        $params = array(
            intval($thisVer["version_info"]["id"]),
            $thisDownload["name"],
            $thisDownload["link"],
            $recommended
        );

        $ex = Database::execOperation($sql, $params_types, $params);
    }
}




migrationLog("Done! Cleaned up " . count($legacyVersions) . " versions", "info", "#42c", true);

$newVersions = file_get_contents("/azelia/api/get_versions.php");
$newVersions = json_decode($newVersions, true);

migrationLog("New versions: " . count($newVersions) . ". Check CONSOLE for more", "data_new", "#294", true);
echo "<script>var newVersions = " . json_encode($newVersions) . ";</script>";
echo "<script>console.log(newVersions);</script>";
// check if count of new versions is the same as legacy. if not, error
if(count($newVersions) != count($legacyVersions))
{
    migrationLog("ERROR: Count of new versions is not the same as legacy versions. Versions missing:", "error", "#f00");
    for($i = 0; $i < count($legacyVersions); $i++)
    {
        $versionID = $legacyVersions[$i]["version_info"]["id"];
        $found = false;
        for($j = 0; $j < count($newVersions); $j++)
        {
            if($newVersions[$j]["Id"] == $versionID)
            {
                $found = true;
                break;
            }
        }
        if(!$found)
        {
            migrationLog("- " . $legacyVersions[$i]["version_info"]["version"], "error", "#f00");
        }
    }
}
else {
    migrationLog("All legacy versions have been successfully migrated to AZELIA", "info", "#42c", true);

}

?>