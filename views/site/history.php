<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'NomNom Historia';
?>
<?= $this->render('/menu/user', ['active' => 'h']) ?>
<?= $this->render('/menu/admin') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2>Historia zamówień</h2>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        [
            'attribute' => 'restaurant_name',
            'value' => function ($model) {
                return $model->restaurant->name;
            }
        ],
        'code',
        [
            'attribute' => 'is_screen',
            'filter' => [1 => 'Jest', 0 => 'Brak'],
            'format' => 'raw',
            'value' => function ($model) {
                return !empty($model->screen) ? Html::a('Zobacz', '/uploads/'
                        . Yii::$app->user->id
                        . '/'
                        . $model->screen, ['target' => 'historia', 'class' => 'btn btn-info btn-xs', 'data-pjax' => '0']) : null;
            }
        ],
        [
            'attribute' => 'price',
            'format' => ['currency', 'PLN']
        ],
        [
            'attribute' => 'with',
            'value' => function ($model) {
                return $model->withOther->username;
            }
        ],
        [
            'attribute' => 'date',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->created_at, 'yyyy-MM-dd');
            }
        ]
    ]
]); ?>
<?php Pjax::end();
