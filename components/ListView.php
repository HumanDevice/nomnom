<?php

namespace app\components;

use Yii;
use yii\helpers\Html;
use yii\widgets\ListView as YiiListView;

class ListView extends YiiListView
{
    /**
     * @var int sum of all orders.
     */
    public $sum = 0;

    /**
     * Renders all data models.
     * @return string the rendering result
     */
    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach (array_values($models) as $index => $model) {
            $key = $keys[$index];
            if (($before = $this->renderBeforeItem($model, $key, $index)) !== null) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($model, $key, $index);
            $this->sum += $model->price;

            if (($after = $this->renderAfterItem($model, $key, $index)) !== null) {
                $rows[] = $after;
            }
        }
        if ($rows) {
            $rows[] = Html::tag('tr', Html::tag('td', 'RAZEM:', ['colspan' => 3, 'class' => 'text-right'])
                . Html::tag('td', Yii::$app->formatter->asCurrency($this->sum, 'PLN'), ['class' => 'text-right']));
        }

        return implode($this->separator, $rows);
    }
}
