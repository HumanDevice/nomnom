<?php

namespace app\controllers;

use app\models\Calendar;
use app\models\DayForm;
use app\models\DaySearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class CalendarController
 * @package app\controllers
 */
class CalendarController extends Controller
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
        $searchModel = new DaySearch;
        $searchModel->from = date('Y-01-01');
        $searchModel->to = date('Y-12-31');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds day off.
     * @return string
     */
    public function actionAdd()
    {
        $model = new DayForm;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->ok('Data dodana.');
            return $this->redirect(['calendar/index']);
        }

        return $this->render('add', ['model' => $model]);
    }

    /**
     * Deletes day off.
     * @param string $id
     * @return string
     */
    public function actionDelete($id)
    {
        $model = Calendar::findOne($id);
        if (!$model) {
            $this->err('Nie można odnaleźć daty.');
            return $this->redirect(['calendar/index']);
        }
        if ($model->delete()) {
            $this->ok('Data została usunięta.');
        } else {
            $this->err('Błąd usuwania daty.');
        }
        return $this->redirect(['calendar/index']);
    }
}
