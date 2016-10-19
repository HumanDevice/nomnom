<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'NomNom Login';
?>
<h1><?= $this->title ?></h1>
<p>Jeśli nie ma Cię w systemie, zapytaj w biurze, może ktoś Cię doda.</p>

<div class="row">
    <div class="col-lg-offset-1 col-lg-11">
        <div class="form-group">
            <?= Html::a('To mój Pierwszy Raz tutaj', ['site/start'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<div class="row">
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ]) ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
