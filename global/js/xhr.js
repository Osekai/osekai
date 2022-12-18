window.createXHR = function (strUrl) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", strUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    return xhr;
};

window.getResponse = function (xhr) {
    if (xhr.readyState === 4 && xhr.status === 200) return JSON.parse(xhr.responseText);
};

window.handleUndefined = function (obj) {
    if (obj === undefined) return true; // return undefined for undefined
    if (obj === null) return true; // null unchanged
    return false;
};

if (typeof GetNotifications !== "undefined") {
    GetNotifications(false, true);
}
