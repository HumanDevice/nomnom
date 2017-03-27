<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'BimBam Nowa Grupa';

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'p', 'service' => 'bimbam']) ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['projects/index']) ?>" class="btn btn-default btn-block">Grupy</a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['projects/add']) ?>" class="btn btn-warning btn-block">Nowa grupa</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2 class="text-center">Nowa grupa</h2>
        </div>
    </div>
</div>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'projectsToAdd')->dropDownList($projects, ['multiple' => true, 'size' => 20])->hint('Zaznacz jeden lub więcej') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'name') ?>
        <div class="form-group">
            <?= Html::submitButton('Dodaj nową grupę', ['class' => 'btn btn-primary btn-block']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end();
