<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<tr>
    <td><?= Html::encode($model->author->short) ?><?= !empty($model->with) ? ' + ' . $model->withOther->short : '' ?></td>
    <?php if (!empty($model->code)): ?>
    <td><?= Html::encode($model->code) ?></td>
    <?php endif; ?>
    <?php if (!empty($model->screen)): ?>
    <td><?= Html::a(Url::to(['/uploads/' . $model->author_id . '/' . $model->screen], true), Url::to(['/uploads/' . $model->author_id . '/' . $model->screen], true)) ?></td>
    <?php endif; ?>
    <?php if (empty($model->code)): ?><td></td><?php endif; ?>
    <?php if (empty($model->screen)): ?><td></td><?php endif; ?>
    <td class="text-right"><?= Yii::$app->formatter->asCurrency($model->price, 'PLN') ?></td>
    <?php if (in_array(Yii::$app->user->id, User::BOOKKEEPER)): ?>
    <td class="text-right">
        <?php if (!$model->balanced): ?>
            <button
                class="btn btn-primary btn-xs"
                data-toggle="modal"
                data-target="#edit"
                data-id="<?= $model->id ?>"
                data-who="<?= Html::encode($model->author->short) ?><?= !empty($model->with) ? ' + ' . $model->withOther->short : '' ?>"
                data-code="<?= Html::encode($model->code) ?>"
                data-price="<?= Html::encode($model->price) ?>"
                data-credit="<?= $model->author->balance ?>">
                <i class="glyphicon glyphicon-edit"></i> edytuj
            </button>
        <?php endif; ?>
        <?php if (empty($model->with) && $model->price > 20 || !empty($model->with) && $model->price > 40): ?>
            <?php
                if (empty($model->with)) {
                    $debet = number_format($model->price - 20 + 2.5, 2);
                } else {
                    $debet = number_format($model->price - 40 + 2.5, 2);
                }
            ?>
            <?php if (!$model->balanced): ?>
                <a href="<?= Url::to(['site/debet', 'id' => $model->id]) ?>" class="btn btn-success btn-xs" data-confirm="Czy na pewno odjąć -<?= Yii::$app->formatter->asCurrency($debet, 'PLN') ?> pracownikowi?">
                    <i class="glyphicon glyphicon-question-sign"></i> potwierdź -<?= Yii::$app->formatter->asCurrency($debet, 'PLN') ?>
                </a>
            <?php else: ?>
                <button class="btn btn-default btn-xs"><i class="glyphicon glyphicon-ok-sign"></i> potwierdzone -<?= Yii::$app->formatter->asCurrency($debet, 'PLN') ?></button>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <?php endif; ?>
</tr>