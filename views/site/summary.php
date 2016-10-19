<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<tr>
    <td><?= Html::encode($model->restaurant->short) ?></td>
    <?php if (!empty($model->code)): ?>
    <td><?= Html::encode($model->code) ?></td>
    <?php endif ?>
    <?php if (!empty($model->screen)): ?>
    <td><?= Html::a(Url::to(['/uploads/' . $model->author_id . '/' . $model->screen], true), Url::to(['/uploads/' . $model->author_id . '/' . $model->screen], true)) ?></td>
    <?php endif ?>
    <?php if (empty($model->code)): ?><td></td><?php endif ?>
    <?php if (empty($model->screen)): ?><td></td><?php endif ?>
</tr>