<?php

$this->title = 'TikTak';
?>
<?= $this->render('/menu/user') ?>

<div class="row">
    <div class="col-lg-12">
        <p>Aktualny tydzień wg <a href="http://jakitydzien.pl" target="jakitydzien">jakitydzien.pl</a>: <span class="label label-primary"><?= $week ?></span></p>
    </div>
</div>
