<?php
$key = $_GET['key'];
if(!isset($_GET['key'])) {
    $key = $_POST['key'];
}
if($key != SCRIPTS_RUST_KEY) {
    echo "no perms";
    exit;
}

function sqlbuilder($table, $columns)
{
    // INSERT INTO `Medals` (`medalid`, `name`, `link`, `description`, `restriction`, `grouping`, `instructions`, `ordering`) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `medalid`=VALUES(`medalid`), `name`=VALUES(`name`), `link`=VALUES(`link`), `description`=VALUES(`description`), `restriction`=VALUES(`restriction`), `grouping`=VALUES(`grouping`), `ordering`=VALUES(`ordering`), `instructions`=VALUES(`instructions`);
    $column_sql = "";
    $values = "";
    $update_sql = "";
    for($x = 0; $x < count($columns); $x++){
        $last = false;
        $column_sql .= "`".$columns[$x]."`";
        $values .= "?";
        $update_sql .= "`" . $columns[$x] . "`=VALUES(`" . $columns[$x] . "`)";
        if($x+1 == count($columns)) $last = true;
        if($last == false) {
            $column_sql .= ",";
            $values .= ",";
            $update_sql .= ",";
        }
    }
    $sql = "INSERT INTO `$table` ($column_sql) VALUES ($values) ON DUPLICATE KEY UPDATE $update_sql;";
    return $sql;
}