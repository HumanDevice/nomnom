<?php

$this->title = 'NomNom';
?>
<?= $this->render('/menu/user', ['service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['service' => 'nomnom']) ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="text-info bg-info text-center">
            NomNom śpi.<br>
            <small>Otwarcie o 10:50.</small>
        </h1>
    </div>
</div>
