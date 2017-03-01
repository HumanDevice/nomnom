<?php

namespace app\controllers;

use app\models\FoodForm;
use app\models\FoodSearch;
use app\models\HistorySearch;
use app\models\LoginForm;
use app\models\Order;
use app\models\OrderChoice;
use app\models\OrderFood;
use app\models\Restaurant;
use app\models\RestaurantSearch;
use app\models\StartForm;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class SiteController extends Controller
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
                        'actions' => ['login', 'start'],
                        'allow' => true,
                        'roles' => ['?'],
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
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $order = Order::find()->where(['!=', 'stage', Order::STAGE_CLOSE])->orderBy(['id' => SORT_DESC])->limit(1)->one();

        if ($order) {
            switch ($order->stage) {
                case Order::STAGE_VOTE:
                    return $this->stageVote($order);
                case Order::STAGE_MEAL:
                    return $this->stageMeal($order);
            }
        }

        return $this->render('index');
    }

    /**
     * Login action.
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Start action.
     * @return string
     */
    public function actionStart()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new StartForm;
        if ($model->load(Yii::$app->request->post()) && $model->start()) {
            return $this->goHome();
        }
        return $this->render('start', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Voting stage
     * @param Order $order
     * @return string
     */
    public function stageVote(Order $order)
    {
        $voted = (new Query)
                ->from(OrderChoice::tableName())
                ->where([
                    'order_id' => $order->id,
                    'author_id' => Yii::$app->user->id,
                ])
                ->exists();

        return $this->render('stage-vote', [
            'order' => $order,
            'voted' => $voted
        ]);
    }

    /**
     * Meal stage
     * @param Order $order
     * @return string
     */
    public function stageMeal(Order $order)
    {
        if ($order->stage_end < time()) {
            return $this->stageAfterMeal($order);
        }

        $model = new FoodForm;
        if (empty($order->restaurant2_id)) {
            $model->restaurant = $order->restaurant_id;
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->screen = UploadedFile::getInstance($model, 'screen');
            if ($model->validate()) {
                if (empty($model->code) && empty($model->screen)) {
                    $model->addError('code', 'Musisz wskazać, co chcesz zamówić.');
                } else {
                    $result = $model->order();
                    if ($result === true) {
                        $this->ok('Zamówienie złożone.');
                        return $this->goHome();
                    }
                    $this->err($result);
                }
            }
        }

        $ordered = OrderFood::findOne([
            'order_id' => $order->id,
            'author_id' => Yii::$app->user->id,
        ]);

        return $this->render('stage-meal', [
            'model' => $model,
            'order' => $order,
            'ordered' => $ordered,
            'dataProvider' => (new FoodSearch)->search($order->id)
        ]);
    }

    /**
     * After meal stage
     * @param Order $order
     * @return string
     */
    protected function stageAfterMeal(Order $order)
    {
        return $this->render('stage-after-meal', ['order' => $order]);
    }

    /**
     * Voting.
     * @param int $order
     * @param int $restaurant
     */
    public function actionVote($order, $restaurant)
    {
        $chosenOrder = Order::findOne($order);
        $chosenRestaurant = Restaurant::findOne(['id' => $restaurant, 'deleted' => 0]);
        if (empty($chosenOrder)) {
            $this->err('Coś nie tak z ID zamówienia!');
            return $this->goBack();
        }
        if (empty($chosenRestaurant)) {
            $this->err('Coś nie tak z ID restauracji!');
            return $this->goBack();
        }
        if ($chosenOrder->stage != Order::STAGE_VOTE) {
            $this->err('Zamówienie nie jest już na etapie głosowania!');
            return $this->goBack();
        }
        if ($chosenOrder->stage_end < time()) {
            $this->err('Termin głosowania upłynął!');
            return $this->goBack();
        }
        if ((new Query)
                ->from(OrderChoice::tableName())
                ->where([
                    'order_id' => $chosenOrder->id,
                    'author_id' => Yii::$app->user->id,
                ])
                ->exists()) {
            $this->err('Usuń najpierw swój głos, aby go zmienić!');
            return $this->goBack();
        }
        $choice = new OrderChoice;
        $choice->author_id = Yii::$app->user->id;
        $choice->restaurant_id = $chosenRestaurant->id;
        $choice->order_id = $chosenOrder->id;
        if ($choice->save()) {
            $this->ok('Dzięki za oddanie głosu!');
        } else {
            $this->err('Błąd przy zapisywaniu głosu!');
        }
        return $this->goBack();
    }

    /**
     * Unvoting.
     * @param int $order
     */
    public function actionUnvote($order)
    {
        $chosenOrder = Order::findOne($order);
        if (empty($chosenOrder)) {
            $this->err('Coś nie tak z ID zamówienia!');
            return $this->goBack();
        }
        if ($chosenOrder->stage != Order::STAGE_VOTE) {
            $this->err('Zamówienie nie jest już na etapie głosowania!');
            return $this->goBack();
        }
        if ($chosenOrder->stage_end < time()) {
            $this->err('Termin głosowania upłynął!');
            return $this->goBack();
        }
        $vote = OrderChoice::findOne([
            'order_id' => $chosenOrder->id,
            'author_id' => Yii::$app->user->id,
        ]);
        if (empty($vote)) {
            $this->err('Nie mamy jeszcze Twojego głosu!');
            return $this->goBack();
        }
        if ($vote->delete()) {
            $this->ok('Głos został usunięty!');
        } else {
            $this->err('Błąd przy usuwaniu głosu!');
        }
        return $this->goBack();
    }

    /**
     * Unorder.
     * @param int $order
     */
    public function actionUnorder($order)
    {
        $chosenOrder = Order::findOne($order);
        if (empty($chosenOrder)) {
            $this->err('Coś nie tak z ID zamówienia!');
            return $this->goBack();
        }
        if ($chosenOrder->stage != Order::STAGE_MEAL) {
            $this->err('Zamówienie nie jest już na etapie głosowania!');
            return $this->goBack();
        }
        if ($chosenOrder->stage_end < time()) {
            $this->err('Termin głosowania upłynął!');
            return $this->goBack();
        }
        $food = OrderFood::findOne([
            'order_id' => $chosenOrder->id,
            'author_id' => Yii::$app->user->id,
        ]);
        if (empty($food)) {
            $this->err('Nie mamy jeszcze Twojego zamówienia!');
            return $this->goBack();
        }
        if ($food->delete()) {
            if (!empty($food->screen)) {
                unlink(Yii::getAlias('@app/web/uploads/' . $food->author_id . '/' . $food->screen));
            }
            $this->ok('Zamówienie zostało usunięte!');
        } else {
            $this->err('Błąd przy usuwaniu zamówienia!');
        }
        return $this->goBack();
    }

    /**
     * Order the same.
     * @param int $food
     */
    public function actionOrder($food)
    {
        $chosenFood = OrderFood::findOne($food);
        if (empty($chosenFood)) {
            $this->err('Coś nie tak z ID zamówienia!');
            return $this->goBack();
        }
        if ($chosenFood->order->stage != Order::STAGE_MEAL) {
            $this->err('Zamówienie nie jest już na etapie głosowania!');
            return $this->goBack();
        }
        if ($chosenFood->order->stage_end < time()) {
            $this->err('Termin głosowania upłynął!');
            return $this->goBack();
        }

        $alreadyOrdered = OrderFood::findOne([
            'author_id' => Yii::$app->user->id,
            'order_id' => $chosenFood->order_id,
        ]);
        if (!empty($alreadyOrdered)) {
            $this->err('Usuń najpierw swoje poprzednie zamówienie, aby je zmienić!');
            return $this->goBack();
        }

        $same = new OrderFood();
        $same->author_id = Yii::$app->user->id;
        $same->order_id = $chosenFood->order_id;
        $same->restaurant_id = $chosenFood->restaurant_id;
        $same->code = $chosenFood->code;
        $same->screen = $chosenFood->screen;

        if (!empty($same->screen)) {
            $hisDirectory = Yii::getAlias('@app/web/uploads') . '/' . $chosenFood->author_id;
            $myDirectory = Yii::getAlias('@app/web/uploads') . '/' . Yii::$app->user->id;
            if (!FileHelper::createDirectory($myDirectory)) {
                $this->err('Błąd tworzenia folderu użytkownika!');
                return $this->goBack();
            }
            if (!copy($hisDirectory . '/' . $chosenFood->screen, $myDirectory . '/' . $same->screen)) {
                $this->err('Błąd kopiowania screena użytkownika!');
                return $this->goBack();
            }
        }

        if ($same->save()) {
            $this->ok('Zamówienie zostało skopiowane!');
        } else {
            $this->err('Błąd przy kopiowaniu zamówienia!');
        }
        return $this->goBack();
    }

    /**
     * Food history
     * @return string
     */
    public function actionHistory()
    {
        $searchModel = new HistorySearch;
        $dataProvider = $searchModel->history(Yii::$app->request->queryParams);

        return $this->render('history', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Restaurants
     * @return string
     */
    public function actionRestaurants()
    {
        $searchModel = new RestaurantSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('restaurants', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

//    public function beforeAction($action)
//    {
//        if (parent::beforeAction($action) && isset($_SERVER['HTTP_USER_AGENT'])) {
//            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
//            if(stripos($ua,'android') !== false) {
//                echo '<pre>██╗   ██╗ ██████╗ ██╗   ██╗
//╚██╗ ██╔╝██╔═══██╗██║   ██║
// ╚████╔╝ ██║   ██║██║   ██║
//  ╚██╔╝  ██║   ██║██║   ██║
//   ██║   ╚██████╔╝╚██████╔╝
//   ╚═╝    ╚═════╝  ╚═════╝
//
//██╗  ██╗ █████╗ ██╗   ██╗███████╗
//██║  ██║██╔══██╗██║   ██║██╔════╝
//███████║███████║██║   ██║█████╗
//██╔══██║██╔══██║╚██╗ ██╔╝██╔══╝
//██║  ██║██║  ██║ ╚████╔╝ ███████╗
//╚═╝  ╚═╝╚═╝  ╚═╝  ╚═══╝  ╚══════╝
//
//██████╗ ███████╗███████╗███╗   ██╗
//██╔══██╗██╔════╝██╔════╝████╗  ██║
//██████╔╝█████╗  █████╗  ██╔██╗ ██║
//██╔══██╗██╔══╝  ██╔══╝  ██║╚██╗██║
//██████╔╝███████╗███████╗██║ ╚████║
//╚═════╝ ╚══════╝╚══════╝╚═╝  ╚═══╝
//
//██╗  ██╗ █████╗ ██╗  ██╗██╗  ██╗███████╗██████╗ ██╗
//██║  ██║██╔══██╗╚██╗██╔╝╚██╗██╔╝██╔════╝██╔══██╗██║
//███████║███████║ ╚███╔╝  ╚███╔╝ █████╗  ██║  ██║██║
//██╔══██║██╔══██║ ██╔██╗  ██╔██╗ ██╔══╝  ██║  ██║╚═╝
//██║  ██║██║  ██║██╔╝ ██╗██╔╝ ██╗███████╗██████╔╝██╗
//╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚═════╝ ╚═╝
//                                                   </pre>';
//
//
//                exit();
//            }
//        }
//        return true;
//    }
}
