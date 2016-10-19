<?php

namespace app\models;

use app\components\HipChat;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\base\Model;

/**
 * OrderForm
 *
 */
class OrderForm extends Model
{
    public $restaurant;
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
            ['hour', 'in', 'range' => array_keys($this->hours)],
            ['minute', 'in', 'range' => array_keys($this->minutes)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'restaurant' => 'Sugestia admina'
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
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $order = new Order;
                $order->admin_id = Yii::$app->user->id;
                $order->stage = Order::STAGE_VOTE;
                $order->stage_end = $stage_end;
                if (!$order->save()) {
                    throw new Exception('Nie można stworzyć zamówienia!');
                }
                $choice = new OrderChoice;
                $choice->author_id = Yii::$app->user->id;
                $choice->restaurant_id = $this->restaurant;
                $choice->order_id = $order->id;
                if (!$choice->save()) {
                    throw new Exception('Nie można dodać głosu admina!');
                }
                $transaction->commit();
                return true;
            } catch (Exception $exc) {
                $transaction->rollBack();
                Yii::error($exc->getMessage());
            }
        }
        return implode('<br>', $this->firstErrors);
    }
    
    /**
     * Returns message
     * @return string
     */
    public function getMessage()
    {
        return '@all '
            . Yii::$app->user->identity->username
            . ' otworzył nowe zamówienie!'
            . "\n"
            . 'Przejdź do http://nomnom.projectown.net i oddaj głos na restaurację, z której chcesz zamówić.'
            . "\n"
            . 'Masz czas do '
            . $this->hour
            . ':'
            . $this->minute
            . ' '
            . HipChat::randomYay();
    }
}
