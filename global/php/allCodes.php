<?php
$useJS = false;
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    exit ('php_intl extension is available on PHP 5.3.0 or later.');
}    
if (!class_exists('Locale')) {
    exit ('You need to install php_intl extension.');
}


foreach($locales as $locale) {
    if ($locale['code'] == "zh_TW")
    {
        echo "Traditional ";
    }
    if ($locale['code'] == "zh_CN")
    {
        echo "Simplified ";
    }
    echo Locale::getDisplayLanguage($locale['code'], 'en_GB') . " => " . $locale['name'] . " => " . $locale['code'] . "<br>";
}