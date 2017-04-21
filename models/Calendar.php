<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * "{{%calendar}}".
 *
 * @property string $offday
 */
class Calendar extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%calendar}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'offday' => 'Dzień wolny od pracy',
        ];
    }
}
