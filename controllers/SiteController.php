<?php

namespace app\controllers;

use app\models\Balance;
use app\models\FoodForm;
use app\models\FoodSearch;
use app\models\HistorySearch;
use app\models\LoginForm;
use app\models\Order;
use app\models\OrderChoice;
use app\models\OrderFood;
use app\models\ResetForm;
use app\models\Restaurant;
use app\models\RestaurantSearch;
use app\models\StartForm;
use app\models\User;
use Exception;
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
                        'actions' => ['login', 'start', 'reset', 'newpass'],
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
     * Password reset part 1.
     * @return string
     */
    public function actionReset()
    {
        $model = new ResetForm;
        if ($model->load(Yii::$app->request->post()) && $model->reset()) {
            $this->ok('Email został wysłany.');
            return $this->goBack();
        }
        return $this->render('reset', ['model' => $model]);
    }

    /**
     * Password reset part 2.
     * @param string $token
     * @return string
     */
    public function actionNewpass($token)
    {
        $model = User::findOne(['auth_key' => $token]);
        if (!$model) {
            $this->err('Nieprawidłowy token!');
            return $this->goBack();
        }
        $model->generateAuthKey();
        $model->password_hash = null;
        if (!$model->save()) {
            $this->err('Nie udało się zresetować hasła.');
            return $this->goBack();
        }
        $this->ok('Hasło zresetowane. Ustaw nowe.');
        return $this->redirect(['site/start']);
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

        $model = new LoginForm;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', ['model' => $model]);
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
                ->limit(1)->one();

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
            $model->screen = null;//UploadedFile::getInstance($model, 'screen');
            if ($model->validate()) {
//                if (empty($model->code) && empty($model->screen)) {
//                    $model->addError('code', 'Musisz wskazać, co chcesz zamówić.');
//                } else {
                $result = $model->order();
                if ($result === true) {
                    $this->ok('Zamówienie złożone.');
                    return $this->goHome();
                }
                $this->err($result);
//                }
            }
        }

        $ordered = OrderFood::find()->where(['and',
            ['order_id' => $order->id],
            ['or',
                ['author_id' => Yii::$app->user->id],
                ['with' => Yii::$app->user->id]
            ]
        ])->limit(1)->one();

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
        if (in_array(Yii::$app->user->id, User::BOOKKEEPER) && Yii::$app->request->post('food_id')) {
            $food = OrderFood::findOne(Yii::$app->request->post('food_id'));
            if (empty($food)) {
                $this->err('Nie znaleziono zamówienia!');
                return $this->goBack();
            }
            $food->code = Yii::$app->request->post('code');
            $food->price = Yii::$app->request->post('price');
            if (!$food->save()) {
                $this->err('Błąd zapisu zamówienia!');
            } else {
                $this->ok('Zamówienie poprawione.');
            }
            return $this->goBack();
        }

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
        if ((new Query)->from(OrderChoice::tableName())->where([
                    'order_id' => $chosenOrder->id,
                    'author_id' => Yii::$app->user->id,
                ])->exists()) {
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
        if (!empty($chosenFood->with)) {
            $this->err('Zamówienia grupowego nie można kopiować!');
            return $this->goBack();
        }
        if ($chosenFood->author_id == 22) {
            $this->err('Zamówienia szefa nie można kopiować!');
            return $this->goBack();
        }

        if (Yii::$app->user->id != 22) {
            $balance = Yii::$app->user->identity->balance;
            $max = $balance - 2.5 > 0 ? $balance - 2.5 + 20: 20;
            if ($max > 99.99) {
                $max = 99.99;
            }
            if ($chosenFood->price > $max) {
                $this->err('Brak wymaganych środków na koncie!');
                return $this->goBack();
            }
        }

        $alreadyOrdered = OrderFood::find()->where(['and',
            ['order_id' => $chosenFood->order_id],
            ['or',
                ['author_id' => Yii::$app->user->id],
                ['with' => Yii::$app->user->id]
            ]
        ])->limit(1)->one();
        if (!empty($alreadyOrdered)) {
            $this->err('Usuń najpierw swoje poprzednie zamówienie, aby je zmienić!');
            return $this->goBack();
        }

        $same = new OrderFood;
        $same->author_id = Yii::$app->user->id;
        $same->order_id = $chosenFood->order_id;
        $same->restaurant_id = $chosenFood->restaurant_id;
        $same->code = $chosenFood->code;
        $same->price = $chosenFood->price;
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
     * Takes money from account.
     * @param int $id
     */
    public function actionDebet($id)
    {
        if (!in_array(Yii::$app->user->id, User::BOOKKEEPER)) {
            $this->err('Tylko księgowość ma tu dostęp!');
            return $this->redirect(['site/index']);
        }
        $food = OrderFood::findOne($id);
        if (empty($food)) {
            $this->err('Nie znaleziono zamówienia o tym ID!');
            return $this->redirect(['site/index']);
        }
        if ($food->balanced) {
            $this->err('Zamówienie zostało już rozliczone!');
            return $this->redirect(['site/index']);
        }
        if (empty($food->with) && $food->price > 20 || !empty($food->with) && $food->price > 40) {
            if (empty($food->with)) {
                $debet = number_format($food->price - 20 + 2.5, 2);
            } else {
                $debet = number_format($food->price - 40 + 2.5, 2);
            }
            if ($food->author->balance < $debet) {
                $this->err('Pracownik nie ma wymaganej kwote na koncie!');
                return $this->redirect(['site/index']);
            }
            $trans = Yii::$app->db->beginTransaction();
            try {
                $balance = new Balance;
                $balance->operator_id = Yii::$app->user->id;
                $balance->food_id = $food->id;
                $balance->user_id = $food->author->id;
                $balance->value = -$debet;
                if (!$balance->save()) {
                    throw new Exception('Balance adding error!');
                }
                $food->author->balance = $food->author->balance - $debet;
                if (!$food->author->save()) {
                    throw new Exception('Account charging error!');
                }
                $food->balanced = 1;
                if (!$food->save()) {
                    throw new Exception('Order balancing error!');
                }
                $trans->commit();
                $this->ok('Konto zostało obciążone.');
                return $this->redirect(['site/index']);
            } catch (Exception $exc) {
                $trans->rollBack();
                Yii::error($exc->getMessage());
                $this->err('Błąd podczas obciążania konta!');
                return $this->redirect(['site/index']);
            }
        }
        $this->err('Kwota zamówienia nie wymaga rozliczenia!');
        return $this->redirect(['site/index']);
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
}
