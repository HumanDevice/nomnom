<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch
 * 
 */
class UserSearch extends User
{
    public $virgin;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role', 'virgin'], 'integer'],
            [['username'], 'string'],
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
        $query = User::find()->where(['deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['role' => $this->role])
            ->andFilterWhere(['like', 'username', $this->username]);
        
        if ($this->virgin === '0') {
            $query->andWhere(['password_hash' => null]);
        }
        if ($this->virgin === '1') {
            $query->andWhere(['is not', 'password_hash', null]);
        }

        return $dataProvider;
    }

}
