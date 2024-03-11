<div class="basic-page-split">
    <div class="basic-page-sidebar">
        <div class="basic-page-item-list">
            <!-- Report Component -->
            <!-- <div class="report-item beatmap pending">
                <div class="item-title-bar">
                    <span class="title">BEATMAP</span>
                    <span class="status-badge pending">PENDING</span>
                    <span class="app-shield"><img src="/global/img/branding/vector/white/medals.svg"></span>
                    <span class="report-id">#001</span>
                </div>
                <div class="item-description">
                    <div class="user-infomation">
                        <span class="username">RainGetaway</span>
                        <span class="date">26/11/2023</span>
                    </div>
                    <p class="user-message">
                        This is really famous 3 mods map, but I haven't heard that this map unlocks medal.
                    </p>
                </div>
            </div>

            <div class="report-item comment pending selected" onclick="openReport(2)">
                <div class="item-title-bar">
                    <span class="title">COMMENT</span>
                    <span class="status-badge open">OPEN</span>
                    <span class="report-id">#002</span>
                </div>
                <div class="item-description">
                    <div class="user-infomation">
                        <span class="username">RainGetaway</span>
                        <span class="date">26/11/2023</span>
                    </div>
                    <p class="user-message">
                        This is really famous 3 mods map, but I haven't heard that this map unlocks medal.
                    </p>
                </div>
            </div>

            <div class="report-item comment pending">
                <div class="item-title-bar">
                    <span class="title">bug</span>
                    <span class="status-badge closed">CLOSED</span>
                    <span class="report-id">#003</span>
                </div>
                <div class="item-description">
                    <div class="user-infomation">
                        <span class="username">RainGetaway</span>
                        <span class="date">26/11/2023</span>
                    </div>
                    <p class="user-message">
                        This is really famous 3 mods map, but I haven't heard that this map unlocks medal.
                    </p>
                </div>
            </div> -->

        </div>
    </div>
    <div class="basic-page-inner basic-page-inner-content">
        <div class="report-item beatmap resolved">
            <div class="item-title-bar">
                <span class="title">beatmap</span>
                <span class="status-badge resolved">resolved</span>
                <span class="app-shield">Medals<img src="/global/img/branding/vector/white/medals.svg"></span>
            </div>
            <div class="item-description">
                <img src="https://a.ppy.sh/7279762" alt="Profile Picture">
                <div class="user-container">
                    <div class="user-infomation">
                        <span class="username">RainGetaway</span>
                        <span class="date">26/11/2023</span>
                    </div>
                    <p class="user-message">
                        This is really famous 3 mods map, but I haven't heard that this map unlocks medal.
                    </p>
                </div>
            </div>
        </div>
        <div class="report-description">
            <div class="column">
                <div class="report-details report-list">
                    <h1>Report Details</h1>
                    <span class="report__submission-date">Submitted at <strong>2:04pm, 06/11/2022</strong></span>
                    <span class="report__reporter-id">Reporter ID: <strong>3298592</strong></span>
                    <span class="report__reporter-username">Reporter Username: <strong>RainGetaway</strong></span>
                    <span class="report__report-type">Report Type: <strong>BEATMAP</strong></span>
                    <span class="report__report-link">Reported on <strong><a href="https://osekai.net/medals/?medal=Nonstop">https://osekai.net/medals/?medal=Nonstop</a></strong></span>
                    <span class="report__report-id">Report ID: <strong>0042</strong></span>
                </div>
                <div class="report-list report-beatmap">
                    <h1>Beatmap Details</h1>
                    <!-- work on this pls -->
                    <beatmap-card beatmap-id=292301></beatmap-card>
                    <span class="report__beatmap-submitter-id">Submitter ID: <strong>3298592</strong></span>
                    <span class="report__beatmap-submitter-username">Submitter Username: <strong>set</strong></span>
                </div>
                <div class="report-list report-comment">
                    <h1>Comment Details</h1>
                    <!-- No custom elements for now...-->
                    <span class="report_comment-text">Did anyone report me? if so.. contact dev ~ Coppertine</span>
                </div>
            </div>
            <div class="column">
                <div class="report-state">
                    <h1>Report State</h1>
                    <div class="status-badge-list">
                        <div class="status-badge pending" onclick="setReportStatus(0,ReportStatus.Pending)">pending</div>
                        <div class="status-badge open" onclick="setReportStatus(0,ReportStatus.Open)">open</div>
                        <div class="status-badge resolved selected" onclick="setReportStatus(0,ReportStatus.Resolved)">resolved</div>
                        <div class="status-badge closed" onclick="setReportStatus(0,ReportStatus.Closed)">closed</div>
                    </div>
                </div>
                <div class="report-actions">
                    <h1>Actions</h1>
                    <button class="button button-danger">Delete</button>
                    <button class="button">Restrict User</button>
                </div>
            </div>
        </div>
    </div>
    <div class="basic-page-notes">
        <notes-section></notes-section>
    </div>
</div>