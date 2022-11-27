<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['mode'])) {
    if($_POST['mode'] == "osu") echo json_encode(Database::execSimpleSelect("SELECT * FROM Ranking WHERE NOT IsNull(standard_global) AND standard_global <> 0 ORDER BY standard_global, standard_pp DESC LIMIT 50"));
    if($_POST['mode'] == "taiko") echo json_encode(Database::execSimpleSelect("SELECT * FROM Ranking WHERE NOT IsNull(taiko_global) AND taiko_global <> 0 ORDER BY taiko_global, taiko_pp DESC LIMIT 50"));
    if($_POST['mode'] == "fruits") echo json_encode(Database::execSimpleSelect("SELECT * FROM Ranking WHERE NOT IsNull(ctb_global) AND ctb_global <> 0 ORDER BY ctb_global, ctb_pp DESC LIMIT 50"));
    if($_POST['mode'] == "mania") echo json_encode(Database::execSimpleSelect("SELECT * FROM Ranking WHERE NOT IsNull(mania_global) AND mania_global <> 0 ORDER BY mania_global, mania_pp DESC LIMIT 50"));
    if($_POST['mode'] == "all") echo json_encode(Database::execSimpleSelect("SELECT * FROM Ranking WHERE NOT IsNull(stdev_pp) AND stdev_pp <> 0 ORDER BY stdev_pp DESC LIMIT 50"));
}
?>