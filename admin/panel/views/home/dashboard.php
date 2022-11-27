<!-- <div class="basic-page-content-padded basic-page-content-padded-scrollable">
    <h1>Welcome back to the Osekai Admin Panel, <strong><?php echo $_SESSION['osu']['username']; ?>.</strong></h1>
    <p>Please do not share images of this panel outside of private admin groups without prior permission or approval.</p>
    <a class="button">Button</a>
    <input type="text" class="input" placeholder="basic input">
    <input type="date" class="input" placeholder="">
    <a class="button button-danger">Danger Button</a>
    <a class="button button-warning">Warning Button</a>
</div> -->

<div class="basic-page-content-padded basic-page-content-padded-scrollable">
    <div class="dashboard">
        <div class="dashboard__left">
            <div class="dashboard__panel dashboard__panel-wide dashboard__panel-welcome">
                <div class="dashboard__panel-welcome-top">
                    <img src="/global/img/branding/vector/osekai_light.svg">
                    <div class="dashboard__panel-welcome-top-texts">
                        <h1>osekai <strong>admin panel</strong></h1>
                        <p>Please do not share images of this panel outside of moderator chats.</p>
                    </div>
                </div>
                <div class="dashboard__panel-welcome-bottom">
                    <div class="dashboard__panel-welcome-bottom-left">
                        [text placeholder]
                    </div>
                    <div class="dashboard__panel-welcome-bottom-right" id="dashboard__quote">
                        [quote placeholder]
                    </div>
                </div>
            </div>
            <div class="dashboard__panel">
                <div class="dashboard__panel-inner">
                    test
                </div>
            </div>
            <div class="dashboard__panel">
                <div class="dashboard__panel-inner">
                    test
                </div>
            </div>
            <div class="dashboard__panel">
                <div class="dashboard__panel-inner">
                    test
                </div>
            </div>
            <div class="dashboard__panel">
                <div class="dashboard__panel-inner">
                    test
                </div>
            </div>
            <div class="dashboard__panel">
                <div class="dashboard__panel-inner">
                    test
                </div>
            </div>
        </div>
        <div class="dashboard__right">
            <div class="dashboard__panel">
                <img src="" id="dashboard__image" class="dashboard__image">
                <div class="dashboard__panel-inner">
                    <h3 id="dashboard__image-text">></h3>
                    <p>Have a cool image? dm me[hubz], and i’ll throw it on this area :3</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const images = <?php echo json_encode(Database::execSimpleSelect("SELECT * FROM AdminFunImages")); ?>;
</script>