<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'NomNomAdmin Historia';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'h']) ?>

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
            'class' => SerialColumn::class
        ],
        [
            'attribute' => 'date',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->created_at, 'yyyy-MM-dd');
            }
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{view}',
            'buttons' => function ($url) {
                return Html::a('Podgląd zamówienia', $url, ['class' => 'btn btn-primary btn-xs']);
            }
        ],
    ]
]); ?>
<?php Pjax::end();
