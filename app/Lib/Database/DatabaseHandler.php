<?php
namespace PHPMVC\Lib\Database;

abstract class DatabaseHandler
{
    public const DATABASE_DRIVER_PDO       = 1;
    public const DATABASE_DRIVER_MYSQLI    = 2;

    private function __construct() {}

    abstract protected static function init();

    abstract public static function getInstance();

    public static function factory(): PDODatabaseHandler|MySQLiDatabaseHandler
    {
        $driver = DATABASE_CONN_DRIVER;
        if ($driver == self::DATABASE_DRIVER_PDO) {
            return PDODatabaseHandler::getInstance();
        } elseif ($driver == self::DATABASE_DRIVER_MYSQLI) {
            return MySQLiDatabaseHandler::getInstance();
        }
        throw new \Exception("Invalid database driver");
    }
}
