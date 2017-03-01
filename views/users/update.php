<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'NomNomAdmin Użytkownicy';
/* @var $model User */
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'u']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['users/index']) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> Lista użytkowników</a>
            <?php if (!$model->isNewRecord): ?>
            <a href="<?= Url::to(['users/view', 'id' => $model->id]) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-eye-open"></span> Podgląd użytkownika</a>
            <a href="<?= Url::to(['users/delete', 'id' => $model->id]) ?>" class="btn btn-danger" data-confirm="Czy na pewno chcesz usunąć tego użytkownika?">
                <span class="glyphicon glyphicon-trash"></span> Usuń użytkownika
            </a>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <?php if ($model->isNewRecord): ?>
            <h1>Nowy użytkownik</h1>
            <?php else: ?>
            <h1>Edycja użytkownika</h1>
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
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'role')->radioList([User::ROLE_USER => 'Pracownik', User::ROLE_ADMIN => 'Admin']) ?>
            <?= $form->field($model, 'division')->dropDownList(User::divisionLabels()) ?>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>