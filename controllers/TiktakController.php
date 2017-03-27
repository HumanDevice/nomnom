<?php

namespace app\controllers;

use app\models\Hour;
use app\models\HourSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class TiktakController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns week type.
     * Cached for 1 hour.
     * @return string
     */
    public function getWeek()
    {
        $week = '?';
        try {
            $week = Yii::$app->cache->get('week');
            if ($week === false) {
                $week = '?';
                $provider = file_get_contents('http://jakitydzien.pl/');
                if (($start = strpos($provider, '<h1>')) !== false) {
                    if (($end = strpos($provider, '</h1>')) !== false) {
                        $week = trim(substr($provider, $start + 4, $end - $start - 4));
                    }
                }
                Yii::$app->cache->set('week', $week, 3600);
            }
        } catch (\Throwable $exc) {
            Yii::error($exc->getMessage());
        }
        return $week;
    }

    /**
     * Displays hours.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new HourSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'week' => $this->getWeek(),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Edit hours
     * @return string
     */
    public function actionUpdate()
    {
        $model = Hour::findOne(['user_id' => Yii::$app->user->id]);
        if (empty($model)) {
            $model = new Hour;
            $model->user_id = Yii::$app->user->id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->ok('Godziny uaktualnione.');
            return $this->redirect(['tiktak/index']);
        }

        return $this->render('update', ['model' => $model]);
    }
}
