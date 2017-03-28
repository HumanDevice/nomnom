<?php

namespace app\controllers;

use app\models\Issue;
use app\models\Project;
use app\models\Time;
use app\models\TimeForm;
use app\models\TimeSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;

class BimbamController extends Controller
{
    use FlashTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['listener'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id === 'listener') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Listens for incoming GitLab webhook request.
     */
    public function actionListener()
    {
        try {
            $postdata = file_get_contents("php://input");
            if (!empty($postdata)) {
                $data = Json::decode($postdata);
                if (isset($data['project']['name'], $data['project']['web_url'], $data['project_id'])) {
                    if (!Project::addNew($data['project']['name'], $data['project']['web_url'], $data['project_id'])) {
                        throw new Exception('Problem z dodaniem projektu: ' . print_r($data, 1));
                    }
                }
                if (isset($data['user']['username'], $data['object_attributes']['project_id'], $data['object_attributes']['id'], $data['object_attributes']['iid'])) {
                    $username = $data['user']['username'];
                    $projectId = $data['object_attributes']['project_id'];
                    $issueId = $data['object_attributes']['id'];
                    $issueInnerId = $data['object_attributes']['iid'];
                    $previousTime = Issue::getTime($projectId, $issueId);
                    $gitlabTime = Yii::$app->gitlab->checkTime($projectId, $issueId);
                    if ($gitlabTime === false || !isset($gitlabTime['total_time_spent'])) {
                        throw new Exception('Problem z API GitLab');
                    }
                    $newTime = $gitlabTime['total_time_spent'];
                    if (!Issue::updateTime($projectId, $issueId, $issueInnerId, $newTime)) {
                        throw new Exception('Problem z aktualizacją czasu w issue: ' . print_r($newTime, 1) . ' - ' . print_r($data, 1));
                    }
                    $registeredTime = $newTime - $previousTime;
                    if ($registeredTime !== 0 && !Time::addTime($projectId, $issueInnerId, $username, $registeredTime)) {
                        throw new Exception('Problem z dodaniem czasu usera: ' . print_r($registeredTime, 1) . ' - ' . print_r($data, 1));
                    }
                }
            }
        } catch (\Throwable $exc) {
            Yii::error($exc->getMessage());
        }
        die;
    }

    /**
     * Displays time spent.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TimeSearch;
        $searchModel->from = date('Y-m-01 00:00');
        $searchModel->to = date('Y-m-t 23:59');
        $searchModel->user_id = Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds custom time.
     * @return string
     */
    public function actionAdd()
    {
        $model = new TimeForm;
        $model->date = date('d.m.Y');
        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            $this->ok('Czas dodany.');
            return $this->redirect(['bimbam/index']);
        }

        $projects = Project::find()->orderBy(['name' => SORT_ASC])->all();
        $data = [];
        /* @var $project Project */
        foreach ($projects as $project) {
            $data[$project->project_id] = $project->name . ' [' . substr($project->url, 32) . ']';
        }

        return $this->render('add', [
            'model' => $model,
            'data' => $data
        ]);
    }

    /**
     * Deletes custom time.
     * @param int $id
     * @return string
     */
    public function actionDelete($id)
    {
        $model = Time::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'issue_id' => null]);
        if (!$model) {
            $this->err('Nie można odnaleźć wpisu.');
            return $this->redirect(['bimbam/index']);
        }
        if ($model->delete()) {
            $this->ok('Wpis został usunięty.');
        } else {
            $this->err('Błąd usuwania wpisu.');
        }
        return $this->redirect(['bimbam/index']);
    }

    /**
     * Updates custom time.
     * @param int $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = TimeForm::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'issue_id' => null]);
        if (!$model) {
            $this->err('Nie można odnaleźć wpisu.');
            return $this->redirect(['bimbam/index']);
        }
        $model->prepareUpdate();
        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            $this->ok('Czas uaktualniony.');
            return $this->redirect(['bimbam/index']);
        }

        $projects = Project::find()->orderBy(['name' => SORT_ASC])->all();
        $data = [];
        /* @var $project Project */
        foreach ($projects as $project) {
            $data[$project->project_id] = $project->name . ' [' . substr($project->url, 32) . ']';
        }

        return $this->render('update', [
            'model' => $model,
            'data' => $data
        ]);
    }
}
