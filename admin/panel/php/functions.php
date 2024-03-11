<?php
function AddCss($link) {
    echo '<link rel="stylesheet" href="' . $link .'">';
}
function Js($path) {
    echo '<script src="/admin/panel/js/'.$path.'"></script>';
}
function aCss($path) {
    echo '<link rel="stylesheet" href="/admin/panel/css/'.$path.'">';
}