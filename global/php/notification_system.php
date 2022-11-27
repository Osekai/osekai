<div class="notification-container" id="notification_container">

</div>

<script>
    counter = 0; //dumb
    function generatenotification(style = "normal", text = "An error occurred.") {
        if (style == "normal") {
            style = ""; // basically we can set a style which just throws that into the class, which we can then change the colours of using css
        }
        counter++
        const markup = `
    <div class="notification ${style}" id="notif_` + counter + `">
            <div class="notification-icon-area">
                <i class="fas fa-info-circle notif-icon"></i>
            </div>
            <p class="notification-text">${text}</p>
            <div class="notification-close-button" onclick="closenotif(this)">
            <i class="fas fa-times"></i>
            </div>
    </div>`;

        obj = document.getElementById("notification_container");;
        obj.innerHTML += markup;
        var notif = document.getElementById("notif_" + counter);
        setTimeout(function() {
            notif.remove();
        }, 8000);
    }

    function closenotif(aobject) {
        aobject.parentElement.remove();
    }
</script>