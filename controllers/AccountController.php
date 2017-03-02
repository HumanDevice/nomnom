<?php

namespace app\controllers;

use app\models\BalanceSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class AccountController extends Controller
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
     * Displays hours.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BalanceSearch;
        $dataProvider = $searchModel->search(Yii::$app->user->id, Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
