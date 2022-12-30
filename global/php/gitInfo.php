<?php
$gitBasePath = $_SERVER['DOCUMENT_ROOT']  . '/.git'; // e.g in laravel: base_path().'/.git';
$gitStr = file_get_contents($gitBasePath . '/HEAD');

// on PR
// ref: refs/heads/pr/username/prID
$isPR = str_starts_with($gitStr, "ref: refs/heads/pr/");
$gitBranchLink = ""; // Leave null for detached head or for other cases where link is not handled

if ($isPR) {
    $gitBranchName = rtrim(str_replace("ref: refs/heads/", '', $gitStr));

    $gitHash = getCurrentCommitHash();
    $gitDate = gmdate("Y-m-d@H:i:s", getDateFromCommitHash($gitHash));

    $prNumber = rtrim(preg_replace("/(.*?\/){2}/", '', $gitBranchName));
    $gitBranchLink = "https://github.com/Osekai/osekai/pull/" . $prNumber;
} else {
    $gitBranchName = rtrim(preg_replace("/(.*?\/){2}/", '', $gitStr));

    $isDetachedHead =  getCurrentCommitHash() == $gitBranchName;
    if ($isDetachedHead) {
        $gitBranchName = "HEAD";
    } else {
        $gitBranchLink = "https://github.com/Osekai/osekai/tree/" . $gitBranchName;
    }

    $gitHash = getCurrentCommitHash();
    $gitDate = gmdate("Y-m-d@H:i:s", getDateFromCommitHash($gitHash));
}

function getCurrentCommitHash()
{
    return rtrim(shell_exec("git rev-parse HEAD"));
}

function getDateFromCommitHash($hash)
{
    $output = shell_exec("git show -s --format=%ct " . $hash);
    return intval($output);
}
