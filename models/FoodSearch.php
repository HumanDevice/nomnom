<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FoodSearch
 * 
 */
class FoodSearch extends Model
{
    /**
     * Creates data provider instance with search query applied
     * @param int $order
     * @return ActiveDataProvider
     */
    public function search($order)
    {
        $query = OrderFood::find()->where(['order_id' => $order]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['restaurant_id' => SORT_ASC, 'id' => SORT_DESC]
            ],
            'pagination' => false
        ]);

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     * @param int $order
     * @param int $restaurant
     * @return ActiveDataProvider
     */
    public function summary($order, $restaurant)
    {
        $query = OrderFood::find()->where([
            'order_id' => $order,
            'restaurant_id' => $restaurant,
        ])->orderBy(['code' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $dataProvider;
    }
}
