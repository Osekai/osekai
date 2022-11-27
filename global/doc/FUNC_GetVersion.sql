/* get all versions from SnapshotsAzeliaVersions and join SnapshotsAzeliaScreenshots and SnapshotsAzeliaDownloads */
SELECT * FROM SnapshotsAzeliaVersions
JOIN SnapshotsAzeliaScreenshots ON SnapshotsAzeliaVersions.Id = SnapshotsAzeliaScreenshots.ReferencedVersion
JOIN SnapshotsAzeliaDownloads ON SnapshotsAzeliaVersions.Id = SnapshotsAzeliaDownloads.ReferencedVersion
WHERE SnapshotsAzeliaVersions.Id = $1;