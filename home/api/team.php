<?php
$loadApps = false;
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
ini_set('display_errors', 0);
error_reporting(E_ERROR);

$team = [];

function addMember($id, $name, $role, $aka, $socials)
{
    global $team;
    $team[] = [
        "id" => $id,
        "name" => $name,
        "name_alt" => $aka,
        "role" => $role,
        "socials" => $socials
    ];
}

$extraHtml = [
    "Mastodon" => "rel=\"me\""
];


function social($link, $name)
{
    return [
        "name" => $name,
        "link" => $link
    ];
}

function cleanTeam()
{
    global $team;
    global $extraHtml;
    $assignedGroups = Database::execSimpleSelect("SELECT * FROM GroupAssignments");
    for ($x = 0; $x < count($team); $x++) {
        $groups = [];
        foreach ($assignedGroups as $group) {
            if ($group['UserId'] == $team[$x]['id']) {
                $groups[] = (int)$group['GroupId'];
            }
        }
        $team[$x]['groups'] = $groups;
    }
}

// -----------------------------

addMember(
    1309242,
    "mulraf",
    "??home.team.role.mulraf??",
    null,
    [social("https://youtube.com/mulraf", "Youtube")]
);
addMember(
    10379965,
    "Tanza3D",
    "??home.team.role.tanza??",
    "Hubz",
    [
        social("https://twitter.com/Tanza3D", "Twitter"),
        social("https://twitch.tv/tanza_live", "Twitch"),
        social("https://www.hubza.co.uk/", "Website"),
        social("https://social.untone.uk/@tanza", "Mastodon")
    ]
);
addMember(
    18152711,
    "MegaMix_Craft",
    "??home.team.role.megamix??",
    "minusQuantumNeko",
    [
        social("https://discord.com/users/494883957117288448", "Discord"),
        social("https://www.youtube.com/MegaMix_Craft", "Youtube"),
        social("https://twitter.com/MegaMix_Craft", "Twitter"),
        social("https://github.com/minusQuantumNeko/", "Github"),
        social("https://www.speedrun.com/user/MegaMix_Craft", "Speedrun.com"),
        social("https://twitch.tv/megamix_craft", "Twitch"),
        social("https://social.untone.uk/@MegaMix_Craft", "Mastodon")
    ]
);
addMember(
    9350342,
    "EXtremeExploit",
    "??home.team.role.generic.developer??",
    "Pedrito",
    [
        social("https://github.com/EXtremeExploit/", "Github"),
        social("https://www.twitch.tv/extremeexploit_", "Twitch"),
        social("https://pedro.moe/", "Website")
    ]
);
addMember(
    7197172,
    "jiniux",
    "??home.team.role.generic.developer??",
    "AlexS4v",
    [
        social("https://github.com/jiniux/", "Github")
    ]
);
addMember(
    7279762,
    "Coppertine",
    "??home.team.role.coppertine??",
    null,
    [
        social("https://twitter.com/shuffler2001", "Twitter"),
        social("https://www.twitch.tv/coppertine", "Twitch"),
        social("https://www.artstation.com/coppertine", "Website")
    ]
);
addMember(
    14125695,
    "TheEggo",
    "??home.team.role.generic.snapshotsDeveloper??",
    null,
    []
);
addMember(
    2211396,
    "Badewanne3",
    "??home.team.role.generic.rankingsDataEngineer??",
    null,
    [social("https://github.com/MaxOhn", "Github")]
);
addMember(
    3357640,
    "Electroyan",
    "??home.team.role.generic.rankingsDataEngineer??",
    null,
    []
);
addMember(
    10238680,
    "chromb",
    "??home.team.role.generic.communityManager??",
    null,
    [
        social("https://www.youtube.com/channel/UCq37paEnfI4pmwE5j3rO5eg", "Youtube"),
        social("https://twitch.tv/chr0mb", "Twitch")
    ]
);
addMember(
    1699875,
    "Remyria",
    "??home.team.role.generic.communityManager??",
    null,
    [
        social("https://www.youtube.com/@Remyria", "Youtube"),
        social("https://twitch.tv/remyria", "Twitch"),
        social("https://www.speedrun.com/user/Remyria", "Speedrun.com")
    ]
);
addMember(
    13175102,
    "bentokage",
    "??home.team.role.generic.modderator??",
    null,
    [
        social("https://www.twitter.com/bentokage", "Twitter"),
        social("https://www.twitch.tv/bentokage", "Twitch"),
    ]
);
addMember(
    16487835,
    "ILuvSkins",
    "??home.team.role.generic.snapshotsManager??",
    null,
    []
);
addMember(
    26544843,
    "Mono x Mellow",
    "??home.team.role.monoxmellow??",
    null,
    [
        social("https://twitter.com/Mono_x_Mellow", "Twitter"),
        social("https://www.youtube.com/channel/UC7ZW8exqZ1c6eOhyajMnyZg", "Youtube"),
        social("https://www.twitch.tv/monoxmellow", "Twitch"),
        social("https://discord.com/users/194863870559387648", "Discord")
    ]
);

cleanTeam();
header("Content-Type: application/json");
echo json_encode($team);
