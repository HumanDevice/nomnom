<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HourSearch
 *
 */
class HourSearch extends Hour
{
    public $username;
    public $division;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'string'],
            ['division', 'in', 'range' => array_keys(User::divisionLabels())],
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
    public function search($params)
    {
        $query = Hour::find()->joinWith('user u')->where(['u.deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC]
            ],
            'pagination' => false
        ]);
        $dataProvider->sort->attributes['username'] = [
            'asc' => ['u.username' => SORT_ASC],
            'desc' => ['u.username' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['division'] = [
            'asc' => ['u.division' => SORT_ASC],
            'desc' => ['u.division' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'u.username', $this->username])
            ->andFilterWhere(['u.division' => $this->division]);

        return $dataProvider;
    }

}
