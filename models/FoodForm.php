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
    public $price = '00.00';
    public $with = 0;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $balance = Yii::$app->user->identity->balance;
        $max = $balance - 2.5 > 0 ? $balance - 2.5 + 20: 20;
        $max2 = $balance - 2.5 > 0 ? $balance - 2.5 + 40: 40;
        if ($max > 99.99) {
            $max = 99.99;
        }
        if ($max2 > 99.99) {
            $max2 = 99.99;
        }
        $rules = [
            [['restaurant', 'code', 'price', 'with'], 'required'],
            ['restaurant', 'integer'],
            ['with', 'integer'],
            ['code', 'string'],
            ['screen', 'image', 'extensions' => 'png, jpg, gif', 'maxWidth' => 1000, 'maxHeight' => 1000, 'mimeTypes' => 'image/*', 'maxSize' => 1024 * 1024],
        ];
        if (Yii::$app->user->id == 22) {
            $rules[] = ['price', 'number', 'min' => 0];
        } else {
            $rules[] = ['price', 'number', 'min' => 0.01];
            $rules[] = ['price', 'number', 'max' => $max, 'when' => function ($model) {
                return $model->with == 0;
            }, 'whenClient' => 'function (attribute, value) { return $("#with").val() == 0; }'];
            $rules[] = ['price', 'number', 'max' => $max2, 'when' => function ($model) {
                return $model->with != 0;
            }, 'whenClient' => 'function (attribute, value) { return $("#with").val() != 0; }'];
        }
        return $rules;
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
            'price' => 'Kwota łącznie',
            'with' => 'Wspólnie z',
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

        $alreadyOrdered = OrderFood::find()->where(['and',
            ['order_id' => $order->id],
            ['or',
                ['author_id' => Yii::$app->user->id],
                ['with' => Yii::$app->user->id]
            ]
        ])->limit(1)->one();
        if (!empty($alreadyOrdered)) {
            return 'Usuń najpierw swoje poprzednie zamówienie, aby je zmienić!';
        }

        if (!empty($this->with)) {
            $checkWith = User::findOne(['id' => $this->with, 'deleted' => 0]);
            if (empty($checkWith)) {
                return 'Coś jest nie tak z wybranym współbiesiadnikiem!';
            }
            $availableList = static::withList($order->id);
            if (!in_array($this->with, array_keys($availableList))) {
                return 'Wybrany współbiesiadnik już prawdopodobnie zamówił!';
            }
        }

        $food = new OrderFood;
        $food->author_id = Yii::$app->user->id;
        $food->order_id = $order->id;
        $food->restaurant_id = $this->restaurant;
        $food->code = $this->code;
        $food->price = $this->price;
        $food->with = $this->with;
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

    /**
     * Returns list of users except current one that did not order yet.
     * @param int $order_id
     * @return array
     */
    public static function withList($order_id)
    {
        $users = User::find()->where(['and',
            ['<>', 'id', Yii::$app->user->id],
            ['deleted' => 0]
        ])->orderBy(['username' => SORT_ASC])->all();
        $orders = OrderFood::find()->where(['order_id' => $order_id])->all();
        $alreadyOrdered = [];
        foreach ($orders as $order) {
            $alreadyOrdered[] = $order->author_id;
            if (!empty($order->with)) {
                $alreadyOrdered[] = $order->with;
            }
        }
        $listWith = [];
        foreach ($users as $user) {
            if (!in_array($user->id, $alreadyOrdered)) {
                $listWith[$user->id] = $user->username;
            }
        }
        return $listWith;
    }
}
