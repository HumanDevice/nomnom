<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

$this->title = 'NomNomAdmin Saldo';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'u']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['users/update', 'id' => $model->id]) ?>" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i> Edytuj użytkownika</a>
            <a href="<?= Url::to(['users/delete', 'id' => $model->id]) ?>" class="btn btn-danger btn-xs" data-confirm="Czy na pewno chcesz usunąć tego użytkownika?">
                <i class="glyphicon glyphicon-trash"></i> Usuń użytkownika
            </a>
        </div>
    </div>
</div>

<h3>Balans konta <?= Html::encode($model->username) ?></h3>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            Aktualny stan konta
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <?= Yii::$app->formatter->asCurrency($model->balance, 'PLN') ?>
        </div>
    </div>
</div>
<div class="row">
    <?= Html::beginForm() ?>
        <div class="col-lg-3">
            <div class="form-group">
                Zasil konto
            </div>
        </div>
        <div class="col-lg-8">
            <div class="form-group">
                <?= MaskedInput::widget([
                    'name' => 'value',
                    'mask' => '999.99',
                ]) ?>
            </div>
        </div>
        <div class="col-lg-1">
            <div class="form-group">
                <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary', 'data-confirm' => 'Czy na pewno zasilić konto podaną kwotą?']) ?>
            </div>
        </div>
    <?= Html::endForm() ?>
</div>

<div class="row">
    <div class="col-lg-12">

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        [
            'attribute' => 'value',
            'format' => ['currency', 'PLN'],
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'date',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->created_at, 'yyyy-MM-dd');
            }
        ],
        [
            'attribute' => 'food_id',
            'format' => 'raw',
            'value' => function ($model) {
                return empty($model->food_id)
                    ? null
                    : Html::a('<i class="glyphicon glyphicon-cutlery"></i>', ['admin/view', 'id' => $model->food->order_id], ['data-pjax' => 0]);
            }
        ]
    ]
]); ?>
<?php Pjax::end(); ?>

    </div>
</div>
