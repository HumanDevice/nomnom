<?php

use app\models\Restaurant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'NomNom Otwarcie sesji';
$allRestaurants = Restaurant::getDetailedList();

$this->registerJs(<<<JS
jQuery(".clickable input[type=radio]").on('click touch', function (e) {
    e.stopPropagation();
});   
jQuery(".clickable").on('click touch', function () {
    jQuery(this).find("input[type=radio]").prop("checked", true);
});
JS
);

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'o']) ?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-lg-12">
        <h1>Zamówienie na dzień <?= date('Y/m/d') ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="form-group text-right">
            Wybór restauracji do godziny
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <?= Html::activeDropDownList($model, 'hour', $model->hours, ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <?= Html::activeDropDownList($model, 'minute', $model->minutes, ['class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <th class="text-center">Sugestia admina</th>
                    <th>Nazwa restauracji</th>
                    <th>Link do menu</th>
                    <th>Zdjęcie menu</th>
                    <th>Wszyscy mogą stąd zamawiać</th>
                </tr>
                <?php foreach ($allRestaurants as $id => $restaurant): ?>
                <tr class="<?= $restaurant['like'] == 0 ? 'warning' : '' ?>">
                    <td class="text-center clickable"><?= Html::activeRadio($model, 'restaurant', ['value' => $id, 'label' => false, 'uncheck' => null]) ?></td>
                    <td><?= Html::encode($restaurant['name']) ?></td>
                    <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                    <td><?= $restaurant['like'] ? 'Tak' : 'Nie' ?></td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::submitButton('Rozpocznij głosowanie', ['class' => 'btn btn-primary btn-block btn-lg', 'data-confirm' => 'Czy na pewno otworzyć zamówienie z wybranymi opcjami?']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
