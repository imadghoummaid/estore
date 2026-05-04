<?php
namespace PHPMVC\Models;

class UserGroupModel extends AbstractModel
{

    public $GroupId;
    public $GroupName;

    protected static string $tableName = 'app_users_groups';

    protected static array $tableSchema = array(
        'GroupId'            => self::DATA_TYPE_INT,
        'GroupName'          => self::DATA_TYPE_STR
    );

    protected static string $primaryKey = 'GroupId';
}