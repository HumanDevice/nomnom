<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BalanceSearch
 *
 */
class BalanceSearch extends Hour
{
    public $date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'number'],
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
     * @param int $owner
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($owner, $params)
    {
        $query = Balance::find()->where(['user_id' => $owner]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);
        $dataProvider->sort->attributes['date'] = [
            'asc' => ['created_at' => SORT_ASC],
            'desc' => ['created_at' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['value' => $this->value]);
        if ($this->date) {
            $query
                ->andWhere(['>=', OrderFood::tableName(). '.created_at', (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y-m-d H:i:s', $this->date . '00:00:00', new DateTimeZone('Europe/Warsaw'))
                    )])
                ->andWhere(['<=', OrderFood::tableName(). '.created_at', (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y-m-d H:i:s', $this->date . '23:59:59', new DateTimeZone('Europe/Warsaw'))
                    )]);
        }

        return $dataProvider;
    }

}
