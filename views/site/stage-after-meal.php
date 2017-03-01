<?php

use app\models\FoodSearch;
use app\models\Order;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'NomNom';

/* @var $order Order */
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin') ?>

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
            </h3>
            <?php if (!empty($order->restaurant2)): ?>
            <h3>
                <strong>2. <?= Html::encode($order->restaurant2->name) ?></strong>
                <?php if (!empty($order->restaurant2->url)): ?>
                <?= Html::a('LINK', $order->restaurant2->url, ['class' => 'btn btn-danger', 'target' => 'restaurant2']) ?>
                <?php endif; ?>
                <?php if (!empty($order->restaurant2->screen)): ?>
                <?= Html::a('ZDJĘCIE', '/uploads/menu/' . $order->restaurant2->screen, ['class' => 'btn btn-danger', 'target' => 'menu2']) ?>
                <?php endif; ?><br>
                <?php if ($order->restaurant2->max > 1): ?>
                <small>Max <?= $order->restaurant2->max ?> restauracj<?= $order->restaurant2->max == 1 ? 'a' : 'e' ?> z tego miejsca</small>
                <?php endif; ?>
                <?php if ($order->restaurant2->phone): ?>
                <small>Telefon: <?= $order->restaurant2->phone ?></small>
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
<?php endif;
