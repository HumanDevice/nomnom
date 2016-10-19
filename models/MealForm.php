<?php

namespace app\models;

use app\components\HipChat;
use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Model;

/**
 * MealForm
 *
 */
class MealForm extends Model
{
    public $restaurant = [];
    public $restaurantNames = [];
    public $hour;
    public $minute;
    public $hours = ['08' => '08', '09' => '09', 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16];
    public $minutes = ['00' => '00', 15 => 15, 30 => 30, 45 => 45];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['restaurant', 'hour', 'minutes'], 'required'],
            ['restaurant', 'each', 'rule' => ['integer']],
            ['hour', 'in', 'range' => array_keys($this->hours)],
            ['minute', 'in', 'range' => array_keys($this->minutes)],
        ];
    }
    
    /**
     * Sets default values.
     */
    public function init()
    {
        parent::init();
        $this->hour = Yii::$app->formatter->asDatetime('+1 hour', 'H');
        $this->minute = 0;
    }

    /**
     * Labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'restaurant' => 'Restauracja, w której dzisiaj jemy,'
        ];
    }
    
    /**
     * Starts order.
     * @return boolean
     */
    public function start()
    {
        if ($this->validate()) {
            $stage_end = Yii::$app->formatter->asTimestamp(
                DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . $this->hour . ':' . $this->minute . ':00', new DateTimeZone(Yii::$app->timeZone))
            );
            if ($stage_end < time()) {
                return 'Wybrana godzina już upłynęła.';
            }
            
            $order = Order::find()->where([
                'and',
                ['stage' => Order::STAGE_VOTE],
                ['<', 'stage_end', time()]
            ])->limit(1)->one();
            if (empty($order)) {
                return 'Brak zamówienia na odpowiednim etapie!';
            }
            
            $chosenRestaurant = OrderChoice::find()->where([
                'order_id' => $order->id,
                'restaurant_id' => $this->restaurant
            ])->groupBy('restaurant_id')->all();
            if (empty($chosenRestaurant)) {
                return 'Musisz wskazać restaurację spośród głosów!';
            }
            if (count($this->restaurant) > 2) {
                return 'Możesz wskazać maksymalnie 2 restauracje spośród głosów!';
            }
            
            $order->stage = Order::STAGE_MEAL;
            $order->stage_end = $stage_end;
            $attr = 'restaurant_id';
            foreach ($chosenRestaurant as $chRest) {
                $order->$attr = $chRest->restaurant_id;
                $this->restaurantNames[] = $chRest->restaurant->name;
                $attr = 'restaurant2_id';
            }
            if (!$order->save()) {
                return 'Błąd przy zmianie etapu zamówienia!';
            }
            return true;
        }
        return implode('<br>', $this->firstErrors);
    }
    
    /**
     * Returns message
     * @return string
     */
    public function getMessage()
    {
        $single = count($this->restaurantNames) == 1;
        return '@all Restauracj'
            . ($single ? 'a' : 'e')
            . ' został'
            . ($single == 1 ? 'a' : 'y')
            . ' wybran'
            . ($single == 1 ? 'a' : 'e')
            . '! Dzisiaj jemy w '
            . $this->restaurantNames[0]
            . (!$single ? ' i ' . $this->restaurantNames[1] : '')
            . ".\n"
            . 'Przejdź do http://nomnom.projectown.net i zapisz, co chcesz dzisiaj zjeść.'
            . "\n"
            . 'Masz czas do '
            . $this->hour
            . ':'
            . $this->minute
            . ' '
            . HipChat::randomYay();
    }
}
