<?php

use app\models\Hour;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'TikTak Aktualizacja godzin';
/* @var $model Hour */

$this->registerJs(<<<JS
$(".copy").click(function(e) {
    e.preventDefault();
    var position = $(this).data("pos");
    var content = $(this).closest(SELEKTOR GRUPY).find("input").val();
    $("input[type=text]").each(function() {
        if ($(this).data("pos") >= position) {
            $(this).val(content);
        }
    });
});
JS
);
?>
<?= $this->render('/menu/user') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h1>Aktualizacja godzin</h1>
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
            <?= $form->field($model, 'vacation')->textInput()->hint('Zostaw puste albo podaj datę do kiedy jesteś na urlopie.') ?>
            <p>TYDZIEŃ NIEPARZYSTY</p>
            <?= $form->field($model, 'monday_odd')->textInput(['autofocus' => true])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 1, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'tuesday_odd')->textInput(['data-pos' => 1])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 2, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'wednesday_odd')->textInput(['data-pos' => 2])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 3, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'thursday_odd')->textInput(['data-pos' => 3])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 4, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'friday_odd')->textInput(['data-pos' => 4])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 5, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <p>TYDZIEŃ PARZYSTY</p>
            <?= $form->field($model, 'monday_even')->textInput(['data-pos' => 5])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 6, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'tuesday_even')->textInput(['data-pos' => 6])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 7, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'wednesday_even')->textInput(['data-pos' => 7])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 8, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'thursday_even')->textInput(['data-pos' => 8])
                ->hint(Html::button('<i class="glyphicon glyphicon-download-alt"></i> skopiuj poniżej', ['data-pos' => 9, 'class' => 'copy btn btn-default btn-xs'])) ?>
            <?= $form->field($model, 'friday_even')->textInput(['data-pos' => 9]) ?>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton('Aktualizuj', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>