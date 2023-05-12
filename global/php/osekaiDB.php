<?php

define("SQL_TIMESPECIFIER_UNIT_MICROSECOND", 1);
define("SQL_TIMESPECIFIER_UNIT_SECOND", 2);
define("SQL_TIMESPECIFIER_UNIT_MINUTE", 3);
define("SQL_TIMESPECIFIER_UNIT_HOUR", 4);
define("SQL_TIMESPECIFIER_UNIT_DAY", 5);
define("SQL_TIMESPECIFIER_UNIT_WEEK", 6);
define("SQL_TIMESPECIFIER_UNIT_MONTH", 7);
define("SQL_TIMESPECIFIER_UNIT_QUARTER", 8);
define("SQL_TIMESPECIFIER_UNIT_YEAR", 9);

class SqlTimeSpecifier {
    private int $unit; 
    private int $value;

    public function __construct(int $unit, int $value) {
        $this->value = intval($value);
        $this->unit = $unit;
    }

    public function getSql()
    {
        $unit = match ($this->unit) {
            SQL_TIMESPECIFIER_UNIT_MICROSECOND   => "MICROSECOND",
            SQL_TIMESPECIFIER_UNIT_SECOND        => "SECOND",
            SQL_TIMESPECIFIER_UNIT_MINUTE        => "MINUTE",
            SQL_TIMESPECIFIER_UNIT_HOUR          => "HOUR",
            SQL_TIMESPECIFIER_UNIT_DAY           => "DAY",
            SQL_TIMESPECIFIER_UNIT_WEEK          => "WEEK",
            SQL_TIMESPECIFIER_UNIT_MONTH         => "MONTH",
            SQL_TIMESPECIFIER_UNIT_QUARTER       => "QUARTER",
            SQL_TIMESPECIFIER_UNIT_YEAR          => "YEAR",

            default => throw new RuntimeException("Invalid value for unit")
        };

        return $this->value . " " . $unit;
    }

	/**
	 * @return int
	 */
	public function getValue(): int {
		return $this->value;
	}

	/**
	 * @return SqlTimeSpecifierUnit
	 */
	public function getUnit(): int {
		return $this->unit;
	}
}

class Database
{

    private static $db;
    private $connection;

    private function __construct()
    {
        $this->connection = new MySQLi(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->connection->connect_error) {
            echo "<style>html { 
                background: linear-gradient(#223, #446);
                display: flex; align-items: center; justify-content: center; border-top: 20px solid #ff4411; color: white; height: 100vh; box-sizing: border-box; font-family: sans-serif; font-size: 30px;
            }
            </style>";
            echo "Osekai is currently experiencing issues connecting to the backend. Please try again later.";
            die();
        }
        $this->connection->set_charset("utf8mb4"); // to fix emojis in comments
        $inTransaction = false;
    }

    private static bool $inTransaction = false;

    public static function wrapInTransaction(callable $function, $flags = MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT) {
        if (self::$inTransaction) {
            return $function();
        }

        self::$inTransaction = true;

        $connection = self::getConnection();

        $connection->begin_transaction($flags);
        try {
            $result = $function();
            $connection->commit();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $connection->rollback();
            self::$inTransaction = false;
        }

        return $result;
    }

    public static function wrapInTransactionReadOnly(callable $function) {
        $connection = self::getConnection();

        $connection->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        try {
            $result = $function();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }

        return $result;
    }

    function __destruct()
    {
        //echo "closing";
        //$this->connection->close();
    }

    public static function getConnection()
    {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }

    /**
     * @param string $strQuery
     * @param string $strTypes
     * @param array $colVariables
     * 
     * @return array
     */
    public static function execSelect($strQuery, $strTypes, $colVariables)
    {
        $mysql = self::getConnection();
        $stmt = $mysql->prepare($strQuery);
        if ($strTypes != '')
            $stmt->bind_param($strTypes, ...$colVariables);
        $stmt->execute();
        $meta = $stmt->result_metadata();

        while ($field = $meta->fetch_field()) $params[] = &$row[$field->name];
        $stmt->bind_result(...$params);
        while ($stmt->fetch()) {
            foreach ($row as $key => $val) {
                $c[$key] = $val;
            }
            $hits[] = $c;
        }
        if ($mysql->more_results()) {
            $mysql->next_result();
        }
        if (isset($hits)) {
            return $hits;
        } else {
            return [];
        }
        return (array)$hits;
    }

    public static function execSelectFirstOrNull($strQuery, $strTypes, $colVariables) {
        $rows = Database::execSelect($strQuery, $strTypes, $colVariables);
        if (count($rows) == 0)
            return null;

        return $rows[0];
    }

    /**
     * @param string $strQuery
     * 
     * @return array
     */
    public static function execSimpleSelect($strQuery)
    {
        $oQuery = self::getConnection()->query($strQuery);
        $hits = array();
        while ($val = $oQuery->fetch_assoc()) {
            $hits[] = $val;
        }
        return $hits;
    }

    /**
     * @param string $strQuery
     * @param string $strTypes
     * @param array $colVariables
     * 
     * @return void
     */
    public static function execOperation($strQuery, $strTypes, $colVariables): void
    {
        $mysql = self::getConnection();
        $stmt = $mysql->prepare($strQuery);
        $stmt->bind_param($strTypes, ...$colVariables);
        $stmt->execute();
    }

    public static function getLastInsertedId() : int {
        $mysql = self::getConnection();
        return $mysql->insert_id;
    }

    /**
     * @param string $strQuery
     * 
     * @return void
     */
    public static function execSimpleOperation($strQuery): void
    {
        $mysql = self::getConnection();
        $stmt = $mysql->prepare($strQuery);
        $stmt->execute();
    }
}
