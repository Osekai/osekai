// requires xhr

window.API_GetUser = function(uid, mode) {
    return new Promise((resolve) => {
        const xhr = createXHR("/global/api/osu_api.php");
        xhr.send("UserID=" + uid + "&Mode=" + mode + "&UseAllMedals=" + window.localStorage.getItem('profiles__showmedalsfromallmodes'));
        xhr.onreadystatechange = function() {
            const oResponse = getResponse(xhr);
            if(handleUndefined(oResponse)) return;
            return resolve(oResponse);
        };
    });
};
