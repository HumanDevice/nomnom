<?php

use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($ordered)) {
    $ordered = true;
}

?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php if (!$ordered && empty($model->with) && $model->author_id != Yii::$app->user->id): ?>
                <a href="<?= Url::to(['site/order', 'food' => $model->id]) ?>" class="btn btn-success btn-xs pull-right">Chcę dokładnie to samo</a>
                <?php endif ?>
                <?= Html::encode($model->author->username) ?>
            </div>
            <div class="panel-body">
                <strong>Restauracja</strong>: <?= Html::encode($model->restaurant->name) ?><br>
                <?php if (!empty($model->code)): ?>
                <p><?= Html::encode($model->code) ?></p>
                <p><strong>Kwota: <?= Yii::$app->formatter->asCurrency($model->price, 'PLN') ?></strong></p>
                <?php endif ?>
                <?php if (!empty($model->screen)): ?>
                <?= Html::img('/uploads/' . $model->author_id . '/' . $model->screen, ['class' => 'img-thumbnail img-responsive']) ?>
                <?php endif ?>
                <?php if (!empty($model->with)): ?>
                <p>Wspólnie z <?= Html::encode($model->withOther->username) ?></p>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>