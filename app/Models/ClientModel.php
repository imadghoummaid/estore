<?php
namespace PHPMVC\Models;

class ClientModel extends AbstractModel
{

    public $ClientId;
    public $Name;
    public $PhoneNumber;
    public $Email;
    public $Address;

    protected static string $tableName = 'app_clients';

    protected static array $tableSchema = array(
        'Name'              => self::DATA_TYPE_STR,
        'PhoneNumber'       => self::DATA_TYPE_STR,
        'Email'             => self::DATA_TYPE_STR,
        'Address'           => self::DATA_TYPE_STR

    );

    protected static string $primaryKey = 'ClientId';
}