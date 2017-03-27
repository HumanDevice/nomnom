<?php

namespace app\controllers;

use app\models\Group;
use app\models\Project;
use app\models\TimeSearch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class TimeController extends Controller
{
    use FlashTrait;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->isAdmin;
                        },
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Time report
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TimeSearch;
        $searchModel->from = date('Y-m-01 00:00');
        $searchModel->to = date('Y-m-t 23:59');

        $summary = $summaryTabs = null;
        if ($searchModel->load(Yii::$app->request->post()) && $searchModel->validate()) {
            $summary = $searchModel->summaryReport;
            $summaryTabs = $searchModel->summaryReportTabs;
        }

        $users = [];
        $userModels = User::find()->where(['deleted' => 0])->orderBy(['username' => SORT_ASC])->all();
        foreach ($userModels as $userModel) {
            $users[$userModel->id] = $userModel->username . ' [' . (isset(User::divisionLabels()[$userModel->division]) ? User::divisionLabels()[$userModel->division] : '') . ']';
        }
        $projects = [];
        $projectModels = Project::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $projectModel Project */
        foreach ($projectModels as $projectModel) {
            $projects[$projectModel->project_id] = $projectModel->name . ' [' . substr($projectModel->url, 32) . ']';
        }
        $groups = [];
        $groupModels = Group::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $groupModel Group */
        foreach ($groupModels as $groupModel) {
            $groups[$groupModel->id] = $groupModel->name;
        }

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'users' => $users,
            'projects' => $projects,
            'groups' => $groups,
            'summary' => $summary,
            'summaryTabs' => $summaryTabs,
        ]);
    }

    /**
     * Detailed time report
     * @return string
     */
    public function actionDetails()
    {
        $searchModel = new TimeSearch;
        $searchModel->from = date('Y-m-01 00:00');
        $searchModel->to = date('Y-m-t 23:59');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $users = [];
        $userModels = User::find()->where(['deleted' => 0])->orderBy(['username' => SORT_ASC])->all();
        foreach ($userModels as $userModel) {
            $users[$userModel->id] = $userModel->username . ' [' . (isset(User::divisionLabels()[$userModel->division]) ? User::divisionLabels()[$userModel->division] : '') . ']';
        }
        $groups = [];
        $groupModels = Group::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $groupModel Group */
        foreach ($groupModels as $groupModel) {
            $groups[$groupModel->id] = $groupModel->name;
        }

        return $this->render('details', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => $users,
            'groups' => $groups,
        ]);
    }
}
