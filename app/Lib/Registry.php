<?php
namespace PHPMVC\Lib;

class Registry
{
    private static ?self $_instance = null;
    private array $_data = [];
    private array $_factories = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->_data[$key] = $value;
    }

    public function __get(string $key): mixed
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }

        if (isset($this->_factories[$key])) {
            $factory = $this->_factories[$key];
            $this->_data[$key] = $factory($this);
            return $this->_data[$key];
        }

        return null;
    }

    public function register(string $key, callable $factory): void
    {
        $this->_factories[$key] = $factory;
    }

    public function has(string $key): bool
    {
        return isset($this->_data[$key]) || isset($this->_factories[$key]);
    }
}
