<div class="basic-page">
    <div class="basic-page-content">
        <div class="basic-page-split">
            <div class="basic-page-sidebar">
                <div class="basic-page-sidebar-padded">
                    <div class="basic-form">
                        <p>Filter User</p>
                        <input class="input" type="text" id="restrictions__filter-user" placeholder="Username / User ID">
                        <p>Date After</p>
                        <input type="date" class="input" id="restrictions__filter-date-after">
                        <p>Date Before</p>
                        <input type="date" class="input" id="restrictions__filter-date-before">
                        
                    </div>
                    

                </div>
                <div class="basic-page-bottom-bar">
                        <a class="button" id="restrictions__filter-button" onclick="searchRestrictions()">Filter</a>
                    </div>
            </div>
            <div class="basic-page-inner" id="restrictions__list">
                <div class="restrictions__item">
                    <div class="restrictions__item-info">
                        <div class="restrictions__user-section">
                            <img src="https://a.ppy.sh/10379965" class="restrictions__profile-image">
                            <div class="restrictions__user-info">
                                <h2>Tanza3D</h2>
                                <h3>4 days ago (22-10-2022)</h3>
                            </div>
                        </div>
                        <div class="restrictions__item-actions">
                            <a class="button">Edit Restriction Details</a>
                            <a class="button button-danger">Unrestrict</a>
                        </div>
                    </div>
                    <div class="restrictions__item-reason">
                        <p>bla bla bla, reasoning here, this is why this guy was restricted... you get the idea</p> 
                    </div>
                </div>
            </div>
            <div class="basic-page-sidebar">
                <div class="basic-page-sidebar-padded">
                    <div class="restrictions__submit-form basic-form">
                        <h2>Restrict a User</h2>
                        <p>User ID</p>
                        <input class="input input-pattern" type="text" id="restrictions__report-user-id" placeholder="None" pattern="[0-9]{0,8}">
                        <p>Reason (Moderator only)</p>
                        <textarea class="input" id="restrictions__report-reason" placeholder="None"></textarea>

                    </div>
                </div>
                <div class="basic-page-bottom-bar">
                    <a id="medals__restrict-button" class="button button-danger" onclick="submitReport()">Restrict User</a>
                </div>
            </div>
        </div>
    </div>
</div>