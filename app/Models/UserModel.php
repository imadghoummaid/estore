<?php
namespace PHPMVC\Models;

use PHPMVC\Lib\Database\DatabaseHandler;

class UserModel extends AbstractModel
{
    public $UserId;
    public $Username;
    public $Password;
    public $Email;
    public $PhoneNumber;
    public $SubscriptionDate;
    public $LastLogin;
    public $GroupId;
    public $Status;

    /**
     * @var UserProfileModel
     */
    public $profile;
    public $privileges;

    protected static string $tableName = 'app_users';

    protected static array $tableSchema = array(
        'UserId'            => self::DATA_TYPE_INT,
        'Username'          => self::DATA_TYPE_STR,
        'Password'          => self::DATA_TYPE_STR,
        'Email'             => self::DATA_TYPE_STR,
        'PhoneNumber'       => self::DATA_TYPE_STR,
        'SubscriptionDate'  => self::DATA_TYPE_DATE,
        'LastLogin'         => self::DATA_TYPE_STR,
        'GroupId'           => self::DATA_TYPE_INT,
        'Status'            => self::DATA_TYPE_INT,
    );

    protected static string $primaryKey = 'UserId';

    public function cryptPassword($password)
    {
        $this->Password = password_hash($password, PASSWORD_DEFAULT);
    }

    public static function getUsers(UserModel $user)
    {
        return self::get(
            'SELECT au.*, aug.GroupName GroupName FROM ' . self::$tableName . ' au INNER JOIN app_users_groups aug ON aug.GroupId = au.GroupId WHERE au.UserId != :userid',
            ['userid' => [self::DATA_TYPE_INT, $user->UserId]]
        );
    }

    public static function userExists($username)
    {
        return self::get(
            'SELECT * FROM ' . self::$tableName . ' WHERE Username = :username',
            ['username' => [self::DATA_TYPE_STR, $username]]
        );
    }

    public static function authenticate($username, $password, $session)
    {
        $sql = 'SELECT *, (SELECT GroupName FROM app_users_groups WHERE app_users_groups.GroupId = ' . self::$tableName . '.GroupId) GroupName FROM ' . self::$tableName . ' WHERE Username = :username';
        $foundUser = self::getOne($sql, ['username' => [self::DATA_TYPE_STR, $username]]);

        if (false !== $foundUser) {
            if (password_verify($password, $foundUser->Password)) {
                if ($foundUser->Status == 2) {
                    return 2;
                }
                $foundUser->LastLogin = date('Y-m-d H:i:s');
                $foundUser->save();
                $foundUser->profile = UserProfileModel::getByPK($foundUser->UserId);
                $foundUser->privileges = UserGroupPrivilegeModel::getPrivilegesForGroup($foundUser->GroupId);
                $session->u = $foundUser;
                return 1;
            }
        }
        return false;
    }
}
