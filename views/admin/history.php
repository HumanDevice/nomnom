<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
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
            'class' => SerialColumn::className()
        ],
        [
            'attribute' => 'date',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->created_at, 'yyyy-MM-dd');
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{view}'
        ],
    ]
]); ?>
<?php Pjax::end();
