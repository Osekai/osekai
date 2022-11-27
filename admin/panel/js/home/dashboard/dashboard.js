var quotes = [
    "pettan pettan tsurupettan... or something like that",
    "welcome to hell",
    "moderation time!",
    '🤔',
    "If you can see this, then adjust your monitor so you can't in order to follow the instructions to fix it.",
    "A funny quote",
    "\"use fortune\" he said. zsh: command not found: fortune",
    "727 WHEN YOU SEE IT OMG WYSI WYSI WYSI",
    "Introducing: Beatmap Spotlights (Season 2)",
    "medals :D",
    "===≠==",
    "is this just fantasy?"
]


document.getElementById("dashboard__quote").innerHTML = quotes[Math.floor(Math.random() * quotes.length)];


var image = images[Math.floor(Math.random() * images.length)];
console.log(image);
document.getElementById("dashboard__image").src = "/admin/panel/fun/images/" + image['path'];
document.getElementById("dashboard__image-text").innerHTML = image['caption'] + "<light> - " + image['by'] + "</light>";