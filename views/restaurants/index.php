<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'NomNomAdmin Restauracje';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'r']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['admin/preferences']) ?>" class="btn btn-default btn-lg pull-right"><span class="glyphicon glyphicon-cog"></span> Preferencje restauracji</a>
            <a href="<?= Url::to(['restaurants/create']) ?>" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj restaurację</a>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        [
            'attribute' => 'id',
            'headerOptions' => ['class' => 'col-sm-1']
        ],
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
        ['class' => ActionColumn::className()],
    ]
]); ?>
<?php Pjax::end();
