<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'NomNom Reset hasła';
?>
<h1><?= $this->title ?></h1>
<p>Reset hasła bez współpracy administratora jest możliwy tylko dla kont z dodanym adresem email.</p>

<div class="row">
<?php $form = ActiveForm::begin([
    'id' => 'reset-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Resetuj', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
