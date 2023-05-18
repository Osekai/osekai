<div class="teams__page">
    <div class="teams__page-navigation">
        <div class="teams__page-navigation-home">
            <i class="fas fa-home"></i>
        </div>
        <div class="teams__page-navigation-pages">
            <?php
            if (isset($ref_page['pages'])) {
                foreach ($ref_page['pages'] as $_page) {
                    $classes = "";
                    if($ref_page_inner == $_page) {
                        $classes = "teams__page-navigation-item-active";
                    }
                    echo "<a class=\"teams__page-navigation-item ".$classes."\" href=\"/teams/".$ref_page['name']."/".$_page['name']."\">";
                    echo $_page['display_name'];
                    echo "</a>";
                }
            }
            ?>
        </div>
        <div class="teams__page-navigation-menu">
            <i class="fas fa-circle"></i>
        </div>
    </div>
    <div id="teams_page" class="teams__page-content">
        <?= $page ?>
    </div>
</div>