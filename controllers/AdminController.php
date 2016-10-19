<?php

namespace app\controllers;

use app\models\FoodSearch;
use app\models\Order;
use app\models\OrderForm;
use app\models\OrderSearch;
use app\models\PreferencesForm;
use app\models\Restaurant;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class AdminController extends Controller
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
                        'matchCallback' => function ($rule, $action) {
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
     * Opens order.
     * @return string
     */
    public function actionOpen()
    {
        if (Order::isOpen()) {
            $this->info('Zamówienie jest otwarte.');
            return $this->redirect(['site/index']);
        }
        $model = new OrderForm;
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->start();
            if ($result === true) {
                $this->ok('Zamówienie zostało otwarte.');
                Yii::$app->hipchat->send($model->message);
                return $this->goHome();
            }
            $this->err($result);
        }
        return $this->render('open', ['model' => $model]);
    }
    
    /**
     * Preferred restaurants action.
     * @return string
     */
    public function actionPreferences()
    {
        $model = new PreferencesForm;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->ok('Preferencje zostały zapisane.');
                return $this->redirect(['admin/preferences']);
            }
            $this->err('Błąd przy zapisie preferencji.');
        }
        return $this->render('preferences', [
            'model' => $model,
            'allRestaurants' => Restaurant::getList()
        ]);
    }
    
    /**
     * Food history
     * @return string
     */
    public function actionHistory()
    {
        $searchModel = new OrderSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('history', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * View order
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = Order::findOne(['id' => $id]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID zamówienia.');
            return $this->redirect(['admin/index']);
        }
        
        return $this->render('view', [
            'model' => $model,
            'dataProvider' => (new FoodSearch)->search($model->id)
        ]);
    }
}
