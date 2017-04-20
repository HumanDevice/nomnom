<?php

use app\components\ListView;
use app\models\FoodSearch;
use app\models\Order;
use app\models\User;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->title = 'NomNom';

/* @var $order Order */

if (in_array(Yii::$app->user->id, User::BOOKKEEPER)) {
    $this->registerJs(<<<JS
$("#edit").on("show.bs.modal", function(e) {
    var button = $(e.relatedTarget);
    $("#who").text(button.data("who"));
    $("#credit").text(button.data("credit"));
    $("#food_id").val(button.data("id"));
    $("#code").val(button.data("code"));
    $("#price").val(button.data("price"));
});
JS
    );
}
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
        <div class="alert alert-info">
            <strong>Wybieranie zamknięte.</strong> Można przystąpić do zamawiania.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h3>Zamawiamy z</h3>
            <?php if (Yii::$app->user->isAdmin): ?>
            <a href="<?= Url::to(['site/notify', 'id' => $order->restaurant->id]) ?>" class="btn btn-warning pull-right">Powiadom kanał o przybyciu jedzenia z <?= Html::encode($order->restaurant->name) ?></a>
            <?php endif; ?>
            <h3>
                <strong>1. <?= Html::encode($order->restaurant->name) ?></strong>
                <?php if (!empty($order->restaurant->url)): ?>
                <?= Html::a('LINK', $order->restaurant->url, ['class' => 'btn btn-danger btn-xs', 'target' => 'restaurant']) ?>
                <?php endif; ?>
                <?php if (!empty($order->restaurant->screen)): ?>
                <?= Html::a('ZDJĘCIE', '/uploads/menu/' . $order->restaurant->screen, ['class' => 'btn btn-danger btn-xs', 'target' => 'menu']) ?>
                <?php endif; ?><br>
                <?php if ($order->restaurant->max > 1): ?>
                <small>Max <?= $order->restaurant->max ?> restauracj<?= $order->restaurant->max == 1 ? 'a' : 'e' ?> z tego miejsca</small><br>
                <?php endif; ?>
                <?php if ($order->restaurant->phone): ?>
                <small>Telefon: <?= $order->restaurant->phone ?></small>
                <?php endif; ?>
                <?php if ($order->restaurant->comment && Yii::$app->user->isAdmin): ?>
                <br><small class="text-muted"><?= $order->restaurant->comment ?></small>
                <?php endif; ?>
            </h3>
            <?php if (!empty($order->restaurant2)): ?>
            <?php if (Yii::$app->user->isAdmin): ?>
            <a href="<?= Url::to(['site/notify', 'id' => $order->restaurant2->id]) ?>" class="btn btn-warning pull-right">Powiadom kanał o przybyciu jedzenia z <?= Html::encode($order->restaurant2->name) ?></a>
            <?php endif; ?>
            <h3>
                <strong>2. <?= Html::encode($order->restaurant2->name) ?></strong>
                <?php if (!empty($order->restaurant2->url)): ?>
                <?= Html::a('LINK', $order->restaurant2->url, ['class' => 'btn btn-danger btn-xs', 'target' => 'restaurant2']) ?>
                <?php endif; ?>
                <?php if (!empty($order->restaurant2->screen)): ?>
                <?= Html::a('ZDJĘCIE', '/uploads/menu/' . $order->restaurant2->screen, ['class' => 'btn btn-danger btn-xs', 'target' => 'menu2']) ?>
                <?php endif; ?><br>
                <?php if ($order->restaurant2->max > 1): ?>
                <small>Max <?= $order->restaurant2->max ?> restauracj<?= $order->restaurant2->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
                <?php endif; ?>
                <?php if ($order->restaurant2->phone): ?>
                <small>Telefon: <?= $order->restaurant2->phone ?></small>
                <?php endif; ?>
                <?php if ($order->restaurant2->comment && Yii::$app->user->isAdmin): ?>
                <br><small class="text-muted"><?= $order->restaurant2->comment ?></small>
                <?php endif; ?>
            </h3>
            <?php endif; ?>
            <?php if (!empty($order->nextRestaurant)): ?>
            <span class="text-muted">Następna niewybrana restauracja: <?= Html::encode($order->nextRestaurant->name) ?></span>
            <?php endif; ?>
            <hr>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group"><h3><?= Html::encode($order->restaurant->name) ?></h3></div>
    </div>
</div>
<?= ListView::widget([
    'dataProvider' => (new FoodSearch)->summary($order->id, $order->restaurant_id),
    'itemView' => 'summary',
    'summary' => null,
    'options' => ['tag' => 'table', 'class' => 'table table-striped'],
    'itemOptions' => ['tag' => false]
]) ?>

<?php if (!empty($order->restaurant2)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group"><h3><?= Html::encode($order->restaurant2->name) ?></h3></div>
    </div>
</div>
<?= ListView::widget([
    'dataProvider' => (new FoodSearch)->summary($order->id, $order->restaurant2_id),
    'itemView' => 'summary',
    'summary' => null,
    'options' => ['tag' => 'table', 'class' => 'table table-striped'],
    'itemOptions' => ['tag' => false]
]) ?>
<?php endif; ?>

<?php if (in_array(Yii::$app->user->id, User::BOOKKEEPER)): ?>
<?php Modal::begin([
    'header' => '<h4 class="modal-title">Edytuj zamówienie</h4>',
    'options' => ['id' => 'edit'],

]); ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <strong>Zamawiający</strong>: <span id="who"></span>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <strong>Saldo</strong>: <span id="credit"></span>
        </div>
    </div>
</div>
<?= Html::beginForm(); ?>
<?= Html::hiddenInput('food_id', null, ['id' => 'food_id']) ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <p><strong>Zamówienie:</strong></p>
            <?= Html::textInput('code', null, ['id' => 'code', 'class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <p><strong>Kwota:</strong></p>
            <?= MaskedInput::widget([
                'id' => 'price',
                'name' => 'price',
                'value' => '00.00',
                'mask' => '99.99'
            ]) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 text-right">
        <?= Html::submitButton('Zapisz zmiany', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?= Html::endForm(); ?>
<?php Modal::end(); ?>
<?php endif;
