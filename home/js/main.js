// pull faq from /home/api/faq.php
var CleanedFAQ = [];
var CurrentActiveButton = null;

function GenerateHeader(app) {
    var header = document.createElement('h1');
    header.innerHTML = app.name;
    header.className = 'home__faq-appheader';
    return header.outerHTML;
}

function GenerateQuestionButton(app, index) {
    var question = document.createElement('p');
    question.innerHTML = app.questions[index].Title;
    question.className = 'home__faq-question';
    question.id = app.simplename + '_' + index;
    question.setAttribute('onclick', 'ShowQuestion("' + app.simplename + '",' + index + ')');
    return question.outerHTML;
}

function PopulateFAQ(data) {
    var html = "";
    var cleanedFAQ = [];
    for (var i = 0; i < data.length; i++) {
        if (data[i].questions != null) {
            if (data[i].questions.length > 0) {
                cleanedFAQ.push(data[i]);
                var app = data[i];
                html += "<div class='home__faq-section'>";
                html += GenerateHeader(app);
                html += "<div class='home__faq-section-content'>";
                for (var j = 0; j < app.questions.length; j++) {
                    html += GenerateQuestionButton(app, j);
                }
                html += "</div>";
                html += "</div>";
            }
        }
    }
    CleanedFAQ = cleanedFAQ;
    document.getElementById('home__faq-list').innerHTML = html;
}

function ShowQuestion(app_simplename, index) {
    var app = CleanedFAQ.find(function(app) {
        return app.simplename == app_simplename;
    });
    var question = app.questions[index];
    console.log(question);
    // mark button as active
    if (CurrentActiveButton != null) {
        CurrentActiveButton.className = "home__faq-question";
    }
    CurrentActiveButton = document.getElementById(app.simplename + '_' + index);
    CurrentActiveButton.className = "home__faq-question home__faq-question-active";
    // TODO: show question in the right place. for now the ui is unfinished, so we can't do that just yet.
    document.getElementById('home__faq-answer-title').innerHTML = question.Title;
    // the content needs to support HTML, such as tables or images
    document.getElementById('home__faq-answer-answer').innerHTML = question.Content;
}

function LoadFAQ() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/home/api/faq.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var data = JSON.parse(xhr.responseText);
            PopulateFAQ(data);
        }
    }
    xhr.send();
}

LoadFAQ();

function LoadTeam() {
    var SocialIcons = {
        "Twitter": "fab fa-twitter",
        "Mastodon": "fab fa-mastodon",
        "Twitch": "fab fa-twitch",
        "Youtube": "fab fa-youtube",
        "Github": "fab fa-github",
        "Discord": "fab fa-discord",
        "Website": "fas fa-globe",
        "Speedrun.com": "fas fa-trophy",
        "osu! Profile": "oif-osu-logo",
        "Osekai Profiles": "oif-app-profiles",
    };

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/home/api/team.php', true);
    xhr.onreadystatechange = async function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var TeamMembers = JSON.parse(xhr.responseText);

            var grid = document.getElementById("team_grid");
            for (member of TeamMembers) {
                var element = document.createElement("div");
                element.classList.add("home__team-member");

                var blurBackground = document.createElement("img");
                blurBackground.src = `https://a.ppy.sh/${member.id}`
                blurBackground.classList.add("osekai__pfp-blur-bg");

                var memberInfo = document.createElement("div");
                memberInfo.classList.add("home__team-member-info");
                memberInfo.appendChild(memberInfoInner = document.createElement("div"));
                memberInfoInner.classList.add("home__team-member-info-inner");
                memberInfoInner.appendChild(memberPfp = document.createElement("img"))
                memberPfp.src = `https://a.ppy.sh/${member.id}`

                memberInfoInner.appendChild(memberInfoTexts = document.createElement("div"))
                memberInfoTexts.classList.add("home__team-member-info-texts");

                memberInfoTexts.appendChild(memberInfoTexts_Name = document.createElement("div"))
                memberInfoTexts_Name.classList.add("home__team-member-info-texts-name");
                memberInfoTexts_Name.appendChild(memberInfoTexts_Name_p = document.createElement("p"))
                memberInfoTexts_Name_p.innerHTML = member.name;
                memberInfoTexts_Name.appendChild(memberInfoTexts_Name_Badges = document.createElement("div"))
                memberInfoTexts_Name_Badges.innerHTML = groupUtils.badgeHtmlFromArray(member.groups);
                memberInfoTexts_Name_Badges.classList.add("home__team-member-info-texts-badges");

                if (member.name_alt != null) {
                    memberInfoTexts.appendChild(memberInfoTexts_NameAlt = document.createElement("small"))
                    memberInfoTexts_NameAlt.innerHTML = await GetStringRaw("home", "team.alsoKnownAs", [member.name_alt]);
                }

                memberInfoTexts.appendChild(memberInfoTexts_Role = document.createElement("p"))
                memberInfoTexts_Role.innerHTML = await LocalizeText(member.role);

                var memberSocials = document.createElement("div");
                memberSocials.classList.add("home__team-member-socials");
                memberSocials.appendChild(memberSocialsInner = document.createElement("div"));
                memberSocialsInner.classList.add("home__team-member-socials-inner");

                function addSocial(social) {
                    var socialEl = document.createElement("a");
                    socialEl.classList.add("home__team-member-social");
                    socialEl.classList.add("tooltip-v2");
                    socialEl.setAttribute("tooltip-content", social.name);
                    socialEl.href = social.link;

                    var icon = document.createElement("i");
                    icon.className = SocialIcons[social.name];
                    socialEl.appendChild(icon);

                    memberSocialsInner.appendChild(socialEl);
                }

                addSocial({
                    "name": "osu! Profile",
                    "link": "https://osu.ppy.sh/users/" + member.id,
                });
                addSocial({
                    "name": "Oseaki Profiles",
                    "link": "/profiles?user=" + member.id,
                });

                for (social of member.socials) {
                    addSocial(social);
                }

                element.appendChild(blurBackground);
                element.appendChild(memberInfo);
                element.appendChild(memberSocials);

                grid.appendChild(element);
            }
        }
    }
    xhr.send();
}

LoadTeam();

function ScrollDown() {
    // scroll down 100vh smoothly
    window.scrollTo({
        top: window.innerHeight - 47,
        behavior: 'smooth',
    });

}

positionNav();

document.addEventListener("DOMContentLoaded", function() {
    // remove lottie translate3d element
    let elements = document.getElementsByTagName('lottie-player');

    for (var i = 0; i < elements.length; i++) {

        elements[i].addEventListener('play', (event) => {
            // console.log(event.target.shadowRoot.querySelector('svg').style.transform);
            event.target.shadowRoot.querySelector('svg').style.transform = '';
        });

    }
});