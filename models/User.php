<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * "{{%user}}".
 *
 * @property int $id
 * @property string $auth_key
 * @property string $username
 * @property string $old_username
 * @property string $password_hash
 * @property int $role
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 * @property int $division
 * @property string $balance
 *
 * @property string $employeeName
 * @property Preference[] $preferences
 * @property Restaurant[] $restaurants
 * @property string $short
 * @property Balance[] $balanceHistory
 * @property Balance $latestBalanceHistory
 */
class User extends ActiveRecord implements IdentityInterface
{
    const BOOKKEEPER = [1, 19, 22];

    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;

    const DIVISION_NONE = 0;
    const DIVISION_PHP = 1;
    const DIVISION_ANDROID = 2;
    const DIVISION_IOS = 3;
    const DIVISION_JAVA = 4;
    const DIVISION_QA = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'role', 'division'], 'required'],
            ['role', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_USER]],
            ['division', 'in', 'range' => array_keys(static::divisionLabels())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Imię',
            'role' => 'Rola',
            'division' => 'Grupa',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'deleted' => 0]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException;
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds user by username.
     * @param string $username
     * @param bool $statusCheck
     * @return User
     */
    public static function findByUsername($username, $statusCheck = true)
    {
        $conditions = ['username' => $username];
        if ($statusCheck) {
            $conditions['deleted'] = 0;
        }
        return static::findOne($conditions);
    }

    /**
     * Checks if user is admin.
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    /**
     * Returns actual user name.
     * @return string
     */
    public function getEmployeeName()
    {
        return $this->deleted ? $this->old_username : $this->username;
    }

    /**
     * Balance history relation
     * @return ActiveQuery
     */
    public function getBalanceHistory()
    {
        return $this->hasMany(Balance::class, ['user_id' => 'id']);
    }

    /**
     * Balance history relation
     * @return ActiveQuery
     */
    public function getLatestBalanceHistory()
    {
        return $this->hasOne(Balance::class, ['user_id' => 'id'])->orderBy(['balance.id' => SORT_DESC])->limit(1);
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Adds new user.
     * @return bool
     */
    public function add()
    {
        $old = User::findOne(['username' => mb_strtolower($this->username, 'UTF-8')]);
        if ($old) {
            $this->addError('username', 'Użytkownik o tym imieniu jest już dodany.');
            return false;
        }

        $this->generateAuthKey();
        return $this->save();
    }

    /**
     * Return short name.
     * @return string
     */
    public function getShort()
    {
        $split = explode(' ', $this->username);
        $short = '';
        foreach ($split as $part) {
            if (strlen($part)) {
                $short .= mb_substr($part, 0, 1, 'UTF-8');
            }
        }

        return mb_strtoupper($short, 'UTF-8');
    }

    /**
     * Returns division labels.
     * @return array
     */
    public static function divisionLabels()
    {
        return [
            self::DIVISION_NONE => 'brak',
            self::DIVISION_PHP => 'PHP',
            self::DIVISION_ANDROID => 'Android',
            self::DIVISION_IOS => 'iOS',
            self::DIVISION_JAVA => 'Java',
            self::DIVISION_QA => 'QA',
        ];
    }
}
