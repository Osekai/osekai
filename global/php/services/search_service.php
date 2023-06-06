<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

class SearchUserResult
{
    private int $userId;
    private string $username;
    private string $avatarUrl;

    public function __construct(int $userId, string $username, string $avatarUrl)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->avatarUrl = $avatarUrl;
    }

    public static function fromApiResult(array $apiResult): SearchUserResult {
        return new SearchUserResult($apiResult['id'], $apiResult['username'], $apiResult['avatar_url']);
    }
    
    public function getUserId(): int { return $this->userId; }
    public function getUsername(): string { return $this->username; }
    public function getAvatarUrl(): string { return $this->avatarUrl; }
}

class SearchMedalResult
{
    private string $medalName;
    private string $iconUrl;

    public function __construct(string $medalName, string $iconUrl)
    {
        $this->medalName = $medalName;
        $this->iconUrl = $iconUrl;
    }

    public static function fromRow(array $row): SearchMedalResult {
        return new SearchMedalResult($row['name'], $row['link']);
    }
    
    public function getMedalName(): string { return $this->medalName; }
    public function getIconUrl(): string { return $this->iconUrl; }
}

class SearchSnapshotVersionResult
{
    private string $snapshotVersionName;
    private int $snapshotVersionId;

    public function __construct(string $snapshotVersionId, string $snapshotVersionName)
    {
        $this->snapshotVersionId = $snapshotVersionId;
        $this->snapshotVersionName = $snapshotVersionName;
    }

    public static function fromRow(array $row): SearchSnapshotVersionResult {
        return new SearchSnapshotVersionResult($row['id'], $row['name']);
    }
    
    public function getSnapshotVersionName(): string { return $this->snapshotVersionName; }
    public function getSnapshotVersionId(): int { return $this->snapshotVersionId; }
}

class SearchService
{
    public static function searchUser(string $query, int $limit = 10): array
    {
        if ($query === '')
            return [];

        $users = json_decode(v2_search($query), true);

        if (isset($users['user']['data'])) {
            $users = $users['user']['data'];
            $users = array_slice($users, 0, $limit);
        }

        return array_map(static function($p) { return SearchUserResult::fromApiResult($p); }, $users);
    }

    public static function searchMedal(string $query, int $limit = 10): array
    {
        if ($query === '')
            return [];

        $query = mysqli_escape_string(Database::getConnection(), $query);
        $medals = Database::execSelect("SELECT `name`, `link` FROM Medals WHERE `name` LIKE '%$query%' ORDER BY `name` LIMIT ?", "i", array($limit));
        return array_map(static function($p) { return SearchMedalResult::fromRow($p); }, $medals);
    }

    public static function searchSnapshotVersion(string $query, int $limit = 10): array
    {
        if ($query === '')
            return [];

        $query = mysqli_escape_string(Database::getConnection(), $query);
        $medals = Database::execSelect("SELECT `id`, json->>\"$.version_info.name\" as `name` FROM SnapshotVersions WHERE json->>\"$.version_info.name\" LIKE '%$query%' LIMIT ?", "i", array($limit));
        return array_map(static function($p) { return SearchSnapshotVersionResult::fromRow($p); }, $medals);
    }
}
