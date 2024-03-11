<?php

if (isset($_GET['user'])) {
?>
    <div class="basic-page">
        <div class="user">
            <div class="user__row">
                <div class="user__cover">
                    <img selector="banner" class="user__cover-image">
                    <div class="user__panel user__info">
                        <img selector="pfp">
                        <div class="user__info-text">
                            <h3 selector="username">? <span>?</span></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="user__row">
                <div class="user__panel user__data">
                    <div class="user__inforow"><span id="commentCount">?</span> Comments</div>
                    <div class="user__inforow"><span id="beatmapCount">?</span> <i class="oif-app-medals"></i>Beatmaps</div>
                    <div class="user__inforow"><span id="submissionCount">?</span> <i class="oif-app-snapshots"></i>Submissions</div>
                    <div class="user__inforow"><span id="versionCount">?</span> <i class="oif-app-snapshots"></i>Versions</div>
                </div>
                <div class="user__panel">
                    <p id="lastLogin">Loading...</light></p>
                </div>
            </div>
            <div class="user__row">
                <div class="user__panel padding-none">
                    <div class="nano-page-selector">
                        <div class="nano-page">
                            test
                        </div>
                        <div class="nano-page">
                            test
                        </div>
                        <div class="nano-page">
                            test
                        </div>
                        <div class="nano-page nano-page-selected">
                            test
                        </div>
                    </div>
                    <div class="user__panel-inner">
                        test
</div>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>

    <form action="user" method="get">
        ID: <input type="text" name="user" class="input">
        <input type="submit" class="button">
    </form>
<?php } ?>