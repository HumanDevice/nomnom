<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = 'NomNomAdmin Restauracje';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'r']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['restaurants/index']) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> Lista restauracji</a>
            <a href="<?= Url::to(['restaurants/update', 'id' => $model->id]) ?>" class="btn btn-success"><span class="glyphicon glyphicon-edit"></span> Edytuj restaurację</a>
            <a href="<?= Url::to(['restaurants/delete', 'id' => $model->id]) ?>" class="btn btn-danger" data-confirm="Czy na pewno chcesz usunąć tę restaurację?">
                <span class="glyphicon glyphicon-trash"></span> Usuń restaurację
            </a>
        </div>
    </div>
</div>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'name',
            'value' => $model->restaurantName
        ],
        [
            'attribute' => 'url',
            'format' => 'raw',
            'value' => !empty($model->url) ? Html::a($model->url, $model->url, ['target' => 'restaurant']) : null
        ],
        'phone',
        [
            'attribute' => 'screen',
            'format' => 'raw',
            'value' => !empty($model->screen) ? Html::a('Zobacz', '/uploads/menu/' . $model->screen, ['target' => 'menu']) : null
        ],
    ]
]); ?>

<?php if (!empty($model->screen)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <img src="/uploads/menu/<?= $model->screen ?>" alt="" class="img-thumbnail img-responsive">
        </div>
    </div>
</div>
<?php endif; ?>
