<?php
$app = "teams";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include("../pages.php");
if (isset($_REQUEST['page'])) {
    foreach ($pages as $ref_page) {
        if ($ref_page['name'] == $_REQUEST['page']) {
            if (isset($_POST['team'])) {
                $team = [
                    "Tag" => "@osekai",
                ];
                // ! TODO ^ make it use api
            }
            if (isset($_REQUEST['subpage'])) {
                foreach ($ref_page['pages'] as $ref_page_inner) {
                    if($ref_page_inner['name'] == $_REQUEST['subpage']) {
                        include("../views/" . $ref_page_inner['page']);
                    }
                }
            } else {
                include("../views/" . $ref_page['page']);
            }
        }
    }
} else {
    echo "No page set";
    exit;
}