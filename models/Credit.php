<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%credit}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property int $food_id
 * @property int $updated_at
 * @property int $created_at
 *
 * @property User $user
 * @property OrderFood $food
 */
class Credit extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%credit}}';
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
            ['amount', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => 'Kwota',
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
     * Food relation
     * @return ActiveQuery
     */
    public function getFood()
    {
        return $this->hasOne(OrderFood::class, ['id' => 'food_id']);
    }
}
