let Gvalue; //value containing % or number, connect with slider and input boxes
let userMedalsCount;
let currentSpecificUserID;
let currentSpecificUserMedalsCount;
let xhr = new XMLHttpRequest(); 
xhr.open("GET", `https://osekai.net/api/profiles/get_user.php?id=${nUserID}&min`, true); 
xhr.responseType = 'json'; 
xhr.send(); 
xhr.onload = function() { 
    userMedalsCount = xhr.response.user_achievements.length; 
}


async function osekaiSkillIssue(res, err) {
        
    console.log(`Medals Calculator Error: ${err}\nResponse Status: ${res.statusCode} | ${res.statusMessage}`); //idk why would we need that but ok, also can show user some error in case this function fires

}

async function GenerateFromPercentage(percentageValue) {

    return Math.ceil((medalAmount/100)*percentageValue); //Medals needed for x%, show that after calculation

}

async function GenerateFromCount(countValue) {

    return ((countValue/medalAmount)*100).toFixed(2); //Percent you'll have after getting x medals, show that after calculation

}

async function GenerateFromToolUserCount(countInput) { //gets user medals count and calculates their % after getting x more medals they inputted 

    let medalsAfterAdding = Number.parseInt(userMedalsCount) + Number.parseInt(countInput)
    return ((medalsAfterAdding/medalAmount)*100).toFixed(2);

}

async function GenerateFromToolUserPercentage(percentageInput) { //gets user medals percent and calculates how many more medals they need to reach x percent they inputted

    let medalsForInputtedPercent = Math.ceil((medalAmount/100)*percentageInput);
    return (Number.parseInt(medalsForInputtedPercent) - Number.parseInt(userMedalsCount));

}

async function GetUserData(userID) {
    new Promise((resolve, reject) => {
        
        let xhr = new XMLHttpRequest(); 
        xhr.open("GET", `https://osekai.net/api/profiles/get_user.php?id=${userID}&min`, true); 
        xhr.responseType = 'json'; 
        xhr.send(); 
        xhr.onload = function() { 
        if(xhr.status !== '200') return;
        currentSpecificUserID = userID;
        currentSpecificUserMedalsCount = xhr.response.user_achievements.length; 

        resolve();
        
        }

      })

      return Promise;

}

async function GenerateFromSpecificUserUserCount(specificUserId, countInput) {

    if(currentSpecificUserID !== specificUserId) {
       await GetUserData(specificUserId)
    }

    let medalsAfterAdding = Number.parseInt(currentSpecificUserMedalsCount) + Number.parseInt(countInput)
    return ((medalsAfterAdding/medalAmount)*100).toFixed(2);

}

async function GenerateFromSpecificUserUserPercentage(specificUserId, percentageInput) {

    if(currentSpecificUserID !== specificUserId) {
       await GetUserData(specificUserId)
    }

    let medalsForInputtedPercent = Math.ceil((medalAmount/100)*percentageInput);
    return (Number.parseInt(medalsForInputtedPercent) - Number.parseInt(currentSpecificUserMedalsCount));

}
