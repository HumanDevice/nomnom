<?php

use app\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'NomNomAdmin Użytkownicy';
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 'u']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <?php if (in_array(Yii::$app->user->id, User::BOOKKEEPER)): ?>
            <a href="<?= Url::to(['users/csv']) ?>" class="btn btn-default btn-lg pull-right"><span class="glyphicon glyphicon-download"></span> Pobierz CSV sald</a>
            <?php endif; ?>
            <a href="<?= Url::to(['users/create']) ?>" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj użytkownika</a>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns'      => [
        [
            'attribute' => 'id',
            'headerOptions' => ['class' => 'col-sm-1']
        ],
        'username',
        [
            'attribute' => 'division',
            'filter' => User::divisionLabels(),
            'value' => function ($model) {
                return User::divisionLabels()[$model->division];
            }
        ],
        [
            'attribute' => 'role',
            'filter' => [User::ROLE_USER => 'Pracownicy', User::ROLE_ADMIN => 'Admini'],
            'value' => function ($model) {
                return $model->role == User::ROLE_ADMIN ? 'Admin' : 'Pracownik';
            }
        ],
        [
            'attribute' => 'virgin',
            'label' => 'Pierwszy raz?',
            'filter' => [0 => 'Tak', 1 => 'Nie'],
            'value' => function ($model) {
                return empty($model->password_hash) ? 'Tak' : 'Nie';
            }
        ],
        'gitlab',
        'email',
        [
            'class' => ActionColumn::class,
            'contentOptions' => ['class' => 'text-right', 'style' => 'white-space:nowrap'],
            'template' => '{view}&nbsp;{update}&nbsp;{balance}&nbsp;{password}&nbsp;{delete}',
            'buttons' => [
                'balance' => function ($url, $model, $key) {
                    return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-usd']), $url, ['data-pjax' => 0]);
                },
                'password' => function ($url, $model, $key) {
                    return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-retweet']), $url, ['data-pjax' => 0]);
                }
            ],
            'visibleButtons' => [
                'balance' => in_array(Yii::$app->user->id, User::BOOKKEEPER)
            ]
        ],
    ]
]); ?>
<?php Pjax::end();
