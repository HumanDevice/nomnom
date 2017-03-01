<?php

use yii\helpers\Url;

if (!isset($active)) {
    $active = null;
}
?>
<div class="row">
    <div class="col-lg-12 text-right">
        <div class="form-group">
            <div class="pull-left">
                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-cutlery"></i> NomNom</a>
                <a href="<?= Url::to(['tiktak/index']) ?>" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-time"></i> TikTak</a>
            </div>
            <a href="<?= Url::to(['site/restaurants']) ?>" class="btn btn-<?= $active == 'r' ? 'danger' : 'default' ?> btn-xs"><i class="glyphicon glyphicon-home"></i> Restauracje</a>
            <a href="<?= Url::to(['site/history']) ?>" class="btn btn-<?= $active == 'h' ? 'danger' : 'default' ?> btn-xs"><i class="glyphicon glyphicon-list-alt"></i> Historia zamówień</a>
            <a href="<?= Url::to(['site/logout']) ?>" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-log-out"></i> Wyloguj</a>
        </div>
    </div>
</div>
