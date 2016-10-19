<?php

use app\models\Restaurant;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'NomNomAdmin Restauracje';
/* @var $model Restaurant */
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'r']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['restaurants/index']) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> Lista restauracji</a>
            <?php if (!$model->isNewRecord): ?>
            <a href="<?= Url::to(['restaurants/view', 'id' => $model->id]) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-eye-open"></span> Podgląd restauracji</a>
            <a href="<?= Url::to(['restaurants/delete', 'id' => $model->id]) ?>" class="btn btn-danger" data-confirm="Czy na pewno chcesz usunąć tę restaurację?">
                <span class="glyphicon glyphicon-trash"></span> Usuń restaurację
            </a>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <?php if ($model->isNewRecord): ?>
            <h1>Nowa restauracja</h1>
            <?php else: ?>
            <h1>Edycja restauracji</h1>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
        ]); ?>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'url') ?>
            <?= $form->field($model, 'screen')->fileInput() ?>
            <?php if (!$model->isNewRecord && !empty($model->screen)): ?>
            Zdjęcie: <?= Html::a($model->screen, '/uploads/menu/' . $model->screen, ['target' => 'menu']) ?>
            <?= $form->field($model, 'stay')->checkbox() ?>
            <?php endif ?>
            <?= $form->field($model, 'max') ?>
            <?php if ($model->isNewRecord): ?>
            <?= $form->field($model, 'preferred')->checkbox() ?>
            <?php endif ?>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>