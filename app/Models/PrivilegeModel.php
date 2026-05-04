<?php
namespace PHPMVC\Models;

class PrivilegeModel extends AbstractModel
{

    public $PrivilegeId;
    public $Privilege;
    public $PrivilegeTitle;

    protected static string $tableName = 'app_users_privileges';

    protected static array $tableSchema = array(
        'PrivilegeId'       => self::DATA_TYPE_INT,
        'Privilege'         => self::DATA_TYPE_STR,
        'PrivilegeTitle'    => self::DATA_TYPE_STR
    );

    protected static string $primaryKey = 'PrivilegeId';
}