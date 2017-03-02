<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%order_food}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $author_id
 * @property int $restaurant_id
 * @property string $code
 * @property string $screen
 * @property string $price
 * @property int $with
 * @property int $balanced
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $author
 * @property User $withOther
 * @property Order $order
 * @property Restaurant $restaurant
 */
class OrderFood extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_food}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * User relation
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * User relation
     * @return ActiveQuery
     */
    public function getWithOther()
    {
        return $this->hasOne(User::class, ['id' => 'with']);
    }

    /**
     * Order relation
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * Restaurant relation
     * @return ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::class, ['id' => 'restaurant_id']);
    }

    /**
     * Labels.
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'ID zamówienia',
            'is_screen' => 'Screenshot',
            'code' => 'Opis',
            'restaurant_name' => 'Restauracja',
            'date' => 'Data',
            'price' => 'Kwota',
            'with' => 'Wspólnie z',
        ];
    }

}
