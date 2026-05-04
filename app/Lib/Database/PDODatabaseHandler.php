<?php
namespace PHPMVC\Lib\Database;

class PDODatabaseHandler extends DatabaseHandler
{
    private static $_instance;
    private static $_handler;

    private function __construct()
    {
        self::init();
    }

    public function __call($name, $arguments)
    {
        if (self::$_handler === null) {
            throw new \Exception("Database connection not initialized.");
        }
        return call_user_func_array(array(self::$_handler, $name), $arguments);
    }

    protected static function init()
    {
        try {
            self::$_handler = new \PDO(
                'mysql:host=' . DATABASE_HOST_NAME . ';dbname=' . DATABASE_DB_NAME . ';port=' . DATABASE_PORT_NUMBER,
                DATABASE_USER_NAME, DATABASE_PASSWORD, array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                )
            );
        } catch (\PDOException $e) {
            // Re-throw or handle more formally. For now, we'll let it throw to stop execution if connection fails.
            throw new \Exception("Failed to connect to the database: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
