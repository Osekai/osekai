<div class="osekai__report-overlay osekai__report-overlay--hidden" id="osekai__report-overlay">
    <div class="osekai__report-overlay-panel">
        <div class="osekai__report-overlay-panel-top">
            <img src="/global/img/icons/report-symbol.svg">
        </div>
        <div class="osekai__report-overlay-panel-bottom">
            <?php if(loggedin()) { ?>
            <h1 id="report-title">Report this comment</h1>
            <p id="report-sub">You are currently reporting a comment. Please give us in-depth details on why you think this comment should be reviewed or deleted.</p>
            <textarea id="report_comment_text" placeholder="" class="osekai__input"></textarea>
            <div class="osekai__report-overlay-buttons">
                <div class="osekai__button" onclick="cancelReport()"><?php echo GetStringRaw("general", "cancel"); ?></div>
                <div class="osekai__button osekai__button-highlighted" onclick="sendReport()"><?php echo GetStringRaw("general", "submit"); ?></div>
            </div>
            <?php } else { ?>
                <h1 id="report-title">Report this comment</h1>
            <p><?php echo GetStringRaw("report", "logged-out"); ?></p>
            <div class="osekai__report-overlay-buttons" style="justify-content: center;">
                <div class="osekai__button" onclick="cancelReport()"><?php echo GetStringRaw("general", "ok"); ?></div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="osekai__report-overlay osekai__report-overlay--hidden osekai__report-overlay-success" id="osekai__report-overlay-success">
    <div class="osekai__report-overlay-panel">
        <div class="osekai__report-overlay-panel-top">
            <img src="/global/img/icons/report-symbol.svg">
        </div>
        <div class="osekai__report-overlay-panel-bottom">
            <h1 id="finished-header"></h1>
            <p id="finished-text">Thank you for reporting this comment. We will review it as soon as possible.</p>
            <div class="osekai__report-overlay-buttons-success">
                <div class="osekai__button" onclick="closeReportSuccess()" id="finished-button"><?php echo GetStringRaw("general", "close"); ?></div>
            </div>
        </div>
    </div>
</div>
<script src="/global/js/osekaiReportSystem.js?v=1.0.2"></script>