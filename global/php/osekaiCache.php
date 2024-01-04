<?php
class Caching
{
    /**
     * @param mixed $name
     * 
     * @return [type]
     */
    public static function getCache($name)
    {
        Caching::cleanCache(); 
        $caches = Database::execSelect("SELECT * FROM GlobalCache WHERE Title = ? ORDER BY Date", "s", [$name]);
        if ($caches == null || count($caches) == 0) {
            return null;
        }
        $cache = $caches[0]['Data'];
        return $cache;
    }

    /**
     * @param mixed $name
     * @param mixed $expiry
     * @param mixed $data
     * 
     * @return [type]
     */
    public static function saveCache($name, $expiry, $data)
    {
        // remove all with existing name
        Database::execOperation("DELETE FROM GlobalCache WHERE Title = ?", "s", [$name]);
        // if expiry is not a date, it is a number of seconds
        if (is_numeric($expiry)) {
            $expiry = date("Y-m-d H:i:s", time() + ($expiry * 2));
        }

        Database::execOperation("INSERT INTO GlobalCache (Title, Expiration, Data, Date) VALUES (?, ?, ?, CURRENT_TIMESTAMP)", "sss", [$name, $expiry, $data]);
    }

    /**
     * @return [type]
     */
    public static function cleanCache()
    {
        // removes expired caches
        Database::execSimpleOperation("DELETE FROM GlobalCache WHERE Expiration < CURRENT_TIMESTAMP");
    }

    /**
     * @param mixed $cacheName
     * 
     * @return [type]
     */
    public static function wipeCache($cacheName)
    {
        Database::execOperation("DELETE FROM GlobalCache WHERE Title = ?", "s", [$cacheName]);
    }

    /**
     * @param mixed $prefix
     * 
     * @return [type]
     */
    public static function wipeCacheFromPrefix($prefix)
    {
        Database::execOperation("DELETE FROM GlobalCache WHERE Title LIKE ?", "s", [$prefix . "%"]);
    }
}
?>