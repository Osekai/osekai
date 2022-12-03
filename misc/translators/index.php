<?php
// osekai home [dev]
// this dev page is for testing controls, in prod it hsould redirect to /home
// /home has actual home content on it
// read the html to see what i mean i guess

$app = "home";
$app_extra = "other";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<meta name="msapplication-TileColor" content="#353d55">
<meta name="theme-color" content="#353d55">
<meta name="description" content="Osekai • other / translators" />
<meta property="og:title" content="Osekai • other / translators" />
<meta property="og:description" content="everyone who've dedicated their time to help translate Osekai into their native language!" />
<meta name="twitter:title" content="Osekai • other / translators" />
<meta name="twitter:description" content="everyone who've dedicated their time to help translate Osekai into their native language!" />
<title name="title">Osekai • other / translators</title>
<meta name="keywords" content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="<?= ROOT_URL ?>" />

<?php
font();
css();
dropdown_system();
mobileManager();
?>

<head>
    <meta charset="utf-8">

    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="" />
    <meta property=“og:description“ content="" />
    <meta name="twitter:title" content="" />
    <meta name="twitter:description" content="" />
    <title></title>
</head>

<body>
    <?php navbar(); 
    
    $allTranslators = Database::execSimpleSelect("SELECT Translators.*, Ranking.name as OsuUsername FROM Translators LEFT JOIN Ranking ON Ranking.id = Translators.Id");
    ?>
    <div class="osekai__panel-container misc__container">
        <div class="misc__header">
            <div class="misc__header-inner">
                <p>other / </p>
                <h1>translators</h1>
            </div>
        </div>
        <div class="misc__panel-container">
            <div class="misc__explainer">
                <p>So many people have helped to translate Osekai into their native
                    language, and allow people from all over the world to use the
                    site much easier than ever before. To be specific, <mark><strong><?php echo count($allTranslators); ?></strong> people</mark> have helped! 
                    And we appreciate every single one of the translators who have helped to make this a reality, from the people
                    who just did a few little strings, to the people who did their entire language in just a few days!</p>
                <p>It is insane how many people have helped, so this page is to credit each and every single person which has helped to translate
                    Osekai. And again, thanks to everyone who helped withe the translations.</p>
                <p>Oh, one last thing. The translators probably translated this string too. How interesting! Thanks to our translators for doing that, too!</p>
                <h3>- the Osekai team</h3>
            </div>
            <div class="osekai__panel">
                <div class="osekai__panel-header">Translators</div>
                <div class="osekai__panel-inner translators__list">
                    <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 0);
                    ini_set('display_startup_errors', 0);

                    for($x = 0; $x < count($allTranslators); $x++)
                    {
                        if($allTranslators[$x]['Id'] != 0 && ($allTranslators[$x]['OsuUsername'] == null || $allTranslators[$x]['OsuUsername'] == ""))
                        {
                            // Translator has osu acc but its not in the rankings
                            // Fetch its username and put it in the OsuUsername field so we use that
                            $allTranslators[$x]['OsuUsername'] = "";
                            $data = json_decode(v2_getUser($allTranslators[$x]['Id'], "osu", false, false), true);
                            $allTranslators[$x]['OsuUsername'] = $data['username'];
                            // Add the translators osu acc into members so it gets processed in the next batch
                            Database::execOperation("INSERT INTO Members (id) VALUES (?) ON DUPLICATE KEY UPDATE id = id", "i", [$allTranslators[$x]['Id']]);
                        }
                    }


                    foreach ($locales as $lang) {
                        //print_r($lang);
                        $translators = 0;

                        foreach($allTranslators as $translator)
                        {
                            if($translator['LanguageCode'] == $lang['code'])
                            {
                                $translators++;
                            }
                        }
                        if($translators == 0) { continue; }

                        //echo "<br>";
                        echo '<div class="translators__language">
                                <div class="translators__language-header">
                                    <div class="translators__language-header-flag">
                                        <img src="' . $lang['flag'] . '">
                                            <div class="translators__language-header-flag-amount">
                                            <h1><strong>' . $translators . '</strong><br>translators</h1>
                                    </div>
                                </div>
                            <div class="translators__language-header-texts">
                                <h1>' . nameToEnglish($lang['code']) . '</h1>
                                <h2>' . $lang['name'] . '</h2>
                            </div>
                        </div>';

                        echo '<div class="translators__language-grid">';
                        foreach($allTranslators as $translator)
                        {
                            if($translator['LanguageCode'] != $lang['code']) continue;
                            echo '<div class="translators__language-translator">';
                                    echo '<img src="';
                                    echo  $translator['Id'] != 0 ? 'https://a.ppy.sh/' . $translator['Id'] : 'https://osu.ppy.sh/assets/images/avatar-guest.8a2df920.png';
                                    echo '">';
                                    echo '<div class="translators__language-translator-texts">';
                                        echo '<p>' . $translator['Role'] . '</p>';
                                        echo '<h1>';
                                        echo $translator['OsuUsername'] ? $translator['OsuUsername'] : $translator['Username'];
                                        echo  '</h1>';
                                    echo '</div>
                                </div>';
                        }
                        echo '</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>