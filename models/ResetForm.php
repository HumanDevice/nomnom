<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * ResetForm
 *
 * @property User|null $user This property is read-only.
 *
 */
class ResetForm extends Model
{
    public $email;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return ['email' => 'Email'];
    }
    
    /**
     * Sends email with password reset link.
     * @return bool
     */
    public function reset()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user === false) {
                $this->addError('email', 'Nie znaleziono takiego adresu w bazie.');
                return false;
            }

            $url = Url::to(['site/newpass', 'token' => $user->auth_key], true);
            if (Yii::$app->mailer->compose()
                ->setFrom('notice@projectown.net')
                ->setTo($user->email)
                ->setSubject('Reset hasła w NomNomie')
                ->setTextBody("Kliknij w link poniżej, żeby zresetować hasło:\n\n{$url}\n\nNomNom")
                ->setHtmlBody("Kliknij w link poniżej, żeby zresetować hasło:<br><br><a href=\"{$url}\">{$url}</a><br><br>NomNom")
                ->send()) {
                return true;
            } else {
                $this->addError('email', 'Wystąpił błąd podczas wysyłania emaila.');
            }
        }
        return false;
    }

    /**
     * Finds user by email
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $user = User::findByEmail($this->email);
            if ($user) {
                $this->_user = $user;
            }
        }
        return $this->_user;
    }
}
