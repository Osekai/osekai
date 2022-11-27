var mobile = false;
window.mobile = mobile;

window.addEventListener('resize', checkMobile);
checkMobile();

function checkMobile(){
    if(window.innerWidth >= 900){
        if(window.mobile == true){
            // we do this check to not spam the console when the user is not on a mobile device
            console.log("moved to desktop");
        }
        mobile = false;
    }
    
    if(window.innerWidth < 900){
        if(mobile == false){
            // we do this check to not spam the console when the user is not on a desktop device
            console.log("moved to mobile");
        }
        mobile = true;
    }
}