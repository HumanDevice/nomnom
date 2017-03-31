<?php

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'BimBam Raport czasowy';

BootstrapPluginAsset::register($this);
?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['active' => 't', 'service' => 'bimbam']) ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['time/index']) ?>" class="btn btn-warning btn-block">Raport zbiorczy</a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <a href="<?= Url::to(['time/details']) ?>" class="btn btn-default btn-block">Raport szczegółowy</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h2 class="text-center">Raport zbiorczy</h2>
        </div>
    </div>
</div>
<?php if ($summary !== null): ?>
<div class="row">
    <div class="col-lg-12 text-center">
        <div class="form-group">
            <h3><i class="glyphicon glyphicon-time"></i> Łączny czas: <strong><?= Yii::$app->formatter->asDuration($summary) ?></strong></h3>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin([
            'id' => 'report-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-2\">{hint}</div>\n<div class=\"col-lg-2\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
        ]); ?>
        <?= $form->field($searchModel, 'from')->widget(DateTimePicker::class, [
            'options' => ['class' => 'date_range'],
            'pluginOptions' => ['autoclose' => true],
        ]) ?>
        <?= $form->field($searchModel, 'to')->widget(DateTimePicker::class, [
            'options' => ['class' => 'date_range'],
            'pluginOptions' => ['autoclose' => true],
        ]) ?>
        <?= $form->field($searchModel, 'user_id')->widget(Select2::class, [
            'data' => $users,
            'options' => ['placeholder' => 'Wszyscy pracownicy'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
        <?= $form->field($searchModel, 'group_id')->widget(Select2::class, [
            'data' => $groups,
            'options' => ['placeholder' => 'Wszystkie grupy'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
        <?= $form->field($searchModel, 'project_id')->widget(Select2::class, [
            'data' => $projectsList,
            'options' => ['placeholder' => 'Wszystkie projekty'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-8">
                <?= Html::submitButton('Oblicz', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php if ($summaryTabs !== null): ?>
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="form-group">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" class="active"><a href="#pracownik" aria-controls="pracownik" role="tab" data-toggle="tab">Pracownicy</a></li>
                    <li role="presentation"><a href="#projekt" aria-controls="projekt" role="tab" data-toggle="tab">Projekty</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="pracownik">
                        <br>
                        <table class="table table-striped">
                            <tr>
                                <th class="text-center">Lp.</th>
                                <th class="text-center">Pracownik</th>
                                <th class="text-center">Czas</th>
                                <th class="text-center">%</th>
                            </tr>
                            <?php $lp = 1; foreach ($summaryTabs['employees'] as $employee => $seconds): ?>
                            <tr>
                                <td class="text-center"><?= $lp ?></td>
                                <td class="text-center"><?= isset($users[$employee]) ? Html::encode($users[$employee]) : $employee ?></td>
                                <td class="text-center"><?= Yii::$app->formatter->asDuration($seconds) ?></td>
                                <td class="text-center"><?= $summaryTabs['seconds'] != 0 ? round($seconds * 100 / $summaryTabs['seconds'], 2) : '-' ?></td>
                            </tr>
                            <?php $lp++; endforeach; ?>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="projekt">
                        <br>
                        <table class="table table-striped">
                            <tr>
                                <th class="text-center">Lp.</th>
                                <th class="text-center">Projekt</th>
                                <th class="text-center">Czas</th>
                                <th class="text-center">%</th>
                            </tr>
                            <?php $lp = 1; foreach ($summaryTabs['projects'] as $project => $seconds): ?>
                                <tr>
                                    <td class="text-center"><?= $lp ?></td>
                                    <td class="text-center"><?= isset($projects[$project]) ? $projects[$project] : $project ?></td>
                                    <td class="text-center"><?= Yii::$app->formatter->asDuration($seconds) ?></td>
                                    <td class="text-center"><?= $summaryTabs['seconds'] != 0 ? round($seconds * 100 / $summaryTabs['seconds'], 2) : '-' ?></td>
                                </tr>
                                <?php $lp++; endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif;
