<?php
$caption = $_POST['caption'];
$author = $_POST['author'];


$id = time();

$target_dir = "../fun/images/";
$filename = $id . "_" . basename($_FILES["file"]["name"]);
$target_file = $target_dir . $filename;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    if (mime_content_type($target_file) == "image/png") {
        echo $target_file . "<br>";
        $img = imagecreatefrompng($target_file);
        echo "<br>generating from png</br>";
    } else {
        $img = imagecreatefromjpeg($target_file);
        echo "<br>generating from jpg</br>";
    }

    $widthMultiplier = imagesx($img) / imagesy($img);

    $heightMultiplier = imagesy($img) / imagesx($img);
    
    $imgResize = imagescale($img, 700, 700 * $heightMultiplier);
    $jpg = imagepng($imgResize, $target_file . ".tmp", 2);
    unlink($target_file);
    rename($target_file . ".tmp", $target_file);
} else {
    echo "Sorry, there was an error uploading your file.";
    exit;
}

Database::execOperation("INSERT INTO `AdminFunImages` (`id`, `path`, `caption`, `by`) VALUES (?,?,?,?);", "isss", [$id, $filename, $caption, $author]);

redirect("/admin/panel/home/images");
