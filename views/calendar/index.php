<?php

use kartik\date\DatePicker;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'Kalendarz';

$yearStart = date('Y-01-01');
$yearEnd = date('Y-12-31');
$this->registerJs(<<<JS
$("#dateYear").click(function(e) {
    e.preventDefault();
    $("#dateFrom").val("$yearStart");
    $("#dateTo").val("$yearEnd").trigger("change");
})
JS
);
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['service' => 'bimbam', 'active' => 'k']) ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['calendar/add']) ?>" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus"></span> Dodaj datę</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-5">
        <div class="form-group">
            Od: <?= DatePicker::widget([
                'name' => 'DaySearch[from]',
                'value' => $searchModel->from,
                'options' => ['class' => 'date_range', 'id' => 'dateFrom'],
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
            ]) ?>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group">
            Do: <?= DatePicker::widget([
                'name' => 'DaySearch[to]',
                'value' => $searchModel->to,
                'options' => ['class' => 'date_range', 'id' => 'dateTo'],
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
            ]) ?>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <br>
            <button class="btn btn-primary btn-block" id="dateYear">Cały rok</button>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => '.date_range',
    'columns' => [
        [
            'attribute' => 'offday',
            'filter' => false
        ],
        [
            'header' => 'Dzień',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->offday, 'eeee');
            }
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{delete}',
        ],
    ]
]); ?>
<?php Pjax::end();
