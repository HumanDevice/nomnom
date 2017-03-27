<?php

use yii\helpers\Url;

if (!isset($active)) {
    $active = null;
}
if (!isset($service)) {
    $service = null;
}
?>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin): ?>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['users/index']) ?>" class="btn btn-<?= $active == 'u' ? 'danger' : 'default' ?> btn-lg btn-block"><i class="glyphicon glyphicon-user"></i> Użytkownicy</a>
        </div>
    </div>
<?php if ($service === 'nomnom'): ?>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['restaurants/index']) ?>" class="btn btn-<?= $active == 'r' ? 'danger' : 'default' ?> btn-lg btn-block"><i class="glyphicon glyphicon-home"></i> Restauracje</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['admin/history']) ?>" class="btn btn-<?= $active == 'h' ? 'danger' : 'default' ?> btn-lg btn-block"><i class="glyphicon glyphicon-list-alt"></i> Historia zamówień</a>
        </div>
    </div>
<?php endif; ?>
<?php if ($service === 'bimbam'): ?>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['time/index']) ?>" class="btn btn-<?= $active == 't' ? 'danger' : 'default' ?> btn-lg btn-block"><i class="glyphicon glyphicon-time"></i> Raport czasowy</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <a href="<?= Url::to(['projects/index']) ?>" class="btn btn-<?= $active == 'p' ? 'danger' : 'default' ?> btn-lg btn-block"><i class="glyphicon glyphicon-folder-open"></i> Grupy</a>
        </div>
    </div>
<?php endif; ?>
</div>
<?php endif;
