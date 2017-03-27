<?php

use yii\widgets\ListView;

$this->title = 'NomNomAdmin Podgląd zamówienia';
?>
<?= $this->render('/menu/user', ['service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['active' => 'h', 'service' => 'nomnom']) ?>

<h1>Zamówienie z dnia <?= Yii::$app->formatter->asDate($model->created_at, 'yyyy/MM/dd') ?></h1>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '/site/ordered',
]) ?>
