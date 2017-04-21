<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Kalendarz Dodanie daty';

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['service' => 'bimbam', 'active' => 'k']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h1>Dodanie dnia wolnego</h1>
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
            <?= $form->field($model, 'offday')->widget(DatePicker::class, ['pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd']]) ?>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>