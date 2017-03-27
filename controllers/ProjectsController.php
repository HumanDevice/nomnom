<?php

namespace app\controllers;

use app\models\Group;
use app\models\GroupForm;
use app\models\Project;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class ProjectsController extends Controller
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
     * Project groups
     * @return string
     */
    public function actionIndex()
    {
        $groups = ArrayHelper::map(Group::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        if (Yii::$app->request->post('GroupForm')) {
            $model = GroupForm::findOne(Yii::$app->request->post('GroupForm')['id']);
            if ($model->load(Yii::$app->request->post())) {
                if ($model->edit()) {
                    $this->ok('Grupa została zapisana.');
                } else {
                    $this->err('Błąd zapisu grupy.');
                }
                return $this->redirect(['projects/index']);
            }
        }

        return $this->render('index', ['groups' => $groups]);
    }

    /**
     * Adds groups
     * @return string
     */
    public function actionAdd()
    {
        $model = new GroupForm;
        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            $this->ok('Grupa dodana.');
            return $this->redirect(['projects/index']);
        }

        $projects = [];
        $projectModels = Project::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $projectModel Project */
        foreach ($projectModels as $projectModel) {
            $projects[$projectModel->project_id] = $projectModel->name . ' [' . substr($projectModel->url, 32) . ']';
        }

        return $this->render('add', [
            'model' => $model,
            'projects' => $projects
        ]);
    }

    /**
     * Renders group
     * @param $id
     * @return null|string|\yii\web\Response
     */
    public function actionGroup($id)
    {
        $model = GroupForm::findOne($id);
        if (!$model) {
            return null;
        }
        $model->projectsToAdd = [];
        foreach ($model->projects as $project) {
            $model->projectsToAdd[] = $project->project_id;
        }

        $projects = [];
        $projectModels = Project::find()->orderBy(['name' => SORT_ASC])->all();
        /* @var $projectModel Project */
        foreach ($projectModels as $projectModel) {
            $projects[$projectModel->project_id] = $projectModel->name . ' [' . substr($projectModel->url, 32) . ']';
        }

        return $this->renderPartial('_group', [
            'model' => $model,
            'projects' => $projects
        ]);
    }

    /**
     * Deletes group
     * @param $id
     * @return null|string|\yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = Group::findOne($id);
        if (!$model) {
            $this->err('Nie można odnaleźć grupy o tym ID.');
            return $this->redirect(['projects/index']);
        }
        if ($model->delete()) {
            $this->ok('Grupa została usunięta.');
        } else {
            $this->err('Nie można usunąć tej grupy.');
        }
        return $this->redirect(['projects/index']);
    }
}
