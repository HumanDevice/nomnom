<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['action' => ['projects/index']]); ?>
<?= Html::activeHiddenInput($model, 'id') ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'projectsToAdd')->dropDownList($projects, ['multiple' => true, 'size' => 20])->hint('Zaznacz jeden lub więcej') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'name') ?>
        <div class="form-group">
            <?= Html::submitButton('Zapisz grupę', ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <div class="form-group">
            <?= Html::a('Usuń grupę', ['projects/delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-block', 'data-confirm' => 'Czy na pewno skasować grupę?']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end();
