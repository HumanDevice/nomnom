<?php

namespace app\controllers;

use app\models\Group;
use app\models\Project;
use app\models\Time;
use app\models\TimeSearch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
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
        $projectsList = [];
        $projectModels = Project::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $projectModel Project */
        foreach ($projectModels as $projectModel) {
            $projects[$projectModel->project_id] =  Html::a(Html::encode('[' . substr($projectModel->url, 32, strrpos($projectModel->url, '/') - 32) . '] ' . $projectModel->name), $projectModel->url, ['target' => 'projekt']);
            $projectsList[$projectModel->project_id] =  '[' . substr($projectModel->url, 32, strrpos($projectModel->url, '/') - 32) . '] ' . $projectModel->name;
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
            'projectsList' => $projectsList,
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

    /**
     * Formats CSV.
     * @param string $data
     * @return string
     */
    public function formatCsv($data)
    {
        if (strpos($data, ',') !== false) {
            $data = '"' . str_replace('"', '""', $data) . '"';
        }
        return $data;
    }

    /**
     * Formats time.
     * @param int $seconds
     * @return string
     */
    public function formatTime($seconds)
    {
        $hours = floor($seconds / 60 / 60);
        $left = $seconds - $hours * 60 * 60;
        $minutes = floor($left / 60);
        $left -= $minutes * 60;
        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($left, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Downloads csv
     */
    public function actionCsv()
    {
        $csv = [];
        $name = date('Y-m', mktime(1, 1, 1, date('n') - 1, 1));
        $from = date('Y-m-01 00:00:00', mktime(1, 1, 1, date('n') - 1, 1));
        $to = date('Y-m-t 23:59:59', mktime(1, 1, 1, date('n') - 1, 1));
        $time = Time::find()->where(['and',
            ['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($from, timezone_open('Europe/Warsaw')))],
            ['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($to, timezone_open('Europe/Warsaw')))],
        ])->joinWith(['project', 'user'])->orderBy(['time.created_at' => SORT_ASC]);
        $csv[] = [
            'Projekt',
            'URL',
            'Autor',
            'Issue',
            'Czas [s]',
            'Czas [h:m:s]',
            'Opis',
            'Data',
        ];
        foreach ($time->each() as $entry) {
            $csv[] = [
                $this->formatCsv($entry->project->name),
                $this->formatCsv($entry->issue_id ? $entry->project->url . '/issues/' . $entry->issue_id : $entry->project->url),
                $this->formatCsv($entry->user->username),
                $entry->issue_id,
                $entry->seconds,
                $this->formatTime($entry->seconds),
                $this->formatCsv($entry->description),
                Yii::$app->formatter->asDate($entry->created_at, 'y-MM-dd')
            ];
        }
        $content = [];
        foreach ($csv as $line) {
            $content[] = implode(',', $line);
        }

        return Yii::$app->response->sendContentAsFile(implode("\n", $content), 'bimbam-' . $name . '.csv', ['mimeType' => 'text/plain']);
    }
}
