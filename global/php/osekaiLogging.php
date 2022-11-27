<?php
class Logging {
    public static function ReadChanges($old, $new)
    {
        $text = "";
        foreach ($new as $key => $value) {
            if ($old[$key] != $new[$key]) {
                $text .= "<p><strong>" . $key . ": </strong><light>" . $old[$key] . " -> </light>" . $new[$key] . "</p>";
            }
        }
        return $text;
    }
    // importance:
    // 1 = verbose
    // 2 = info
    // 3 = normal
    // 4 = important
    // 5 = error
    public static function PutLog($logtext, $app = -1, $importance = 3) {
        $user = 0;
        if(isset($_SESSION['osu'])) {
            $user = $_SESSION['osu']['id'];
        }
        Database::execOperation("INSERT INTO `AdminLogs` (`user`, `data`, `app`, `importance`)
        VALUES (?,?, ?, ?);", "isii", [$user, $logtext, $app, $importance]);
    }
}