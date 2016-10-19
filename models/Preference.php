<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%preference}}".
 *
 * @property integer $id
 * @property integer $restaurant_id
 * @property integer $like
 * @property integer $created_at
 * @property integer $updated_at
 * 
 * @property User $user
 * @property Restaurant $restaurant
 */
class Preference extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%preference}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restaurant_id', 'like'], 'required'],
            ['like', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nazwa',
            'url' => 'Link do menu',
        ];
    }

    /**
     * Restaurant relation
     * @return ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::className(), ['id' => 'restaurant_id']);
    }
}
