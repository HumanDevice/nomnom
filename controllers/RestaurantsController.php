<?php

namespace app\controllers;

use app\models\Restaurant;
use app\models\RestaurantSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class RestaurantsController extends Controller
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
     * Restaurants
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new RestaurantSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Add restaurant
     * @return string
     */
    public function actionCreate()
    {
        $model = new Restaurant;
        $model->max = 1;
        if ($model->load(Yii::$app->request->post())) {
            $model->screen = UploadedFile::getInstance($model, 'screen');
            if ($model->validate()) {
                if (empty($model->url) && empty($model->screen)) {
                    $model->addError('url', 'Musisz podać URL lub plik ze zdjęciem menu.');
                } else {
                    if ($model->add()) {
                        $this->ok('Restauracja dodana.');
                        return $this->redirect(['restaurants/index']);
                    }
                }
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    /**
     * Edit restaurant
     * @param int $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = Restaurant::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID restauracji.');
            return $this->redirect(['restaurants/index']);
        }
        $oldScreen = $model->screen;
        if ($model->load(Yii::$app->request->post())) {
            $upload = UploadedFile::getInstance($model, 'screen');
            $model->screen = $upload ?: $oldScreen;
            if ($model->validate()) {
                if (empty($model->url) && empty($model->screen) && $model->stay == 0) {
                    $model->addError('url', 'Musisz podać URL lub plik ze zdjęciem menu.');
                } else {
                    if ($model->modify()) {
                        $this->ok('Restauracja uaktualniona.');
                        return $this->redirect(['restaurants/index']);
                    }
                }
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    /**
     * Delete restaurant
     * @param int $id
     * @return string
     */
    public function actionDelete($id)
    {
        $model = Restaurant::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID restauracji.');
            return $this->redirect(['restaurants/index']);
        }
        $model->deleted = 1;
        $model->old_name = $model->name;
        $model->name = (string)time();
        if ($model->save()) {
            $this->ok('Restauracja usunięta.');
        } else {
            $this->err('Błąd przy usuwaniu!');
        }
        return $this->redirect(['restaurants/index']);
    }
    
    /**
     * View restaurant
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = Restaurant::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID restauracji.');
            return $this->redirect(['restaurants/index']);
        }
        
        return $this->render('view', ['model' => $model]);
    }
}
