async function getUser(uid, mode = "all", medals = true) {
    return new Promise(resolve => {
        var xhr = createXHR("/global/api/osu_api.php");
        xhr.send("UserID=" + uid + "&Mode=" + mode + "&UseAllMedals=" + medals);
        xhr.onreadystatechange = function () {
            var oResponse = getResponse(xhr);
            if (handleUndefined(oResponse)) return;
            return resolve(JSON.parse(oResponse));
        };
    });
}


async function doStuff(uid1, uid2) {
    let users = await Promise.all([getUser(uid1), getUser(uid2)]);
    let u1 = users[0];
    let u2 = users[1];

    //clear out the things
    document.getElementById("bothhave").textContent = '';
    document.getElementById("u1has").textContent = '';
    document.getElementById("u2has").textContent = '';

    // thanks stackoverflow
    // A comparer used to determine if two entries are equal.
    const isSameMedal = (a, b) => a.achievement_id === b.achievement_id;

    // Get items that only occur in the left array,
    // using the compareFunction to determine equality.
    const onlyInLeft = (left, right, compareFunction) =>
        left.filter(leftValue =>
            !right.some(rightValue =>
                compareFunction(leftValue, rightValue)));

    const bothFilter = (left, right, compareFunction) =>
        left.filter(leftValue =>
            right.some(rightValue =>
                compareFunction(leftValue, rightValue)));

    const onlyInU1 = onlyInLeft(u1.user_achievements, u2.user_achievements, isSameMedal);
    for (let i = 0; i < onlyInU1.length; i++) {
        let item = document.createElement('li');
        item.textContent = onlyInU1[i].name
        document.getElementById('u1has').appendChild(item);
    }
    const onlyInU2 = onlyInLeft(u2.user_achievements, u1.user_achievements, isSameMedal);
    for (let i = 0; i < onlyInU2.length; i++) {
        let item = document.createElement('li');
        item.textContent = onlyInU2[i].name
        document.getElementById('u2has').appendChild(item);
    }

    const bothMedals = bothFilter(u1.user_achievements, u2.user_achievements, isSameMedal);
    for (let i = 0; i < bothMedals.length; i++) {
        let item = document.createElement('li');
        item.textContent = bothMedals[i].name
        document.getElementById('bothhave').appendChild(item);
    }
}

document.getElementById("thebuttonomg").addEventListener("click", buttonClicked);

//the button got clicked omg
function buttonClicked() {
    let uid1 = document.getElementById('uid1').value;
    let uid2 = document.getElementById('uid2').value;
    document.getElementById("bothhave").textContent = 'Loading...';
    document.getElementById("u1has").textContent = 'Loading...';
    document.getElementById("u2has").textContent = 'Loading...';


    document.getElementById("onlyuid1has").textContent = `Only ${uid1} has:`;
    document.getElementById("onlyuid2has").textContent = `Only ${uid2} has:`;

    doStuff(uid1, uid2)
}