<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'NomNom Saldo';
?>
<?= $this->render('/menu/user', ['active' => 'a']) ?>
<?= $this->render('/menu/admin') ?>

<h3>Balans konta</h3>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            Aktualny stan konta
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <span class="badge"><?= Yii::$app->formatter->asCurrency(Yii::$app->user->identity->balance, 'PLN') ?></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <div class="alert alert-info">
                <i class="glyphicon glyphicon-info-sign"></i> Aby zwiększyć saldo należy wpłacić minimum 50 zł na konto <kbd>67 1090 2398 0000 0001 3405 3249</kbd>
            </div>
        </div>
    </div>
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
            'value' => function ($model) {
                return empty($model->food_id)
                    ? null
                    : Yii::$app->formatter->asDate($model->food->created_at);
            }
        ]
    ]
]); ?>
<?php Pjax::end(); ?>

    </div>
</div>
