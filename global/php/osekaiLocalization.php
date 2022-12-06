<?php
$locales = [
    'en_GB' => [
        'name' => 'English (UK)',
        'code' => 'en_GB',
        'short' => 'en',
        'flag' => "https://assets.ppy.sh/old-flags/GB.png",
    ],
    'de_DE' => [
        'name' => 'Deutsch',
        'code' => 'de_DE',
        'short' => 'de',
        'flag' => "https://assets.ppy.sh/old-flags/DE.png",
    ],
    'fr_FR' => [
        'name' => 'Français',
        "wip" => false,
        'code' => 'fr_FR',
        'short' => 'fr',
        'flag' => "https://assets.ppy.sh/old-flags/FR.png",
    ],
    'pt_BR' => [
        'name' => 'Português (Brasil)',
        'code' => 'pt_BR',
        'short' => 'pt',
        'flag' => "https://assets.ppy.sh/old-flags/BR.png",
    ],
    'nl_NL' => [
        'name' => 'Nederlands',
        "wip" => false,
        'code' => 'nl_NL',
        'short' => 'nl',
        'flag' => "https://assets.ppy.sh/old-flags/NL.png",
    ],
    "id_ID" => [
        "name" => "Indonesia",
        "code" => "id_ID",
        "short" => "id",
        "flag" => "https://assets.ppy.sh/old-flags/ID.png",
    ],
    "sk_SK" => [
        "name" => "Slovenčina",
        "code" => "sk_SK",
        "short" => "sk",
        "flag" => "https://assets.ppy.sh/old-flags/SK.png",
    ],
    /* "db_DB" => [
        "name" => "Debug",
        "code" => "db_DB",
        "short" => "db",
        "flag" => "https://assets.ppy.sh/old-flags/XX.png",
    ], */
    "hr_HR" => [
        "name" => "Hrvatski",
        "code" => "hr_HR",
        "short" => "hr",
        "flag" => "https://assets.ppy.sh/old-flags/HR.png",
    ],
    "pl_PL" => [
        "name" => "Polski",
        "code" => "pl_PL",
        "short" => "pl",
        "flag" => "https://assets.ppy.sh/old-flags/PL.png",
    ],
    "fi_FI" => [
        "name" => "Suomi",
        "code" => "fi_FI",
        "short" => "fi",
        "flag" => "https://assets.ppy.sh/old-flags/FI.png",
    ],
    'ru_RU' => [
        'name' => 'Русский',
        'code' => 'ru_RU',
        'short' => 'ru',
        'flag' => "https://assets.ppy.sh/old-flags/RU.png",
    ],
    'uk_UA' => [
        'name' => 'Українська',
        'code' => 'uk_UA',
        'short' => 'uk',
        'flag' => "https://assets.ppy.sh/old-flags/UA.png",
    ],
    "ko_KR" => [
        "name" => "한국어",
        "wip" => true,
        "code" => "ko_KR",
        "short" => "ko",
        "flag" => "https://assets.ppy.sh/old-flags/KR.png",
        "extra_html" => '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">',
        "extra_css" => 'body { font-family: "Comfortaa", "Noto Sans KR", sans-serif !important; }',
    ],
    "ja_JP" => [
        "name" => "日本語",
        "wip" => true,
        "code" => "ja_JP",
        "short" => "ja",
        "flag" => "https://assets.ppy.sh/old-flags/JP.png",
        "extra_html" => '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@100;300;400;500;700;800;900&display=swap" rel="stylesheet"> ',
        "extra_css" => 'body { font-family: "Comfortaa", "M PLUS Rounded 1c", sans-serif !important; }',
    ],
    /* chinese simplified */
    "zh_CN" => [
        "name" => "简体中文",
        "code" => "zh_CN",
        "short" => "zh",
        "flag" => "https://assets.ppy.sh/old-flags/CN.png"
    ],
    /* chinese traditional */
    "zh_TW" => [
        "name" => "繁體中文",
        "wip" => false,
        "experimental" => false,
        "code" => "zh_TW",
        "short" => "zh",
        "flag" => "https://assets.ppy.sh/old-flags/TW.png",
        "extra_html" => '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100;300;400;500;700&display=swap" rel="stylesheet"> ',
        "extra_css" => 'body { font-family: "Comfortaa", "Noto Sans TC", sans-serif !important; }',
    ],
    /* hebrew */
    "he_IL" => [
        "name" => "עברית",
        "wip" => true, // until RTL finished
        "experimental" => false,
        "code" => "he_IL",
        "short" => "he",
        "flag" => "https://assets.ppy.sh/old-flags/IL.png",
        // switch page direction when page finishes loading
        "rtl" => true,
        "extra_html" => '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@100;300;400;500;700;800;900&display=swap" rel="stylesheet"> ',
        "extra_css" => 'body { font-family: "Comfortaa", "M PLUS Rounded 1c", sans-serif !important; }',
    ],
    /* italian */
    "it_IT" => [
        "name" => "Italiano",
        "wip" => false,
        "experimental" => false,
        "code" => "it_IT",
        "short" => "it",
        "flag" => "https://assets.ppy.sh/old-flags/IT.png"
    ],
    /* swedish */
    "sv_SE" => [
        "name" => "Svenska",
        "wip" => false,
        "experimental" => false,
        "code" => "sv_SE",
        "short" => "sv",
        "flag" => "https://assets.ppy.sh/old-flags/SE.png"
    ],
    /* turkish */
    "tr_TR" => [
        "name" => "Türkçe",
        "wip" => false,
        "experimental" => false,
        "code" => "tr_TR",
        "short" => "tr",
        "flag" => "https://assets.ppy.sh/old-flags/TR.png"
    ],
    /* norwegian bokmal */
    "nb_NO" => [
        "name" => "Norsk bokmål",
        "wip" => false,
        "experimental" => false,
        "code" => "nb_NO",
        "short" => "nb",
        "flag" => "https://assets.ppy.sh/old-flags/NO.png"
    ],
    /* thai */
    "th_TH" => [
        "name" => "ไทย",
        "wip" => false,
        "code" => "th_TH",
        "short" => "th",
        "flag" => "https://assets.ppy.sh/old-flags/TH.png",
        "extra_html" => '<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kodchasan:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">',
        "extra_css" => 'body { font-family: "Comfortaa", "Kodchasan", sans-serif !important; }'
    ],
    /* spanish */
    "es_ES" => [
        "name" => "Español",
        "wip" => false,
        "code" => "es_ES",
        "short" => "es",
        "flag" => "https://assets.ppy.sh/old-flags/ES.png",
    ],
    "tl_PH" => [
        "name" => "Tagalog",
        "wip" => true,
        "code" => "tl_PH",
        "short" => "ph",
        "flag" => "https://assets.ppy.sh/old-flags/PH.png"
    ],
    /* bulgarian */
    "bg_BG" => [
        "name" => "български",
        "wip" => true,
        "experimental" => true,
        "code" => "bg_BG",
        "short" => "bg",
        "flag" => "https://assets.ppy.sh/old-flags/BG.png"
    ],
    /* portugese */
    "pt_PT" => [
        "name" => "Português",
        "wip" => true,
        "experimental" => true,
        "code" => "pt_PT",
        "short" => "pt",
        "flag" => "https://assets.ppy.sh/old-flags/PT.png"
    ],
    "vi_VN" => [
        "name" => "Tiếng Việt",
        "code" => "vi_VN",
        "short" => "vi",
        "flag" => "https://assets.ppy.sh/old-flags/VI.png"
    ]

];

