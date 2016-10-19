<?php

use yii\helpers\Url;

$this->title = 'NomNom';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin') ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="text-info bg-info text-center">
            Brak otwartych sesji.<br>
            <small>Zawołaj admina, żeby otworzył.</small>
        </h1>
    </div>
</div>
