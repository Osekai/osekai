<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Dark.js"></script>

<style>
    .graph {
        width: 100%;
        height: 500px;
        max-width: 100%
    }

    .analytics__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
</style>
<div class="analytics__grid">
    <div>
        <h1>Comments</h1>
        <div id="comments" class="graph"></div>
    </div>
    <div>
        <h1>Beatmaps</h1>
        <div id="beatmaps" class="graph"></div>
    </div>
</div>

<div>
    <h1>Pageviews</h1>
    <div id="pageviews" class="graph"></div>
</div>
<script>

    window.onload = function () {
        GenerateGraph("comments", <?= json_encode(Database::execSimpleSelect("SELECT DATE(PostDate) AS ForDate,
        COUNT(*) AS NumPosts
        FROM   Comments
        GROUP BY DATE(PostDate)
        ORDER BY ForDate")) ?>, "ForDate", "NumPosts")

        GenerateGraph("beatmaps", <?= json_encode(Database::execSimpleSelect("SELECT DATE(SubmissionDate) AS ForDate,
        COUNT(*) AS NumPosts
        FROM   Beatmaps
        GROUP BY DATE(SubmissionDate)
        ORDER BY ForDate")) ?>, "ForDate", "NumPosts")

        GenerateGraph("pageviews", <?= json_encode(Database::execSimpleSelect("SELECT DATE(Date) AS ForDate,
        COUNT(*) AS NumPosts
        FROM   StatsPageViews
        GROUP BY DATE(Date)
        ORDER BY ForDate")) ?>, "ForDate", "NumPosts")
    }
</script>