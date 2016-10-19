<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * StartForm
 *
 * @property User|null $user This property is read-only.
 *
 */
class StartForm extends Model
{
    public $username;
    public $password;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'string', 'min' => 3, 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Imię',
            'password' => 'Hasło',
        ];
    }
    
    /**
     * Sets password and logs user.
     * @return boolean
     */
    public function start()
    {
        if ($this->validate()) {
            if (empty($this->user)) {
                $this->addError('username', 'Nie ma Cię na liście. Zgłoś się do adminów.');
                return false;
            }
            if (!empty($this->user->password_hash)) {
                $this->addError('password', 'Hej, masz już ustawione hasło! Jeśli chcesz je zmienić, zgłoś się do adminów.');
                return false;
            }
            $this->user->setPassword($this->password);
            if ($this->user->save()) {
                return Yii::$app->user->login($this->user, 3600*24*30);
            }
            $this->addError('username', 'Hmm... Nie udało się ustawić hasła... Zgłoś się do adminów.');
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
