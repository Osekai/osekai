<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#353d55">
    <meta name="theme-color" content="#353d55">
    <meta name="description" content="need to get in contact with us? this is the place for you." />
    <meta property="og:title" content="Osekai Contact" />
    <meta property="og:description" content="need to get in contact with us? this is the place for you." />
    <meta name="twitter:title" content="Osekai Contact" />
    <meta name="twitter:description" content="need to get in contact with us? this is the place for you." />
    <title name="title">Osekai Contact</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= ROOT_URL ?>/legal/licences" />

    <?php
    font();
    css();
    dropdown_system();
    //notification_system();
    //user_hover_system();
    //medal_hover_system();
    //tooltip_system();
    //comments_system();
    //report_system();

    $licences = [];
    function add($name = null, $link = null, $licence = null, $type = "licence")
    {
        global $licences;
        $licences[] = [
            "name" => $name,
            "link" => $link,
            "licence" => $licence,
            "type" => $type
        ];
    }
    add("Twemoji", "https://github.com/twitter/twemoji", "CC-BY-4.0");
    add("BBcode Parser", "https://github.com/Frug/js-bbcode-parser", "MIT");
    add("Colour Picker", "https://github.com/taufik-nurrohman/color-picker", "MIT");
    add("Picmo", "https://github.com/joeattardi/picmo/", "MIT");
    add("Popper.js", "https://popper.js.org/", "MIT");
    add("Tippy.js", "https://atomiks.github.io/tippyjs/", "MIT");
    add("Font Awesome", "https://fontawesome.com/", "CC BY 4.0 / SIL OFL 1.1 / MIT - see <a href=\"https://fontawesome.com/license/free\">https://fontawesome.com/license/free</a> for more info");
    add(null, null, null, "divider");
    add("Comfortaa", "https://fonts.google.com/specimen/Comfortaa", "OFL");
    add("Cabin", "https://fonts.google.com/specimen/Cabin", "OFL");
    add("Noto Sans TC", "https://fonts.google.com/noto/specimen/Noto+Sans+TC", "OFL");
    add("Noto Sans KR", "https://fonts.google.com/noto/specimen/Noto+Sans+KR", "OFL");
    add("M Plus Rounded 1c", "https://fonts.google.com/specimen/M+PLUS+Rounded+1c", "OFL");

    ?>
</head>

<style>
    .osekai__panel-inner {
        font-family: var(--body-font) !important;
    }
    .osekai__panel-inner h3 {
        font-size: 24px;
    }
    .osekai__panel-inner p {
        font-size: 18px;
    }
</style>

<body>
    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>Licence</p>
                    </div>
                    <div class="osekai__panel-inner">
                        <h3>Osekai's code is licenced under the MIT licence. For more info read the <a href="">LICENCE</a> file.</h3>
                        <p>Keep in mind that graphic assets such as the Osekai Logo and Wordmark are under the <strong>CC BY</strong> licence.</p>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>Third Party Licences</p>
                    </div>
                    <div class="osekai__panel-inner">
                        <?php
                        foreach ($licences as $l) {
                            if ($l['type'] == "divider") {
                                echo '<div class="osekai__divider"></div>';
                                continue;
                            }
                            echo "<p style=\"margin: 4px 0px;\"><a href=\"{$l['link']}\">{$l['name']}</a> ({$l['licence']})</p>";
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>