/// EXTERANL VARIABLES

// var locales = list of available locales
// var currentLocale = current locale
// var sourcesNames = json names for lang files


var sources = [];

//function loadSource($source)
//{
//    global $currentLocale;
//    global $sourcesNames;
//    global $sources;
//    // if not a source
//    if (!in_array($source, $sourcesNames)) {
//        return;
//    }
//
//    $location = "global/lang/" . $currentLocale["code"] . "/" . $source . ".json";
//    $location = $_SERVER['DOCUMENT_ROOT'] . $location;
//    $json = file_get_contents($location);
//    $json = json_decode($json, true);
//    $sources[$source] = $json;
//}

async function loadSource(source, locale = currentLocale) {
    // javascirpt
    if (!sourcesNames.includes(source) || source == "db_DB") {
        return;
    }

    sources[source] = {};

    var location = "/global/lang/" + currentLocale["code"] + "/" + source + ".json?v=" + version;

    var xhr = new XMLHttpRequest();
    var promise = new Promise(function(resolve, reject) {
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    sources[source] = JSON.parse(xhr.responseText);
                    resolve(xhr.responseText);
                } else {
                    sources[source] = {};
                    resolve(xhr.status);
                }
            }
        }
    });
    xhr.open("GET", location, true);
    xhr.send();

    return promise;
}

//function sourceLoaded($source) {
//    // if source loaded
//    global $sources;
//    if (isset($sources[$source])) {
//        return true;
//    } else {
//        return false;
//    }
//}

function sourceLoaded(source) {
    // if source loaded
    if (sources[source]) {
        return true;
    } else {
        return false;
    }
}

// function loadIfSourceNotLoaded($source) {
//     // if source loaded
//     if (!sourceLoaded($source)) {
//         loadSource($source);
//     }
// }

async function loadIfSourceNotLoaded(source) {
    // if the source is not loaded, load it and return the promise
    // otherwise, return a new promise that is already resolved
    if (!sourceLoaded(source)) {
        console.log("Loading " + source + "...");
        await loadSource(source);
    } else {
        return new Promise(function(resolve, reject) {
            resolve();
        });
    }
}

//`function GetStringRaw($source, $key, $variables = []) {
//    loadIfSourceNotLoaded($source);
//    global $sources;
//    if (isset($sources[$source][$key])) {
//        $text = $sources[$source][$key];
//    } else {
//        $text = "{$source}.{$key}";
//    }
//    $i = 0;
//    foreach ($variables as $variable) {
//        $text = str_replace("$" . $i, $variable, $text);
//        $i++;
//    }
//    return $text;
//}

async function GetStringRaw(source, key, variables = []) {
    await loadIfSourceNotLoaded(source);
    return GetStringRawNonAsync(source, key, variables);
}

function GetStringRawNonAsync(source, key, variables = []) {
    //console.log(sources);
    var text = sources[source][key];
    var i = 1;
    if (text == undefined) {
        text = "__" + source + "." + key + "__";
        return text;
    }
    for (var variable in variables) {
        //console.log("variable: " + variables[variable]);
        //console.log("$" + i);
        text = text.replaceAll("$" + i, variables[variable]);
        i++;
    }
    text = text.replace("osekai", "oseaki");
    text = text.replace("Osekai", "Oseaki");
    return text;
}

//function GetString($string, $variables = []) {
//    // string type: source.key (key can contain dots)
//    $string = explode(".", $string);
//    $source = $string[0];
//    $key = implode(".", array_slice($string, 1));
//    $text = GetStringRaw($source, $key, $variables);
//    return $text;
//}

async function GetString(string, variables = []) {
    // string type: source.key (key can contain dots)
    var string = string.split(".");
    var source = string[0];
    var key = string.slice(1).join(".");
    var text = await GetStringRaw(source, key, variables);
    return text;
}

function GetStringNonAsync(string, variables = []) {
    var string = string.split(".");
    var source = string[0];
    var key = string.slice(1).join(".");
    var text = GetStringRawNonAsync(source, key, variables);
    return text;
}
//function LocalizeText($string, $variables = [])
//{
//    // this text can include multiple strings, like this:
//    // 50 ??source.key?? something something ??source.anotherkey??
//    $strings = preg_match_all("/\?\?([^\?]+)\?\?/", $string, $matches);
//    
//    foreach ($matches[1] as $match) {
//        $localizedString = GetString($match, $variables);
//        $string = str_replace("??" . $match . "??", $localizedString, $string);
//    }
//    return $string;
//}

async function LocalizeText(string, variables = []) {
    // this text can include multiple strings, like this:
    // 50 ??source.key?? something something ??source.anotherkey??
    var strings = string.match(/\?\?([^\?]+)\?\?/g);

    if (strings) {
        for (var i = 0; i < strings.length; i++) {
            var cleanedString = strings[i].replace(/\?\?|\?\?/g, "");
            var localizedString = await GetString(cleanedString, variables);

            string = string.replace("??" + cleanedString + "??", localizedString);
        }
    }

    string = string.replace("osekai", "oseaki");
    string = string.replace("Osekai", "Oseaki");
    return string;
}

function LocalizeTextNonAsync(string, variables = []) {
    var strings = string.match(/\?\?([^\?]+)\?\?/g);

    if (strings) {
        for (var i = 0; i < strings.length; i++) {
            var cleanedString = strings[i].replace(/\?\?|\?\?/g, "");
            var localizedString = GetStringNonAsync(cleanedString, variables);

            string = string.replace("??" + cleanedString + "??", localizedString);
        }
    }
    return string;
}

function LocalizeInnerHTML(element) {
    LocalizeText(element.innerHTML).then(function(text) {
        // remove $1, $2, etc.
        text = text.replace(/\$\d/g, "");
        element.innerHTML = text;
    });
}

function LocalizeInnerHTMLText(element) {
    element.innerHTML = "";
    LocalizeText(string).then(function(text) {
        // remove all $1, $2, etc.
        text = text.replace(/\$\d/g, "");
        element.innerHTML = text;
    });
}

//var localeMutationObserver = new MutationObserver(function (mutations) {
//    document.getElementsByClassName("translatable").forEach(function (element) {
//        LocalizeInnerHTMLText(element);
//        element.classList.remove("translatable");
//    });
//});
//
//localeMutationObserver.observe(document.body, {
//    childList: true,
//    subtree: true
//});

// on document load
document.addEventListener("DOMContentLoaded", function() {

    var localeMutationObserver = new MutationObserver(function(mutations) {
        var elements = document.getElementsByClassName("translatable");
        for (var i = 0; i < elements.length; i++) {
            LocalizeInnerHTML(elements[i]);
            elements[i].classList.remove("translatable");
        }
    });

    localeMutationObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
});

document.GetString = GetString;
document.LocalizeText = LocalizeText;
document.GetStringRaw = GetStringRaw;
document.LocalizeInnerHTML = LocalizeInnerHTML;