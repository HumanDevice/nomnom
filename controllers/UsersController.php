<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class UsersController extends Controller
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
     * Users
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Add user
     * @return string
     */
    public function actionCreate()
    {
        $model = new User;
        $model->role = User::ROLE_USER;
        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            $this->ok('Użytkownik dodany.');
            return $this->redirect(['users/index']);
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    /**
     * Edit user
     * @param int $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID użytkownika.');
            return $this->redirect(['users/index']);
        }
        if ($model->id == Yii::$app->user->id) {
            $this->err('Nie ma takiego zmieniania sobie samemu!');
            return $this->redirect(['users/index']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->ok('Użytkownik uaktualniony.');
            return $this->redirect(['users/index']);
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    /**
     * Delete user
     * @param int $id
     * @return string
     */
    public function actionDelete($id)
    {
        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID użytkownika.');
            return $this->redirect(['users/index']);
        }
        if ($model->id == Yii::$app->user->id) {
            $this->err('Nie ma takiego kasowania siebie samego!');
            return $this->redirect(['users/index']);
        }
        $model->deleted = 1;
        $model->old_username = $model->username;
        $model->username = (string)time();
        if ($model->save()) {
            $this->ok('Użytkownik usunięty.');
        } else {
            $this->err('Błąd przy usuwaniu!');
        }
        return $this->redirect(['users/index']);
    }
    
    /**
     * View user
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID użytkownika.');
            return $this->redirect(['users/index']);
        }
        
        return $this->render('view', ['model' => $model]);
    }
    
    /**
     * Prompts for reset user's password
     * @param int $id
     * @return string
     */
    public function actionPassword($id)
    {
        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID użytkownika.');
            return $this->redirect(['users/index']);
        }
        if ($model->id == Yii::$app->user->id) {
            $this->err('Nie ma takiego resetowania samemu sobie!');
            return $this->redirect(['users/index']);
        }
        return $this->render('password', ['model' => $model]);
    }
    
    /**
     * Reset user's password
     * @param int $id
     * @return string
     */
    public function actionReset($id)
    {
        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (empty($model)) {
            $this->err('Coś nie tak z ID użytkownika.');
            return $this->redirect(['users/index']);
        }
        if ($model->id == Yii::$app->user->id) {
            $this->err('Nie ma takiego resetowania samemu sobie!');
            return $this->redirect(['users/index']);
        }
        $model->generateAuthKey();
        $model->password_hash = null;
        if ($model->save()) {
            $this->ok('Hasło użytkownika zostało zresetowane. Użytkownik musi je ponownie ustawić na stronie "To mój Pierwszy Raz tutaj".');
        } else {
            $this->err('Błąd przy resetowaniu!');
        }
        return $this->redirect(['users/index']);
    }
}
