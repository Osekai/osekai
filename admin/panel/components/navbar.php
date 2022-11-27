<div class="navbar">
    <div class="navbar-logo">
        <img src="/global/img/branding/vector/osekai_light.svg">
        <p>osekai <strong>admin panel</strong></p>
    </div>
    <div class="navbar-pages-centered">
        <div class="navbar-pages-centered-container">
            <?php
            foreach ($pages as $page) {
                $classes = "";
                if ($ref_page['name'] == $page['name']) {
                    $classes = "navbar-page-selected";
                }
            ?>
                <a class="navbar-page <?= $classes ?>" href="/admin/panel/<?=$page['name']?>">
                    <p><?= $page['display_name'] ?></p>
                </a>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="navbar-user">
        <p><?=$_SESSION['osu']['username']?></p>
        <img src="https://a.ppy.sh/<?=$_SESSION['osu']['id']?>">
    </div>
</div>