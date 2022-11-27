<?php
    $f_contents = file("../splash.txt");
    $line = $f_contents[array_rand($f_contents)];
    $data = $line;
    echo $data;
?>