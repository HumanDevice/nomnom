<?php

namespace app\commands;

use app\components\HipChat;
use app\models\Order;
use app\models\Restaurant;
use DateTime;
use DateTimeZone;
use Yii;
use yii\console\Controller;

/**
 * NomNomController
 */
class NomNomController extends Controller
{
    /**
     * Starts order (10:50).
     */
    public function actionStart()
    {
        $order = new Order;
        $order->stage = Order::STAGE_VOTE;
        $order->stage_end = Yii::$app->formatter->asTimestamp(
            DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 11:00:00', new DateTimeZone(Yii::$app->timeZone))
        );
        
        if (!$order->save()) {
            Yii::error($order->errors);
            Yii::$app->hipchat->send('(failed) Nie można otworzyć zamówienia!', 'red');
            return Controller::EXIT_CODE_ERROR;
        }
        
        Yii::$app->hipchat->send('@all NomNom otworzył nowe zamówienie!'
            . "\n"
            . 'Jedna restauracja jest już wybrana, ale jeśli masz smaka na coś innego, to przejdź do http://nomnom.projectown.net i zagłosuj na drugą.'
            . "\n"
            . 'Masz czas do 11:00 (10 minut) '
            . HipChat::randomYay());
        
        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * Starts meal choose (11:00).
     */
    public function actionMeal()
    {
        $order = Order::find()->where(['stage' => Order::STAGE_VOTE])->limit(1)->one();
        if (!$order) {
            return Controller::EXIT_CODE_NORMAL;
        }
        
        $restaurantNames = [];
        
        $order->stage = Order::STAGE_MEAL;
        $order->stage_end = Yii::$app->formatter->asTimestamp(
            DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 11:30:00', new DateTimeZone(Yii::$app->timeZone))
        );
        
        $defaultRestaurant = Restaurant::findOne(Yii::$app->params['default_restaurant']);
        if (empty($defaultRestaurant)) {
            Yii::$app->hipchat->send('(failed) Nie można pobrać danych domyślnej restauracji!', 'red');
            return Controller::EXIT_CODE_ERROR;
        }
        
        $order->restaurant_id = $defaultRestaurant->id;
        $restaurantNames[] = $defaultRestaurant->name;
        
        if (!empty($order->winner)) {
            $chosenRestaurant = Restaurant::findOne($order->winner);
            if (empty($chosenRestaurant)) {
                Yii::$app->hipchat->send('(failed) Nie można pobrać danych drugiej restauracji!', 'red');
                return Controller::EXIT_CODE_ERROR;
            }
            $order->restaurant2_id = $chosenRestaurant->id;
            $restaurantNames[] = $chosenRestaurant->name;
        }
        
        if (!$order->save()) {
            Yii::error($order->errors);
            Yii::$app->hipchat->send('(failed) Nie można przejść do wyboru posiłku!', 'red');
            return Controller::EXIT_CODE_ERROR;
        }
        
        $single = count($this->restaurantNames) == 1;
        
        Yii::$app->hipchat->send('@all Naszym' . (!$single ? 'i' : '')
            . ' dzisiejszym' . (!$single ? 'i' : '')
            . ' kucharz' . (!$single ? 'ami' : 'em')
            . ' ' . (!$single ? 'są' : 'jest')
            . ' ' . $restaurantNames[0]
            . (!$single ? ' i ' . $restaurantNames[1] : '')
            . ".\n"
            . 'Przejdź do http://nomnom.projectown.net i zapisz na co masz smaka (do 20zł).'
            . "\n"
            . 'Masz czas do 11:30 (pół godziny) '
            . HipChat::randomYay());
        
        return Controller::EXIT_CODE_NORMAL;
    }
    
    /**
     * Sends timer reminder (11:20).
     */
    public function actionReminder()
    {
        $order = Order::find()->where(['stage' => Order::STAGE_MEAL])->limit(1)->one();
        if (!$order) {
            return Controller::EXIT_CODE_NORMAL;
        }
        
        Yii::$app->hipchat->send('@all Zostało 10 minut do końca wybierania posiłku! ' . HipChat::randomNope(), 'yellow');
        
        return Controller::EXIT_CODE_NORMAL;
    }
    
    /**
     * Ends order (11:30).
     */
    public function actionEnd()
    {
        $order = Order::find()->where(['stage' => Order::STAGE_MEAL])->limit(1)->one();
        if (!$order) {
            return Controller::EXIT_CODE_NORMAL;
        }
        
        Yii::$app->hipchat->send('@all Koniec wybierania posiłku.', 'green');
        
        return Controller::EXIT_CODE_NORMAL;
    }
    
    /**
     * Closes order (15:00).
     */
    public function actionClose()
    {
        $order = Order::find()->where(['!=', 'stage', Order::STAGE_CLOSE])->limit(1)->one();
        if (!$order) {
            return Controller::EXIT_CODE_NORMAL;
        }
        
        $order->stage = Order::STAGE_CLOSE;
        if (!$order->save()) {
            Yii::error($order->errors);
            Yii::$app->hipchat->send('(failed) Nie można zamknąć zamówienia!', 'red');
            return Controller::EXIT_CODE_ERROR;
        }
        
        return Controller::EXIT_CODE_NORMAL;
    }
}
