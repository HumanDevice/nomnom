<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * "{{%user}}".
 *
 * @property integer $id
 * @property string $auth_key
 * @property string $username
 * @property string $old_username
 * @property string $password_hash
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 * 
 * @property string $employeeName
 * @property Preference[] $preferences
 * @property Restaurant[] $restaurants
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    
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
        return [TimestampBehavior::className()];
    }
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'role'], 'required'],
            ['role', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_USER]],
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
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
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
}
