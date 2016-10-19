<?php

namespace app\commands;

use app\models\Order;
use Yii;
use yii\console\Controller;

/**
 * NomBotController
 */
class NomBotController extends Controller
{
    /**
     * Sends timer notification to HipChat.
     */
    public function actionIndex()
    {
        $order = Order::find()->where(['!=', 'stage', Order::STAGE_CLOSE])->limit(1)->one();
        if (!$order) {
            return Controller::EXIT_CODE_NORMAL;
        }
        
        if ($order->stage_end - 9 * 60 - 30 > time() && time() > $order->stage_end - 11 * 60 + 30) {
            $message = '@all Zostało 10 minut do końca ';
            if ($order->stage == Order::STAGE_VOTE) {
                $message .= 'głosowania! (watchingyou)';
            } else {
                $message .= 'wybierania posiłku! (zmiana)';
            }
            Yii::$app->hipchat->send($message, 'red');
        } elseif ($order->stage_end + 30 > time() && time() > $order->stage_end - 30) {
            $message = '@all Koniec ';
            if ($order->stage == Order::STAGE_VOTE) {
                $message .= 'głosowania.';
            } else {
                $message .= 'wybierania posiłku.';
            }
            Yii::$app->hipchat->send($message, 'green');
        }
        
        return Controller::EXIT_CODE_NORMAL;
    }
    
    /**
     * Test HipChat bot.
     */
    public function actionTest($msg, $color = null)
    {
        $this->stdout('Msg: ' . $msg . "\n");
        $this->stdout('Color: ' . $color . "\n");
        if ($msg) {
            $resp = Yii::$app->hipchat->send($msg, $color, 1);
            $this->stdout('Response: ' . ($resp ? 'true' : 'false') . "\n");
        }
        return Controller::EXIT_CODE_NORMAL;
    }
}
