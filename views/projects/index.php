<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'BimBam Grupy';

$url = Url::to(['projects/group']);
$this->registerJs(<<<JS
$("#groupId").change(function () {
    var id = $(this).val();
    $("#editGroup").load("$url?id=" + id);
});
JS
);
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'p', 'service' => 'bimbam']) ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['projects/index']) ?>" class="btn btn-warning btn-block">Grupy</a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['projects/add']) ?>" class="btn btn-default btn-block">Nowa grupa</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2 class="text-center">Grupy</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <select name="groups" id="groupId" class="form-control">
                <option value="" disabled selected>Wybierz grupę</option>
                <?php foreach ($groups as $id => $group): ?>
                <option value="<?= $id ?>"><?= Html::encode($group) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div id="editGroup"></div>
