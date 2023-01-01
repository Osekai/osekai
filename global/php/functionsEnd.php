<?php
if (isset($app)) {
    new_report_system();
}



$time = microtime(true) - $time_start;
$time = round($time, 4);

if (!isbot() && !isset($a404) && isset($app)) {
    // after some testing, this literally adds nothing to the page load time. at most it adds 1ms so it's not a big deal
    // if anything slows down, it's not this.
    include_once("osekaiAnalytics.php");
}

$tooltipText = "This debug panel is so that you, and our team, can make sure Osekai is running smoothly!\n";
//$tooltipText .= "\n  |  Load time: Seconds taken to generate the page";
//$tooltipText .= "\n  |  Aborted Session Saves: Amount of times a session has attempted to be saved, but no data has changed.";

?>
<meta charset="utf-8">
<div class="debug">
    <div class="debug__arrow">
        <i class="fas fa-caret-left"></i>
    </div>
    <div class="debug__inner">
        <div class="debug__header">
            <h1>Debug</h1>
            <div class="debug__moreinfo tooltip-v2" tooltip-content="<?php echo $tooltipText; ?>">
                ?
            </div>
        </div>
        <p><?php echo GetStringRaw("general", "page.generatedIn", ["<strong>" . $time . "</strong>"]); ?></p>
        <p>Aborted Session Saves: <strong><?php echo $abortedSaves; ?></strong></p>
        <p style="font-size: 10px">Commit <strong><a href="https://github.com/Osekai/osekai/commit/<?= $gitHash ?>"><?= $gitHash ?></a></strong>
            (branch <strong>
                <?= $gitBranchLink != "" ? '<a href="' . $gitBranchLink . '">' . $gitBranchName . '</a>' : $gitBranchName ?></strong>)

        <p style="font-size: 10px"><?= $gitDate; ?> UTC</p>

        <p>Version Key <?= OSEKAI_VERSION ?></p>
    </div>
</div>

<script>
    console.log("generated in: " + <?php echo $time; ?> + " seconds");
</script>