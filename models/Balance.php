<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%balance}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $food_id
 * @property string $value
 * @property int $operator_id
 * @property int $updated_at
 * @property int $created_at
 *
 * @property User $user
 * @property OrderFood $food
 */
class Balance extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%balance}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'food_id' => 'Zamówienie',
            'value' => 'Kwota',
            'date' => 'Data',
        ];
    }

    /**
     * User relation
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * OrderFood relation
     * @return ActiveQuery
     */
    public function getFood()
    {
        return $this->hasOne(OrderFood::class, ['id' => 'food_id']);
    }
}
