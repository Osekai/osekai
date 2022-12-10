<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

define(
    "CHANGELOG_QUERY",
    
    "SELECT `c1`.`Id` as `id`, `c1`.`Name` as `name`, `c1`.`Date` as `date`,
    `c3`.`name` as `previous`, `c2`.`name` as `next`
    FROM Changelogs c1
    LEFT JOIN Changelogs c2 ON c2.Id = (SELECT MIN(ct.Id) FROM Changelogs ct WHERE ct.Id > c1.Id)
    LEFT JOIN Changelogs c3 ON c3.Id = (SELECT MAX(ct.Id) FROM Changelogs ct WHERE ct.Id < c1.Id)"
);

class ChangelogRepository
{
    public static function getChangelogByName(int $name): ?array
    {
        return Database::execSelectFirstOrNull(
            CHANGELOG_QUERY . " WHERE c1.Name = ?",
            "i",
            [$name]
        );
    }

    public static function getChangelogById(int $name): ?array
    {
        return Database::execSelectFirstOrNull(
            CHANGELOG_QUERY . " WHERE c1.Id = ?",
            "i",
            [$name]
        );
    }

    public static function getChangelogs(int $limit = PHP_INT_MAX): ?array
    {
        return Database::execSelect(
            CHANGELOG_QUERY . " ORDER BY c1.Id DESC LIMIT ?",
            "i",
            [$limit]
        );
    }


    public static function changelogExistsByName(int $name): bool {
        return Database::execSelectFirstOrNull(
            "SELECT (EXISTS (SELECT * FROM Changelogs WHERE Name = ?)) as `result`", 
            "i", 
            [$name]
        )['result'];
    }

    public static function addChangelog(int $name, string $date): int
    {
        Database::execOperation(
            "INSERT INTO `Changelogs` (`Name`, `Date`) VALUES (?, ?);",
            "is",
            [$name, $date]
        );

        return Database::getLastInsertedId();
    }
}