function nameToEnglish($code)
{
    $name = Locale::getDisplayLanguage($code, 'en_GB');
    if ($code == "zh_TW") {
        $name = "Simplified " . $name;
    }
    if ($code == "zh_CN") {
        $name = "Simplified " . $name;
    }
    return $name;
}


// sort locales by name without removing keys
$locales = array_values($locales);
usort($locales, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});
// set english as first
function move_to_top($array, $key)
{
    $temp = array_splice($array, array_search($key, array_column($array, 'code')), 1);
    array_unshift($array, $temp[0]);
    return $array;
}
$locales = move_to_top($locales, 'en_GB');

$temp = [];
foreach ($locales as $locale) {
    $temp[$locale['code']] = $locale;
}
$locales = $temp;




$sourcesNames = ["report", "apps", "badges", "comments", "faq", "general", "home", "medals", "navbar", "profiles", "rankings", "snapshots", "donate", "contact", "misc/translators", "misc/groups", "misc/global"];
$sources = array();

$currentLocale = null;

$currentLocale = $locales['en_GB'];

$enabled = true;
if (isExperimental()) {
    $enabled = true;
}

// php set locale
setlocale(LC_ALL, $currentLocale['code']);

function setLocaleCookie($text)
{
    setcookie("locale", $text, 2147483647, "/");
    //echo "updated to " . $text . "<pre>";
    //var_dump(debug_backtrace());
    //echo "</pre>";
}

