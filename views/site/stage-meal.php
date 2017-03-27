<?php

use app\models\FoodForm;
use app\models\Order;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\MaskedInput;

$this->title = 'NomNom';

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
/* @var $order Order */
?>
<?= $this->render('/menu/user', ['service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['service' => 'nomnom']) ?>

<div class="row">
    <div class="col-lg-12">
        <h1>Zamówienie na dzień <?= date('Y/m/d') ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-warning">
            <strong>Zamknięcie wybierania za</strong><br>
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
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h3>Zamawiamy z</h3>
            <h3>
                <strong>1. <?= Html::encode($order->restaurant->name) ?></strong>:
                <?php if (!empty($order->restaurant->url)): ?>
                <?= Html::a('LINK', $order->restaurant->url, ['class' => 'btn btn-danger btn-xs', 'target' => 'restaurant']) ?>
                <?php endif ?>
                <?php if (!empty($order->restaurant->screen)): ?>
                <?= Html::a('ZDJĘCIE', '/uploads/menu/' . $order->restaurant->screen, ['class' => 'btn btn-danger btn-xs', 'target' => 'menu']) ?>
                <?php endif ?><br>
                <?php if ($order->restaurant->max > 1): ?>
                <small>Max <?= $order->restaurant->max ?> restauracj<?= $order->restaurant->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
                <?php endif; ?>
                <?php if ($order->restaurant->phone): ?>
                <small>Telefon: <?= $order->restaurant->phone ?></small>
                <?php endif; ?>
            </h3>
            <?php if (!empty($order->restaurant2)): ?>
            <h3>
                <strong>2. <?= Html::encode($order->restaurant2->name) ?></strong>:
                <?php if (!empty($order->restaurant2->url)): ?>
                <?= Html::a('LINK', $order->restaurant2->url, ['class' => 'btn btn-danger btn-xs', 'target' => 'restaurant2']) ?>
                <?php endif ?>
                <?php if (!empty($order->restaurant2->screen)): ?>
                <?= Html::a('ZDJĘCIE', '/uploads/menu/' . $order->restaurant2->screen, ['class' => 'btn btn-danger btn-xs', 'target' => 'menu2']) ?>
                <?php endif ?><br>
                <?php if ($order->restaurant2->max > 1): ?>
                <small>Max <?= $order->restaurant2->max ?> restauracj<?= $order->restaurant2->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
                <?php endif; ?>
                <?php if ($order->restaurant2->phone): ?>
                <small>Telefon: <?= $order->restaurant2->phone ?></small>
                <?php endif; ?>
            </h3>
            <?php endif ?>
            <hr>
        </div>
    </div>
</div>
<?php if (!$ordered): ?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-9">
                <?php if (!empty($order->restaurant2)): ?>
                <?= $form->field($model, 'restaurant')->radioList([
                    $order->restaurant->id => $order->restaurant->name,
                    $order->restaurant2->id => $order->restaurant2->name,
                ]) ?>
                <?php else: ?>
                <?= Html::activeHiddenInput($model, 'restaurant') ?>
                <?php endif ?>
            </div>
            <div class="col-lg-3 text-right">
                <?= $form->field($model, 'with')->dropDownList([0 => '-'] + FoodForm::withList($order->id), ['id' => 'with']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9">
                <?= $form->field($model, 'code')->textInput(['autofocus' => true])->hint('W przypadku Manufaktury podajemy zamówienie w kolejności: zupa, drugie danie, sałatki') ?>
                <?php /*= $form->field($model, 'screen')->fileInput()*/ ?>
            </div>
            <div class="col-lg-3 text-right">
                <?php if (Yii::$app->user->id != 22): ?>
                <?= $form->field($model, 'price')->widget(MaskedInput::class, ['mask' => '99.99'])->hint('Proszę pamiętać o doliczeniu ewentualnego kosztu opakowania.') ?>
                <?php else: ?>
                <?= Html::activeHiddenInput($model, 'price') ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if (Yii::$app->user->id != 22):
        $balance = Yii::$app->user->identity->balance;
        $max = $balance - 2.5 > 0 ? $balance + 20 - 2.5 : 20;
        if ($max > 99.99) {
            $max = 99.99;
        }
        ?>
        <p>Aktualne saldo: <strong><?= Yii::$app->formatter->asCurrency($balance, 'PLN') ?></strong><br>
        Powyższa kwota pozwala jednorazowo na zamówienie o łącznej wartości <?= Yii::$app->formatter->asCurrency($max, 'PLN') ?></p>
        <?php else: ?>
        <p>Aktualne saldo: <strong>&infin;</strong></p>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <?= Html::submitButton('Zamawiam', ['class' => 'btn btn-success btn-lg']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-success">
            <?php if ($ordered->author_id == Yii::$app->user->id): ?>
            <a href="<?= Url::to(['site/unorder', 'order' => $order->id]) ?>" class="btn btn-danger pull-right" data-confirm="Czy na pewno chcesz usunąć zamówienie?">Usuń zamówienie</a>
            <?php else: ?>
            <span class="btn btn-default pull-right">Zamówienie grupowe</span>
            <?php endif; ?>
            <strong>Moje zamówienie na kwotę <span class="label label-danger"><?= Yii::$app->formatter->asCurrency($ordered->price, 'PLN') ?></span></strong>:<br><br>
            <strong>Restauracja</strong>: <?= Html::encode($ordered->restaurant->name) ?>
            <?php if (!empty($ordered->code)): ?>
            <p><?= Html::encode($ordered->code) ?></p>
            <?php endif ?>
            <?php if (!empty($ordered->screen)): ?>
            <?= Html::img('/uploads/' . $ordered->author_id . '/' . $ordered->screen, ['class' => 'img-thumbnail img-responsive']) ?>
            <?php endif ?>
            <?php if (!empty($ordered->with)): ?>
            <p>Wspólnie z <?= Html::encode($ordered->withOther->username) ?></p>
            <?php endif ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'ordered',
    'viewParams' => ['ordered' => !empty($ordered)]
]) ?>