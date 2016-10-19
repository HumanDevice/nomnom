<?php

use yii\helpers\Url;

if (!isset($active)) {
    $active = null;
}
?>
<div class="row">
    <div class="col-lg-12 text-right">
        <div class="form-group">
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-info btn-xs pull-left">NomNom</a>
            <a href="<?= Url::to(['site/history']) ?>" class="btn btn-<?= $active == 'h' ? 'danger' : 'default' ?> btn-xs">Historia zamówień</a>
            <a href="<?= Url::to(['site/logout']) ?>" class="btn btn-default btn-xs">Wyloguj</a>
        </div>
    </div>
</div>
