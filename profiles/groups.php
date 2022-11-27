<?php
$app = "profiles";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />



<head>

    <?php
    if (isset($_GET['user'])) {
        //$colBadges = Database::execSelect("SELECT * FROM Badges where id = ?", "i", array($_GET['badge']));
        //include("../global/php/osu_api_functions.php");
        // we can cache this like forever
        $cache = Caching::getCache("profiles_meta_" . $_GET['user']);
        $user = "";
        if ($cache != null) {
            $user = $cache;
        } else {
            $user = v2_getUser($_GET['user']);
            Caching::saveCache("profiles_meta_" . $_GET['user'], 172800, $user);
        }
        $user = json_decode($user, true);

        if (isset($user)) {
            $user_id = $user['id'];
            $user_name = $user['username'];

            $title = "Osekai Profiles • " . $user_name;
            $desc = "Check out the Osekai Profiles page for " . $user_name . "! Including stats, medals, goals, timeline, and more!";
            $keyword = $user_name;
            $keyword2 = "osekai profiles";

            $meta = '<meta charset="utf-8" />
            <meta name="msapplication-TileColor" content="#303f5e">
            <meta name="theme-color" content="#303f5e">
            <meta property="og:image" content="https://a.ppy.sh/' . $user_id . '" />
            <meta name="description" content="' . htmlspecialchars($desc) . '" />
            <meta property="og:title" content="' . htmlspecialchars($title) . '" />
            <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
            <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
            <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
            <title name="title">' . htmlspecialchars($title) . '</title>
            <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,profile,user_profile,' . $keyword . ',' . $keyword2 . ',graph,chart,goals">';
        }
    } else {
        $title = "Osekai Profiles • Home";
        // ! temporary description
        $desc = "Check out Osekai Profiles! Featuring stats, medals, goals, timeline, and more, for every single osu! user!";

        $meta = '<meta charset="utf-8" />
        <meta name="description" content="' . htmlspecialchars($desc) . '" />
        <meta name="msapplication-TileColor" content="#303f5e">
        <meta name="theme-color" content="#303f5e">
        <meta property="og:title" content="' . htmlspecialchars($title) . '" />
        <meta property="og:description" content="' . htmlspecialchars($desc) . '" />
        <meta name="twitter:title" content="' . htmlspecialchars($title) . '" />
        <meta name="twitter:description" content="' . htmlspecialchars($desc) . '" />
        <title name="title">' . htmlspecialchars($title) . '</title>
        <meta name="keywords" content="osekai,osu,osu!,osu!game,osugame,game,video game,profile,user_profile,badges,graph,chart,goals">
        <meta property="og:url" content="/profiles" />';
    }
    echo $meta;
    echo $head;

    font();
    css();
    dropdown_system();
    mobileManager();
    osu_api();
    fontawesome();

    tippy();
    ?>
</head>

<body>
    <?php navbar(); ?>
    <div class="osekai__panel-container">
        <?php
        if(isset($_GET['group'])) {
            $group = Database::execSelect("SELECT * FROM Groups WHERE Id = ?", "i", [$_GET['group']]);
            $users = Database::execSelect("SELECT GA.*, R.name FROM
            GroupAssignments GA LEFT JOIN
            Ranking R ON R.id = GA.UserId
            WHERE GroupId = ?", "i", [$_GET['group']]);


            ?>
                <pre>
                    <?php     print_r($group);
print_r($users) ?>
                </pre>
            <?php
        }else {
            $groups = Database::execSimpleSelect("SELECT Groups.*, COUNT(GA.GroupId) AS Count
            FROM
                Groups Groups LEFT JOIN
                GroupAssignments GA ON GA.GroupId = Groups.ID
            WHERE Groups.Hidden = 0
            GROUP BY Groups.Id");
            ?>
<section class="osekai__panel">
            <div class="osekai__panel-header">
                <p>Groups</p>
            </div>
            <div class="osekai__panel-inner">
                <?php
                foreach($groups as $group) {
                    ?>
                    <a class="profiles__groups__group" href="/profiles/groups?group=<?= $group['Id'] ?>">
                        <h1><?= $group['Name'] ?> <span>(<?= $group['ShortName'] ?>)</span></h1>
                        <p><strong><?= $group['Count'] ?></strong> Members</p>
                </a>
                    <?php
                }
                ?>
            </div>
        </section>
            <?php
        }
        ?>
    </div>
    <script type="text/javascript" src="./js/functions.js?vx=<?php echo OSEKAI_VERSION; ?>"></script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>
<!-- woo -->