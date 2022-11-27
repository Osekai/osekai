<?php

$app = "medals";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$preloadimg = Database::execSimpleSelect("SELECT link FROM Medals ORDER BY grouping");
echo "<div>";
foreach ($preloadimg as $a) {

    $link = $a['link'];
    $link = str_replace(".png", "@2x.png", $link);
    echo '<img src="' . $link . '">';
    // basically preloads all the images so that on browsers they load in more smoothly (loaded after js and before content opens i think?)
}
echo "</div>";
?>

<style>
*{
    padding: 0px;
    margin: 0px;
}
img{
    width: 100px;
    height: 106px;
}
div{
    display: flex;
    gap: 14px;
    padding: 10px;
    background-color: #0b0c11;
    flex-wrap: wrap;
}
</style>