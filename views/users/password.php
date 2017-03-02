<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'NomNomAdmin Użytkownicy';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'u']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['users/view', 'id' => $model->id]) ?>" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Podgląd użytkownika</a>
            <a href="<?= Url::to(['users/update', 'id' => $model->id]) ?>" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span> Edytuj użytkownika</a>
            <a href="<?= Url::to(['users/delete', 'id' => $model->id]) ?>" class="btn btn-danger btn-xs" data-confirm="Czy na pewno chcesz usunąć tego użytkownika?">
                <span class="glyphicon glyphicon-trash"></span> Usuń użytkownika
            </a>
        </div>
    </div>
</div>
<br><br>
<div class="row">
    <div class="col-lg-12 text-center">
        <a href="<?= Url::to(['users/reset', 'id' => $model->id]) ?>" class="btn btn-warning btn-lg" data-confirm="Czy na pewno chcesz zresetować hasło tego użytkownika?">
            <span class="glyphicon glyphicon-retweet"></span> Zresetuj hasło użytkownika <?= Html::encode($model->username) ?>
        </a>
    </div>
</div>