function getLocaleCookie()
{
    return $_COOKIE['locale'];
}

function setCurrentLocale($localeName, $save = true)
{
    global $currentLocale;
    global $locales;
    if (array_key_exists($localeName, $locales)) {
        $currentLocale = $locales[$localeName];
        //$_SESSION['locale'] = $localeName;
        if($save) {
            setLocaleCookie($localeName);
        }
    } else {
        //echo "specified locale does not exist";
        $currentLocale = $locales['en_GB'];
        //$_SESSION['locale'] = 'en_GB';
        setLocaleCookie("en_GB");
    }
    saveSession();
}

if (getLocaleCookie() == null) {
    // base on browser
    $browserLocale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $browserLocaleLong = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
    $browserLocaleLong = str_replace("-", "_", $browserLocaleLong);
    setCurrentLocale($browserLocaleLong);
} else {
    setCurrentLocale(getLocaleCookie(), false);
}

if ($useJS == true) {
    echo '<script id="localization">';
    echo 'var locales = ' . json_encode($locales) . ';';
    echo 'var sourcesNames = ' . json_encode($sourcesNames) . ';';
    echo 'var currentLocale = ' . json_encode($currentLocale) . ';';
    echo '</script>';

    echo '<script src="/global/js/localization.js?v=1.3"></script>';
}

function loadSource($source)
{
    global $currentLocale;
    global $sourcesNames;
    global $sources;
    global $enabled;
    // if not a source
    if (!in_array($source, $sourcesNames)) {
        return;
    }

    if ($enabled == true) {
        $location = "/global/lang/" . $currentLocale["code"] . "/" . $source . ".json";
    } else {
        // localization isnt released yet
        $location = "/global/lang/en_GB/" . $source . ".json";
    }
    $location = $_SERVER['DOCUMENT_ROOT'] . $location;
    $json = file_get_contents($location);
    $json = json_decode($json, true);
    $sources[$source] = $json;
}


function sourceLoaded($source)
{
    // if source loaded
    global $sources;
    if (isset($sources[$source])) {
        return true;
    } else {
        return false;
    }
}

function loadIfSourceNotLoaded($source)
{
    // if source loaded
    if (!sourceLoaded($source)) {
        loadSource($source);
    }
}

function GetStringRaw($source, $key, $variables = [])
{
    loadIfSourceNotLoaded($source);
    global $sources;
    if (isset($sources[$source][$key])) {
        $text = $sources[$source][$key];
    } else {
        $text = "__ {$source}.{$key} __";
    }
    $i = 1;
    foreach ($variables as $variable) {
        $text = str_replace("$" . $i, $variable, $text);
        $i++;
    }
    return $text;
}

function GetString($string, $variables = [])
{
    // string type: source.key (key can contain dots)
    $string = explode(".", $string);
    $source = $string[0];
    $key = implode(".", array_slice($string, 1));
    $text = GetStringRaw($source, $key, $variables);
    return $text;
}

function LocalizeText($string, $variables = [])
{
    // this text can include multiple strings, like this:
    // 50 ??source.key?? something something ??source.anotherkey??
    $strings = preg_match_all("/\?\?([^\?]+)\?\?/", $string, $matches);

    foreach ($matches[1] as $match) {
        $localizedString = GetString($match, $variables);
        $string = str_replace("??" . $match . "??", $localizedString, $string);
    }
    return $string;
}

if ($useJS == true) {
    if (isset($currentLocale['extra_html'])) {
        echo $currentLocale['extra_html'];
    }
    if (isset($currentLocale['extra_css'])) {
        echo '<style>' . $currentLocale['extra_css'] . '</style>';
    }
    if (isset($currentLocale['extra_js'])) {
        echo '<script>' . $currentLocale['extra_js'] . '</script>';
    }
    if (isset($currentLocale['rtl'])) {
        echo '<script>document.addEventListener("DOMContentLoaded", function() {
        //document.body.style.direction = "rtl";
        document.body.classList.add("rtl");
    });</script>';
    }
}
