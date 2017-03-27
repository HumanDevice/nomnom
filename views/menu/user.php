<?php

use yii\helpers\Url;

if (!isset($active)) {
    $active = null;
}
if (!isset($service)) {
    $service = null;
}
?>
<div class="row">
    <div class="col-lg-12 text-right">
        <div class="form-group">
            <div class="pull-left">
                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-cutlery"></i> NomNom</a>
                <a href="<?= Url::to(['tiktak/index']) ?>" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-time"></i> TikTak</a>
                <a href="<?= Url::to(['bimbam/index']) ?>" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-tags"></i> BimBam</a>
            </div>
<?php if ($service === 'nomnom'): ?>
            <a href="<?= Url::to(['account/index']) ?>" class="btn btn-<?= $active == 'a' ? 'danger' : 'default' ?> btn-xs"><i class="glyphicon glyphicon-usd"></i> Aktualne saldo: <strong><?= Yii::$app->formatter->asCurrency(Yii::$app->user->identity->balance, 'PLN') ?></strong></a>
            <a href="<?= Url::to(['site/restaurants']) ?>" class="btn btn-<?= $active == 'r' ? 'danger' : 'default' ?> btn-xs"><i class="glyphicon glyphicon-home"></i> Restauracje</a>
            <a href="<?= Url::to(['site/history']) ?>" class="btn btn-<?= $active == 'h' ? 'danger' : 'default' ?> btn-xs"><i class="glyphicon glyphicon-list-alt"></i> Historia zamówień</a>
<?php endif; ?>
            <a href="<?= Url::to(['site/logout']) ?>" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-log-out"></i> Wyloguj</a>
        </div>
    </div>
</div>
