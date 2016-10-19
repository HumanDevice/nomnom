<?php

use app\models\FoodSearch;
use app\models\Order;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'NomNom';

/* @var $order Order */
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
        <div class="alert alert-info">
            <?php if ($order->admin_id == Yii::$app->user->id): ?>
            <a href="<?= Url::to(['site/close']) ?>" class="btn btn-warning pull-right btn-lg" data-confirm="Czy na pewno chcesz zamknąć?">Potwierdź zamówienie i zamknij</a>
            <?php endif ?>
            <strong>Wybieranie zamknięte.</strong> Admin przystępuje do zamawiania.
        </div>
    </div>
</div>
<?php if ($order->admin_id == Yii::$app->user->id): ?>
<div class="row">
    <div class="col-lg-12">
        Wyślij wiadomość na HipChat przez bota:
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($admin, 'msg')->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton('Niechaj leci', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php endif ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h3>Zamawiamy z</h3>
            <h3>
                <strong>1. <?= Html::encode($order->restaurant->name) ?></strong>: 
                <?php if (!empty($order->restaurant->url)): ?>
                <?= Html::a('LINK DO MENU', $order->restaurant->url, ['class' => 'btn btn-danger', 'target' => 'restaurant']) ?>
                <?php endif ?>
                <?php if (!empty($order->restaurant->screen)): ?>
                <?= Html::a('ZDJĘCIE MENU', '/uploads/menu/' . $order->restaurant->screen, ['class' => 'btn btn-danger', 'target' => 'menu']) ?>
                <?php endif ?>
                <small>Max <?= $order->restaurant->max ?> restauracj<?= $order->restaurant->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
            </h3>
            <?php if (!empty($order->restaurant2)): ?>
            <h3>
                <strong>2. <?= Html::encode($order->restaurant2->name) ?></strong>: 
                <?php if (!empty($order->restaurant2->url)): ?>
                <?= Html::a('LINK DO MENU', $order->restaurant2->url, ['class' => 'btn btn-danger', 'target' => 'restaurant2']) ?>
                <?php endif ?>
                <?php if (!empty($order->restaurant2->screen)): ?>
                <?= Html::a('ZDJĘCIE MENU', '/uploads/menu/' . $order->restaurant2->screen, ['class' => 'btn btn-danger', 'target' => 'menu2']) ?>
                <?php endif ?>
                <small>Max <?= $order->restaurant2->max ?> restauracj<?= $order->restaurant2->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
            </h3>
            <?php endif ?>
            <hr>
        </div>
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
<?= ListView::widget([
    'dataProvider' => (new FoodSearch)->summary($order->id, $order->restaurant2_id),
    'itemView' => 'summary',
    'summary' => null,
    'options' => ['tag' => 'table', 'class' => 'table table-striped'],
    'itemOptions' => ['tag' => false]
]) ?>
<?php endif;
