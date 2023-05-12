<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

class ChangelogEntryRepository
{
    public static function getChangelogEntries(int $changelogId): array
    {
        return array_map(
            function ($row) {
                $row['tags'] = json_decode($row['tags']);
                return $row;
            },
            Database::execSelect(
                "SELECT `Name` as `name`, `Tags` as `tags`, `User` as `user`, `Link` as `link`
                FROM ChangelogEntries WHERE ChangelogId = ?",
                "i",
                [$changelogId]
            )
        );
    }

    public static function addChangelogEntries($changelogId, array $entries)
    {
        $query = "INSERT INTO `ChangelogEntries` (`ChangelogId`, `Name`, `Tags`, `User`, `Link`) VALUES ";
        $typeSignature = "";
        $parameters = [];

        for ($i=0; $i < count($entries); $i++) {
            $entry = $entries[$i];
            $query .= "(?, ?, ?, ?, ?)";

            $typeSignature .= "issss";
            array_push($parameters, ...[$changelogId, $entry['name'], json_encode($entry['tags']), $entry['user'], $entry['link']]);

            if ($i == count($entries) - 1)
                $query .= ";";
            else
                $query .= ",";
        }

        Database::execOperation($query, $typeSignature, $parameters);
    }
}