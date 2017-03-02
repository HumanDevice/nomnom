<?php

use app\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'TikTak';

$this->registerJs(<<<JS
var week_odd = true;
var week_even = true;
$("#week_odd").click(function(e) {
    e.preventDefault();
    if (week_odd) {
        $(this).removeClass("btn-info").addClass("btn-default").html("<i class=\"glyphicon glyphicon-eye-open\"></i> Pokaż tydzień nieparzysty");
        $(".week_odd").hide();
    } else {
        $(this).addClass("btn-info").removeClass("btn-default").html("<i class=\"glyphicon glyphicon-eye-close\"></i> Ukryj tydzień nieparzysty");
        $(".week_odd").show();
    }
    week_odd = !week_odd;
});
$("#week_even").click(function(e) {
    e.preventDefault();
    if (week_even) {
        $(this).removeClass("btn-warning").addClass("btn-default").html("<i class=\"glyphicon glyphicon-eye-open\"></i> Pokaż tydzień parzysty");
        $(".week_even").hide();
    } else {
        $(this).addClass("btn-warning").removeClass("btn-default").html("<i class=\"glyphicon glyphicon-eye-close\"></i> Ukryj tydzień parzysty");
        $(".week_even").show();
    }
    week_even = !week_even;
});
JS
);
?>
<?= $this->render('/menu/user') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a href="<?= Url::to(['tiktak/update']) ?>" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-time"></span> Aktualizuj godziny</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <i class="glyphicon glyphicon-alert"></i> W podanych tu dniach i godzinach spodziewamy się zastać daną osobę (w siedzibie firmy albo przynajmniej online z możliwością
            pracy zdalnej), zatem prosimy o aktualizowanie godzin na bieżąco i bycie punktualnym.
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            Aktualny tydzień wg <a href="http://jakitydzien.pl" target="jakitydzien">jakitydzien.pl</a>: <span class="label label-primary"><?= $week ?></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <button id="week_odd" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-close"></i> Ukryj tydzień nieparzysty</button>
            <button id="week_even" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-eye-close"></i> Ukryj tydzień parzysty</button>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        [
            'attribute' => 'username',
            'value' => function ($model) {
                return Html::encode($model->user->username);
            }
        ],
        [
            'attribute' => 'division',
            'filter' => User::divisionLabels(),
            'value' => function ($model) {
                return User::divisionLabels()[$model->user->division];
            }
        ],
        [
            'attribute' => 'monday_odd',
            'headerOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'contentOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'filterOptions' => ['class' => 'week_odd', 'style' => 'background-color:#c7dcef'],
        ],
        [
            'attribute' => 'tuesday_odd',
            'headerOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'contentOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'filterOptions' => ['class' => 'week_odd', 'style' => 'background-color:#c7dcef'],
        ],
        [
            'attribute' => 'wednesday_odd',
            'headerOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'contentOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'filterOptions' => ['class' => 'week_odd', 'style' => 'background-color:#c7dcef'],
        ],
        [
            'attribute' => 'thursday_odd',
            'headerOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'contentOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'filterOptions' => ['class' => 'week_odd', 'style' => 'background-color:#c7dcef'],
        ],
        [
            'attribute' => 'friday_odd',
            'headerOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'contentOptions' => ['class' => 'text-center week_odd', 'style' => 'background-color:#c7dcef'],
            'filterOptions' => ['class' => 'week_odd', 'style' => 'background-color:#c7dcef'],
        ],
        [
            'attribute' => 'monday_even',
            'headerOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'contentOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'filterOptions' => ['class' => 'week_even', 'style' => 'background-color:#efe2c7'],
        ],
        [
            'attribute' => 'tuesday_even',
            'headerOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'contentOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'filterOptions' => ['class' => 'week_even', 'style' => 'background-color:#efe2c7'],
        ],
        [
            'attribute' => 'wednesday_even',
            'headerOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'contentOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'filterOptions' => ['class' => 'week_even', 'style' => 'background-color:#efe2c7'],
        ],
        [
            'attribute' => 'thursday_even',
            'headerOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'contentOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'filterOptions' => ['class' => 'week_even', 'style' => 'background-color:#efe2c7'],
        ],
        [
            'attribute' => 'friday_even',
            'headerOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'contentOptions' => ['class' => 'text-center week_even', 'style' => 'background-color:#efe2c7'],
            'filterOptions' => ['class' => 'week_even', 'style' => 'background-color:#efe2c7'],
        ],
        [
            'attribute' => 'vacation',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
        ],
    ]
]); ?>
<?php Pjax::end();
