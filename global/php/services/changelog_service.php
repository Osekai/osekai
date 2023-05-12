<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

changelog_repository();
changelog_entry_repository();

class ChangelogService
{
    private static function addEntriesToChangelog(&$changelog) {
        $changelogEntries = ChangelogEntryRepository::getChangelogEntries($changelog['id']);
        $changelog['entries'] = $changelogEntries;

        return new OkApiResult($changelog);
    }

    public static function getChangelogs(int $limit = PHP_INT_MAX): ?array {
        return Database::wrapInTransactionReadOnly(function () use ($limit) {
            $changelogs = ChangelogRepository::getChangelogs($limit);

            foreach ($changelogs as &$changelog) {
                ChangelogService::addEntriesToChangelog($changelog);
                unset($changelog['id']);
            }

            return $changelogs;
        });
    }

    public static function getChangelogByName(int $id): ?array {
        return Database::wrapInTransactionReadOnly(function() use ($id) {
            $changelog = ChangelogRepository::getChangelogById($id);
            if (!isset($changelog))
                return null;
    
            ChangelogService::addEntriesToChangelog($changelog);
            unset($changelog['id']);
            return $changelog;
        });
    }

    public static function addChangelog(int $name, string $date, array $entries): ?array {
        return Database::wrapInTransaction(function () use ($name, $date, $entries) {
            if (ChangelogRepository::changelogExistsByName($name))
                throw new InvalidOperationException("A changelog with this name already exists");

            $changelogId = ChangelogRepository::addChangelog($name, $date);
            ChangelogEntryRepository::addChangelogEntries($changelogId, $entries);
        });
    }
}