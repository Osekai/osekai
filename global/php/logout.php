<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

flushSession();
?>

<script>
    if(localStorage.getItem("url") != null){
        window.location.href = localStorage.getItem("url");
    }
    else{
        window.location.href = "<?= ROOT_URL ?>";
    }
</script>