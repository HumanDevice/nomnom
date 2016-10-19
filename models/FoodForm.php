<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * FoodForm
 *
 */
class FoodForm extends Model
{
    public $restaurant;
    public $code;
    public $screen;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['restaurant', 'required'],
            ['restaurant', 'integer'],
            ['code', 'string', 'max' => 255],
            ['screen', 'image', 'extensions' => 'png, jpg, gif', 'maxWidth' => 1000, 'maxHeight' => 1000, 'mimeTypes' => 'image/*', 'maxSize' => 1024 * 1024],
        ];
    }
    
    /**
     * Labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'restaurant' => 'Restauracja',
            'code' => 'Wybrany posiłek',
            'screen' => 'Opcjonalny screen z zaznaczonymi opcjami posiłku w menu'
        ];
    }
    
    /**
     * Adds order.
     */
    public function order()
    {
        $order = Order::find()->where([
            'and',
            ['stage' => Order::STAGE_MEAL],
            ['>', 'stage_end', time()]
        ])->limit(1)->one();
        if (empty($order)) {
            return 'Brak zamówienia na odpowiednim etapie!';
        }

        $food = new OrderFood;
        $food->author_id = Yii::$app->user->id;
        $food->order_id = $order->id;
        $food->restaurant_id = $this->restaurant;
        $food->code = $this->code;
        if ($this->screen instanceof UploadedFile) {
            $directory = Yii::getAlias('@app/web/uploads') . '/' . Yii::$app->user->id;
            if (!FileHelper::createDirectory($directory)) {
                return 'Błąd tworzenia folderu użytkownika!';
            }
            $file = Yii::$app->security->generateRandomString(10) . '.' . $this->screen->extension;
            if (!$this->screen->saveAs($directory . '/' . $file)) {
                return 'Błąd zapisu screena!';
            }
            $food->screen = $file;
        } else {
            $food->screen = null;
        }
        if (!$food->save()) {
            return 'Błąd zapisu zamówienia!';
        }
        return true;
    }
}
