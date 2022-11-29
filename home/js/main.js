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
    var app = CleanedFAQ.find(function (app) {
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
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var data = JSON.parse(xhr.responseText);
            PopulateFAQ(data);
        }
    }
    xhr.send();
}

LoadFAQ();

function ScrollDown() {
    // scroll down 100vh smoothly
    window.scrollTo({
        top: window.innerHeight - 47,
        behavior: 'smooth',
    });

}

positionNav();

document.addEventListener("DOMContentLoaded", function () {
    // remove lottie translate3d element
    let elements = document.getElementsByTagName('lottie-player');

    for (var i = 0; i < elements.length; i++) {

        elements[i].addEventListener('play', (event) => {
            // console.log(event.target.shadowRoot.querySelector('svg').style.transform);
            event.target.shadowRoot.querySelector('svg').style.transform = '';
        });
        elements[i].play(); // trigger (again)

    }
});