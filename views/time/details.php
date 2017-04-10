<?php

use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'BimBam Raport czasowy';

/* @var $this View */
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 't', 'service' => 'bimbam']) ?>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <a href="<?= Url::to(['time/index']) ?>" class="btn btn-default btn-block">Raport zbiorczy</a>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <a href="<?= Url::to(['time/details']) ?>" class="btn btn-warning btn-block">Raport szczegółowy</a>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <a href="<?= Url::to(['time/csv']) ?>" class="btn btn-success btn-block">Pobierz CSV z poprzedniego miesiąca</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2 class="text-center">Raport szczegółowy</h2>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            Od: <?= DateTimePicker::widget([
                'name' => 'TimeSearch[from]',
                'value' => $searchModel->from,
                'options' => ['class' => 'date_range'],
                'pluginOptions' => ['autoclose' => true],
            ]) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            Do: <?= DateTimePicker::widget([
                'name' => 'TimeSearch[to]',
                'value' => $searchModel->to,
                'options' => ['class' => 'date_range'],
                'pluginOptions' => ['autoclose' => true],
            ]) ?>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<div class="row">
    <div class="col-lg-12 text-center">
        <div class="form-group">
            <h3><i class="glyphicon glyphicon-time"></i> Łączny czas dla wybranych dat: <strong><?= $searchModel->formatSummary($searchModel->summary) ?></strong></h3>
        </div>
    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => '.date_range',
    'columns'      => [
        [
            'attribute' => 'group_id',
            'contentOptions' => ['class' => 'small'],
            'filter' => $groups,
            'value' => function ($model) {
                return $model->group_names;
            }
        ],
        [
            'attribute' => 'project_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'placeholder' => 'ID'],
            'value' => function ($model) {
                return Html::tag('span', $model->project_id, ['class' => 'badge'])
                    . Html::tag(
                        'div',
                        Html::a(substr($model->project->url, 32, strrpos($model->project->url, '/') - 32), $model->project->url, ['class' => 'btn btn-xs btn-primary', 'target' => 'projekt'])
                         . Html::a($model->project->name, $model->project->url, ['class' => 'btn btn-xs btn-default', 'target' => 'projekt']),
                        ['class' => 'btn-group pull-right']
                    );
            }
        ],
        [
            'attribute' => 'user_id',
            'filter' => $users,
            'value' => function ($model) use ($users) {
                return isset($users[$model->user_id]) ? $users[$model->user_id] : $model->user_id;
            }
        ],
        [
            'attribute' => 'issue_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'placeholder' => 'ID'],
            'value' => function ($model) {
                if (empty($model->issue_id)) {
                    return null;
                }
                return Html::a('link', $model->project->url . '/issues/' . $model->issue_id, ['class' => 'btn btn-xs btn-primary pull-right', 'target' => 'ticket'])
                    . Html::tag('span', $model->issue_id, ['class' => 'badge']);
            }
        ],
        [
            'attribute' => 'seconds',
            'value' => function ($model) {
                return $model->formatSummary($model->seconds);
            }
        ],
        [
            'attribute' => 'description',
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime'
        ],
    ]
]); ?>
<?php Pjax::end();
