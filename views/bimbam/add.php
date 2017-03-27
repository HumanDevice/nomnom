<?php

use app\models\Time;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'BimBam Dodanie czasu';
/* @var $model Time */

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['service' => 'bimbam']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h1>Dodanie czasu</h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-6\">{hint}</div>\n<div class=\"col-lg-6\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
        ]); ?>
            <?= $form->field($model, 'project_id')->widget(Select2::class, [
                'data' => $data,
                'options' => ['placeholder' => 'Wybierz projekt...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
            <?= $form->field($model, 'date')->widget(DatePicker::class) ?>
            <?= $form->field($model, 'time')->widget(MaskedInput::class, ['mask' => '99:99'])->hint('Format HH:MM') ?>
            <?= $form->field($model, 'description') ?>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>