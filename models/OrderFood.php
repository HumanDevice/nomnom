<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%order_food}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $author_id
 * @property integer $restaurant_id
 * @property string $code
 * @property string $screen
 * @property integer $created_at
 * @property integer $updated_at
 * 
 * @property User $author
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
        return [TimestampBehavior::className()];
    }

    /**
     * User relation
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
    
    /**
     * Order relation
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
    /**
     * Restaurant relation
     * @return ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::className(), ['id' => 'restaurant_id']);
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
        ];
    }
    
}
