<?php

use app\models\Restaurant;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'NomNom';

$allRestaurants = Restaurant::getDetailedList();

if ($order->stage_end > time()) {
    $this->registerJs(<<<JS
function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = t < 0 ? 0 : Math.floor((t / 1000) % 60);
    var minutes = t < 0 ? 0 : Math.floor((t / 1000 / 60) % 60);
    var hours = t < 0 ? 0 : Math.floor((t / (1000 * 60 * 60)) % 24);
    return {
        'total': t,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}
function initializeClock(id, endtime) {
    var clock = document.getElementById(id);
    var hoursSpan = clock.querySelector('.hours');
    var minutesSpan = clock.querySelector('.minutes');
    var secondsSpan = clock.querySelector('.seconds');
        
    function updateClock() {
        var t = getTimeRemaining(endtime);

        hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
        minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
        secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

        if (t.total <= 0) {
            clearInterval(timeinterval);
        }
    }

    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
}

var deadline = new Date({$order->stage_end} * 1000);
initializeClock('clockdiv', deadline);
JS
);
}
if ($order->stage_end < time() && $order->admin_id == Yii::$app->user->id) {
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
}

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin') ?>

<div class="row">
    <div class="col-lg-12">
        <p class="pull-right">Zamówienie otworzył <span class="label label-info"><?= Html::encode($order->admin->username) ?></span></p>
        <h1>Zamówienie na dzień <?= date('Y/m/d') ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php if ($order->stage_end > time()): ?>
        <div class="alert alert-warning">
            <strong>Zamknięcie głosowania za</strong><br>
            <div class="text-center">
                <div id="clockdiv">
                    <div>
                        <span class="hours"></span>
                        <div class="smalltext">Godziny</div>
                    </div>
                    <div>
                        <span class="minutes"></span>
                        <div class="smalltext">Minuty</div>
                    </div>
                    <div>
                        <span class="seconds"></span>
                        <div class="smalltext">Sekundy</div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <strong>Głosowanie zamknięte.</strong> Admin za chwilę uruchomi etap wyboru posiłku.
        </div>
        <?php endif ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h3>Wybieramy restaurację</h3>
    </div>
</div>
<?php if ($order->stage_end < time() && $order->admin_id == Yii::$app->user->id): ?>
<?php $form = ActiveForm::begin(); ?>
<?php endif ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <?php if ($order->stage_end < time() && $order->admin_id == Yii::$app->user->id): ?>
                    <th class="text-center danger">Dzisiaj jemy w (max 2)</th>
                    <?php endif ?>
                    <?php if ($order->admin_id == Yii::$app->user->id): ?>
                    <th class="text-center">Ilość głosów</th>
                    <?php endif ?>
                    <th>Nazwa restauracji</th>
                    <th>Link do menu</th>
                    <th>Zdjęcie menu</th>
                    <th></th>
                </tr>
                <?php foreach ($order->votesList as $id => $restaurant): ?>
                <tr>
                    <?php if ($order->stage_end < time() && $order->admin_id == Yii::$app->user->id): ?>
                    <td class="text-center danger clickable"><?= Html::activeCheckbox($model, 'restaurant[]', ['value' => $id, 'label' => false, 'uncheck' => null]) ?></td>
                    <?php endif ?>
                    <?php if ($order->admin_id == Yii::$app->user->id): ?>
                    <td class="text-center">
                        <span class="badge"><?= $restaurant['votes'] ?></span>
                    </td>
                    <?php endif ?>
                    <td><?= Html::encode($restaurant['name']) ?></td>
                    <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                    <td class="text-right">
                        <?php if (!$voted && $order->stage_end > time()): ?>
                        <a href="<?= Url::to(['site/vote', 'restaurant' => $id, 'order' => $order->id]) ?>" class="btn btn-success">Głosuję na tę restaurację</a>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
<?php if ($order->stage_end < time() && $order->admin_id == Yii::$app->user->id): ?>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group text-right">
            Wybór posiłku do godziny
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
            <?= Html::submitButton('Wybierz restaurację i przejdź do etapu wyboru posiłków', ['class' => 'btn btn-primary btn-block btn-lg', 'data-confirm' => 'Czy na pewno uruchomić wybór posiłku z wybranymi opcjami?']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php endif ?>
<?php if (!$voted && $order->stage_end > time()): ?>
<div class="row" id="listaRestauracji">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <th>Nazwa restauracji</th>
                    <th>Link do menu</th>
                    <th>Zdjęcie menu</th>
                    <th>Wszyscy mogą stąd zamawiać</th>
                    <th></th>
                </tr>
                <?php foreach ($allRestaurants as $id => $restaurant): ?>
                <tr class="<?= $restaurant['like'] == 0 ? 'warning' : '' ?>">
                    <td><?= Html::encode($restaurant['name']) ?></td>
                    <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                    <td><?= $restaurant['like'] ? 'Tak' : 'Nie' ?></td>
                    <td class="text-right">
                        <a href="<?= Url::to(['site/vote', 'restaurant' => $id, 'order' => $order->id]) ?>" class="btn btn-success">Głosuję na tę restaurację</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
<?php elseif ($order->stage_end > time()): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group text-right">
            <a href="<?= Url::to(['site/unvote', 'order' => $order->id]) ?>" class="btn btn-lg btn-danger" data-confirm="Czy na pewno chcesz usunąć swój głos?">Chcę usunąć mój głos</a>
        </div>
    </div>
</div>
<?php endif;
