<?php

use app\models\User;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = 'NomNomAdmin Użytkownicy';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'u']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['users/index']) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> Lista użytkowników</a>
            <a href="<?= Url::to(['users/update', 'id' => $model->id]) ?>" class="btn btn-success"><span class="glyphicon glyphicon-edit"></span> Edytuj użytkownika</a>
            <a href="<?= Url::to(['users/delete', 'id' => $model->id]) ?>" class="btn btn-danger" data-confirm="Czy na pewno chcesz usunąć tego użytkownika?">
                <span class="glyphicon glyphicon-trash"></span> Usuń użytkownika
            </a>
        </div>
    </div>
</div>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'username',
            'value' => $model->employeeName
        ],
        [
            'attribute' => 'role',
            'value' => $model->role == User::ROLE_ADMIN ? 'Admin' : 'Pracownik'
        ],
    ]
]);
