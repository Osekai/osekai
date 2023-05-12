<?php
$loadApps = false;
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
ini_set('display_errors', 0);
error_reporting(E_ERROR);

$apps = (array)Database::execSimpleSelect("SELECT * FROM Apps");

$allowExperimental = false;
if(isExperimental())
{
    $allowExperimental = true;
}
if($allowExperimental = false)
{
    foreach($apps as $app)
    {
        if($app['experimental'] == 1)
        {
            $allowExperimental = true;
            break;
        }
    }
}
// remove hidden ones
$apps = array_filter($apps, function($app)
    {
        return $app['visible'] == 1;
    });

for($x = 0; $x > count($apps); $x++)
{
    $apps[$x]['questions'] = [];
}

$faq = Database::execSimpleSelect("SELECT * FROM FAQ");
foreach($faq as $question)
{
    for($x = 0; $x < count($apps); $x++)
    {
        if(isset($apps[$x]) && $apps[$x]['id'] == $question['App'])
        {
            $apps[$x]['questions'][] = $question;
        }
    }
}

$apps = array_values($apps);

ini_set('display_errors', 0);

$useJS = false;
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osekaiLocalization.php");

loadSource("faq");

for($x = 0; $x < count($apps); $x++)
{
    if(!isset($apps[$x]['questions']))
    {
        $apps[$x]['questions'] = [];
    }
    for($y = 0; $y < count($apps[$x]['questions']); $y++)
    {
        if(isset($apps[$x]['questions'][$y]['LocalizationPrefix']))
        {
            //echo "getting string for " . $apps[$x]['questions'][$y]['LocalizationPrefix'] . " " . $apps[$x]['questions'][$y]['Title'] . "\n";
            $apps[$x]['questions'][$y]['Title'] = GetStringRaw("faq", $apps[$x]['simplename'] . "." . $apps[$x]['questions'][$y]['LocalizationPrefix'] . ".question");
            $apps[$x]['questions'][$y]['Content'] = GetStringRaw("faq", $apps[$x]['simplename'] . "." . $apps[$x]['questions'][$y]['LocalizationPrefix'] . ".answer");
            // if the question contains __ then we need to use the other method instead
            if(strpos($apps[$x]['questions'][$y]['Title'], "__") !== false)
            {
                $apps[$x]['questions'][$y]['Title'] = GetStringRaw("faq", $apps[$x]['simplename'] . "." . $apps[$x]['questions'][$y]['LocalizationPrefix']);
            }

            $apps[$x]['questions'][$y]['Content'] = LocalizeText($apps[$x]['questions'][$y]['Content']);
            //echo $apps[$x]['questions'][$y]['Title'] . " " . $apps[$x]['questions'][$y]['Answer'] . "<br><br>";
        }
    }
}

echo json_encode($apps);