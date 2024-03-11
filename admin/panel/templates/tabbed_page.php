<div class="basic-page">
    <div class="basic-page-tabs">
        <?php
        foreach ($ref_page['pages'] as $_page) {
            $classes = "";
            if ($_page['name'] == $arguments[0]) {
                $classes = "basic-page-tab-active";
            }
            echo '<a class="basic-page-tab ' . $classes . '" href="/admin/panel/' . $ref_page['name'] . '/' . $_page['name'] . '">
                <p>' . $_page['display_name'] . '</p>
            </a>';
        }
        ?>
    </div>
    <div class="basic-page-content">
        <?php echo $page; ?>
    </div>
</div>