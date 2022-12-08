<?php
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

$nameMapping = [
    "Twitter" => "fab fa-twitter",
    "Mastodon" => "fab fa-mastodon",
    "Twitch" => "fab fa-twitch",
    "Youtube" => "fab fa-youtube",
    "Github" => "fab fa-github",
    "Discord" => "fab fa-discord",
    "Website" => "fas fa-globe",
    "Speedrun.com" => "fas fa-trophy",
];

$extraHtml = [
    "Mastodon" => "rel=\"me\""
];


function social($link, $name, $icon = null)
{
    global $nameMapping;
    if ($icon == null && $nameMapping[$name] != null) {
        $icon = $nameMapping[$name];
    }
    return [
        "name" => $name,
        "link" => $link,
        "icon" => $icon
    ];
}

function printTeam()
{
    global $team;
    global $extraHtml;
    $assignedGroups = Database::execSimpleSelect("SELECT * FROM GroupAssignments");
    foreach ($team as $member) {
        $groups = [];
        foreach ($assignedGroups as $group) {
            if ($group['UserId'] == $member['id']) {
                $groups[] = getGroupFromId($group['GroupId']);
            }
        }
        $socialHtml = '';
        $badgeHtml = '';
        $groups = orderBadgeArray($groups);
        foreach($groups as $group) {
            $badgeHtml .= badgeHtmlFromGroup($group, "small");
        }
        foreach($member['socials'] as $social) {
            $socialHtml .= '<a class="home__team-member-social tooltip-v2" href="'.$social['link'].'" tooltip-content="'.$social['name'].'" '.$extraHtml[$social['name']].'>
            <i class="'.$social['icon'].'" aria-hidden="true"></i>
        </a>';
        }
        // TODO: somehow use user cover for the background instead of pfp. didn't wanna setup api stuff
        echo '<div class="home__team-member">
        <img class="osekai__pfp-blur-bg" src="https://a.ppy.sh/' . $member['id'] . '">
        <div class="home__team-member-info">
            <div class="home__team-member-info-inner">
                <img src="https://a.ppy.sh/' . $member['id'] . '">
                <div class="home__team-member-info-texts">
                    <div class="home__team-member-info-texts-name">
                        <p>' . $member['name'] . '</p>
                        <div class="home__team-member-info-texts-badges">'.$badgeHtml.'</div>
                    </div>';

        if($member['name_alt'] != null) {
            echo '<small>also known as <strong>'.$member['name_alt'].'</strong></small>';
        }
        
        echo '<p>' . $member['role'] . '</p>
                </div>
            </div>
        </div>
        <div class="home__team-member-socials">
            <div class="home__team-member-socials-inner">
                <a class="home__team-member-social tooltip-v2" tooltip-content="osu! Profile" href="https://osu.ppy.sh/users/' . $member['id'] . '">
                    <i class="oif-osu-logo" aria-hidden="true"></i>
                </a>
                <a class="home__team-member-social tooltip-v2" tooltip-content="Osekai Profiles" href="/profiles/?user=' . $member['id'] . '">
                    <i class="oif-app-profiles" aria-hidden="true"></i>
                </a>
                ' . $socialHtml . '
            </div>
        </div>
    </div>';
    }
}
