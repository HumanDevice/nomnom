<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%order_choice}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $restaurant_id
 * @property integer $author_id
 * 
 * @property Restaurant $restaurant
 * @property User $author
 */
class OrderChoice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_choice}}';
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
     * User relation
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
}
