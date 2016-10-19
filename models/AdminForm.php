<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * AdminForm
 *
 */
class AdminForm extends Model
{
    public $msg;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['msg', 'required'],
            ['msg', 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'msg' => 'Wiadomość'
        ];
    }
    
    /**
     * Adds order.
     */
    public function send()
    {
        if ($this->validate()) {
            return Yii::$app->hipchat->send($this->msg, 'yellow');
        }
    }
}
