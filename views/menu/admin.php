<?php

use app\models\Order;
use yii\helpers\Url;

if (!isset($active)) {
    $active = null;
}
?>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin): ?>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['users/index']) ?>" class="btn btn-<?= $active == 'u' ? 'danger' : 'default' ?> btn-lg btn-block">Użytkownicy</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['restaurants/index']) ?>" class="btn btn-<?= $active == 'r' ? 'danger' : 'default' ?> btn-lg btn-block">Restauracje</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['admin/history']) ?>" class="btn btn-<?= $active == 'h' ? 'danger' : 'default' ?> btn-lg btn-block">Historia zamówień</a>
        </div>
    </div>
</div>
<?php endif;
