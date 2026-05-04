<?php
namespace PHPMVC\Models;

use PHPMVC\Lib\Database\DatabaseHandler;

abstract class AbstractModel
{
    public const DATA_TYPE_BOOL = \PDO::PARAM_BOOL;
    public const DATA_TYPE_STR = \PDO::PARAM_STR;
    public const DATA_TYPE_INT = \PDO::PARAM_INT;
    public const DATA_TYPE_DECIMAL = 4;
    public const DATA_TYPE_DATE = 5;

    private const VALIDATE_DATE_STRING = '/^[1-2][0-9][0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:0[1-9])|(?:(?:1|2)[0-9])|(?:3[0-1]))$/';
    private const VALIDATE_DATE_NUMERIC = '/^\d{6,8}$/';
    private const DEFAULT_MYSQL_DATE = '1970-01-01';

    protected static string $tableName;
    protected static array $tableSchema;
    protected static string $primaryKey;

    private function prepareValues(\PDOStatement &$stmt): void
    {
        foreach (static::$tableSchema as $columnName => $type) {
            if ($type === self::DATA_TYPE_DECIMAL) {
                $sanitizedValue = filter_var($this->$columnName, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(":{$columnName}", $sanitizedValue);
            } elseif ($type === self::DATA_TYPE_DATE) {
                if (!preg_match(self::VALIDATE_DATE_STRING, $this->$columnName)) {
                    $stmt->bindValue(":{$columnName}", self::DEFAULT_MYSQL_DATE);
                } else {
                    $stmt->bindValue(":{$columnName}", $this->$columnName);
                }
            } else {
                $stmt->bindValue(":{$columnName}", $this->$columnName, $type);
            }
        }
    }

    private function buildNameParametersSQL(): string
    {
        $namedParams = '';
        foreach (static::$tableSchema as $columnName => $type) {
            $namedParams .= $columnName . ' = :' . $columnName . ', ';
        }
        return trim($namedParams, ', ');
    }

    private function create(): bool
    {
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . $this->buildNameParametersSQL();
        $stmt = DatabaseHandler::factory()->prepare($sql);
        $this->prepareValues($stmt);
        if ($stmt->execute()) {
            $this->{static::$primaryKey} = DatabaseHandler::factory()->lastInsertId();
            return true;
        }
        return false;
    }

    private function update(): bool
    {
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . $this->buildNameParametersSQL() . ' WHERE ' . static::$primaryKey . ' = :' . static::$primaryKey;
        $stmt = DatabaseHandler::factory()->prepare($sql);
        $this->prepareValues($stmt);
        $stmt->bindValue(':' . static::$primaryKey, $this->{static::$primaryKey}, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function save(bool $primaryKeyCheck = true): bool
    {
        if (false === $primaryKeyCheck) {
            return $this->create();
        }
        return $this->{static::$primaryKey} === null ? $this->create() : $this->update();
    }

    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = :' . static::$primaryKey;
        $stmt = DatabaseHandler::factory()->prepare($sql);
        $stmt->bindValue(':' . static::$primaryKey, $this->{static::$primaryKey}, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function getAll(): \ArrayIterator|false
    {
        $sql = 'SELECT * FROM ' . static::$tableName;
        return static::get($sql);
    }

    public static function getByPK($pk): static|false
    {
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = :pk';
        $stmt = DatabaseHandler::factory()->prepare($sql);
        $stmt->bindValue(':pk', $pk);
        if ($stmt->execute() === true) {
            if (method_exists(static::class, '__construct')) {
                $obj = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class, array_keys(static::$tableSchema));
            } else {
                $obj = $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }
            return !empty($obj) ? array_shift($obj) : false;
        }
        return false;
    }

    public static function getBy(array $columns, array $options = []): \ArrayIterator|false
    {
        $whereClause = [];
        foreach ($columns as $columnName => $value) {
            $whereClause[] = $columnName . ' = :' . $columnName;
        }
        $whereClause = implode(' AND ', $whereClause);
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' . $whereClause;

        // Prepare options for get method, ensuring it uses named parameters
        $preparedOptions = [];
        foreach ($columns as $columnName => $value) {
            $preparedOptions[$columnName] = [self::DATA_TYPE_STR, $value];
        }

        return static::get($sql, $preparedOptions);
    }

    public static function get(string $sql, array $options = []): \ArrayIterator|false
    {
        $stmt = DatabaseHandler::factory()->prepare($sql);
        if (!empty($options)) {
            foreach ($options as $columnName => $type) {
                if ($type[0] === self::DATA_TYPE_DECIMAL) {
                    $sanitizedValue = filter_var($type[1], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stmt->bindValue(":{$columnName}", $sanitizedValue);
                } elseif ($type[0] === self::DATA_TYPE_DATE) {
                    if (!preg_match(self::VALIDATE_DATE_STRING, $type[1])) {
                        $stmt->bindValue(":{$columnName}", self::DEFAULT_MYSQL_DATE);
                    } else {
                        $stmt->bindValue(":{$columnName}", $type[1]);
                    }
                } else {
                    $stmt->bindValue(":{$columnName}", $type[1], $type[0]);
                }
            }
        }
        $stmt->execute();
        if (method_exists(static::class, '__construct')) {
            $results = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class, array_keys(static::$tableSchema));
        } else {
            $results = $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
        }
        if (is_array($results) && !empty($results)) {
            return new \ArrayIterator($results);
        }
        return false;
    }

    public static function getOne(string $sql, array $options = []): static|false
    {
        $result = static::get($sql, $options);
        return $result === false ? false : $result->current();
    }

    public static function getModelTableName(): string
    {
        return static::$tableName;
    }
}
