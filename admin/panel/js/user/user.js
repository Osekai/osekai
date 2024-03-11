let user;
let userdata;

const params = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

if (params.user != null) {
    user = params.user;
    getUser();
}

function getUser() {
    var xhr = createXHR("/admin/panel/api/users/user");
    xhr.send(`user=${user}`)
    xhr.onload = function () {
        userdata = JSON.parse(xhr.responseText);
        setUI();
    }
}

function setUI() {
    console.log(userdata);
    document.querySelectorAll("[selector='banner']").forEach((item) => {
        item.src = userdata['info']['cover']['custom_url'];
    });
    document.querySelectorAll("[selector='pfp']").forEach((item) => {
        item.src = userdata['info']['avatar_url'];
    });
    document.querySelectorAll("[selector='username']").forEach((item) => {
        item.innerHTML = userdata.info.username + " <span>" + userdata.info.id + "</span>";
    });

    document.getElementById("commentCount").innerText = userdata.comments.length;
    document.getElementById("beatmapCount").innerText = userdata.beatmaps.length;
    document.getElementById("submissionCount").innerText = userdata.submissions.length;
    document.getElementById("versionCount").innerText = userdata.versions.length;

    document.getElementById("lastLogin").innerHTML = `Last logged in <strong>${userdata.other_info.last_logon_date}</strong> <light>(${TimeAgo.inWords(new Date(userdata.other_info.last_logon_date).getTime())})</light>`;
}