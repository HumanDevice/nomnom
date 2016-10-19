<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Witamy w NomNom';
?>
<h1><?= $this->title ?></h1>
<p>Jeśli masz już hasło, przejdź do <a href="<?= Url::to(['site/login']) ?>">logowania</a>.</p>

<?php $form = ActiveForm::begin([
    'id' => 'start-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->textInput() ?>
    <div class="col-lg-offset-1 col-lg-11"><div class="form-group text-muted small">* aby ukryć hasło przed podglądaczami, wyłącz monitor</div></div>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Ustaw hasło', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

