//requires xhr

window.API_GetUser = function(uid, mode) {
    return new Promise(resolve => {
        var xhr = createXHR("/global/api/osu_api.php");
        xhr.send("UserID=" + uid + "&Mode=" + mode + "&UseAllMedals=" + window.localStorage.getItem('settings_profiles__showmedalsfromallmodes'));
        xhr.onreadystatechange = function() {
            var oResponse = getResponse(xhr);
            if(handleUndefined(oResponse)) return;
            return resolve(oResponse);
        };
    });
}