<?php
$gitBasePath = $_SERVER['DOCUMENT_ROOT']  . '/.git'; // e.g in laravel: base_path().'/.git';
$gitStr = file_get_contents($gitBasePath . '/HEAD');
$gitBranchName = rtrim(preg_replace("/(.*?\/){2}/", '', $gitStr));
$gitPathBranch = $gitBasePath . '/refs/heads/' . $gitBranchName;
$gitHash = file_get_contents($gitPathBranch);
$gitDate = gmdate("Y-m-d@H:i:s", filemtime($gitPathBranch));