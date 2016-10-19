<?php

namespace app\models;

use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HistorySearch
 * 
 */
class HistorySearch extends OrderFood
{
    public $is_screen;
    public $restaurant_name;
    public $date;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['is_screen', 'boolean'],
            [['code', 'restaurant_name'], 'string'],
            ['date', 'date', 'format' => 'yyyy-MM-dd'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }
    
    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function history($params)
    {
        $query = OrderFood::find()
                ->where(['author_id' => Yii::$app->user->id])
                ->joinWith(['restaurant']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC],
            ]
        ]);
        
        $dataProvider->sort->attributes['date'] = [
            'asc' => ['created_at' => SORT_ASC],
            'desc' => ['created_at' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['restaurant_name'] = [
            'asc' => ['restaurant.name' => SORT_ASC],
            'desc' => ['restaurant.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'restaurant.name', $this->restaurant_name]);
        if ($this->date) {
            $query
                ->andWhere(['>=', OrderFood::tableName(). '.created_at', (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y-m-d H:i:s', $this->date . '00:00:00', new DateTimeZone('Europe/Warsaw'))
                    )])
                ->andWhere(['<=', OrderFood::tableName(). '.created_at', (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y-m-d H:i:s', $this->date . '23:59:59', new DateTimeZone('Europe/Warsaw'))
                    )]);
        }
        if ($this->is_screen === '0') {
            $query->andWhere([OrderFood::tableName(). '.screen' => null]);
        } elseif ($this->is_screen === '1') {
            $query->andWhere(['is not', OrderFood::tableName(). '.screen', null]);
        }

        return $dataProvider;
    }
}
