<?php

use app\models\PreferencesForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'NomNomAdmin Preferencje restauracji';

$this->registerJs(<<<JS
jQuery(".clickable input[type=checkbox]").on('click touch', function (e) {
    e.stopPropagation();
});   
jQuery(".clickable").on('click touch', function () {
    var checkBox = jQuery(this).find("input[type=checkbox]");
    checkBox.prop("checked", !checkBox.prop("checked"));
});
JS
);
/* @var $model PreferencesForm */
?>
<?= $this->render('/menu/user', ['service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['active' => 'r', 'service' => 'nomnom']) ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['restaurants/index']) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> Lista restauracji</a>
        </div>
    </div>
</div>

<?php $form = ActiveForm::begin(); ?>
    <table class="table table-hover">
        <tr>
            <th>Nazwa</th>
            <th>Link do menu</th>
            <th>Zdjęcie menu</th>
            <th class="text-center">Wszyscy mogą tu zamawiać</th>
        </tr>
        <?php foreach ($allRestaurants as $id => $restaurant): ?>
        <tr class="<?= !$model->restaurants[$id] ? 'danger' : '' ?>">
            <td><?= Html::encode($restaurant['name']) ?></td>
            <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
            <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'menu', 'class' => 'btn btn-info btn-xs']) : null ?></td>
            <td class="text-center clickable"><?= Html::activeCheckbox($model, 'restaurants[' . $id . ']', ['label' => false]) ?></td>
        </tr>
        <?php endforeach ?>
    </table>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary btn-block btn-lg']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
