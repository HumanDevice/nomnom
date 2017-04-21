<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DaySearch
 */
class DaySearch extends Calendar
{
    /**
     * @var string date from
     */
    public $from;
    /**
     * @var string date to
     */
    public $to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'date', 'format' => 'y-MM-dd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'from' => 'Od',
            'to' => 'Do',
        ]);
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
    public function search($params)
    {
        $query = Calendar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['offday' => SORT_ASC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->from) {
            $query->andFilterWhere(['>=', 'offday', Yii::$app->formatter->asDate(date_create($this->from, timezone_open('Europe/Warsaw')), 'y-MM-dd')]);
        }
        if ($this->to) {
            $query->andFilterWhere(['<=', 'offday', Yii::$app->formatter->asDate(date_create($this->to, timezone_open('Europe/Warsaw')), 'y-MM-dd')]);
        }

        return $dataProvider;
    }
}
