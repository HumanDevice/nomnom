<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'NomNom Restauracje';
?>
<?= $this->render('/menu/user', ['active' => 'r', 'service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['service' => 'nomnom']) ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2>Restauracje</h2>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        'name',
        [
            'attribute' => 'url',
            'format' => 'raw',
            'value' => function ($model) {
                return !empty($model->url) ? Html::a($model->url, $model->url, ['target' => 'restaurant', 'data-pjax' => 0]) : null;
            }
        ],
        [
            'attribute' => 'screen',
            'format' => 'raw',
            'value' => function ($model) {
                return !empty($model->screen) ? Html::a('Zobacz', '/uploads/menu/' . $model->screen, ['target' => 'menu', 'class' => 'btn btn-info btn-xs', 'data-pjax' => 0]) : null;
            }
        ],
    ]
]); ?>
<?php Pjax::end();
