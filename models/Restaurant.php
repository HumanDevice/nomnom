<?php

namespace app\models;

use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


/**
 * "{{%restaurant}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $old_name
 * @property string $url
 * @property string $screen
 * @property integer $max
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 * 
 * @property string $restaurantName
 * @property string $short
 */
class Restaurant extends ActiveRecord
{
    public $preferred = 1;
    public $stay = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%restaurant}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'preferred', 'max'], 'required'],
            ['name', 'unique'],
            ['max', 'integer', 'min' => 1],
            [['preferred', 'stay'], 'boolean'],
            ['url', 'url', 'defaultScheme' => 'http'],
            ['screen', 'image', 'extensions' => 'png, jpg, gif', 'maxWidth' => 3000, 'maxHeight' => 3000, 'mimeTypes' => 'image/*', 'maxSize' => 5 * 1024 * 1024],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nazwa',
            'stay' => 'Nie usuwaj zdjęcia',
            'url' => 'Link do menu',
            'screen' => 'Zdjęcie menu opcjonalnie',
            'max' => 'Maksymalna ilość restauracji, z których można zamówić na raz',
            'preferred' => 'Wszyscy mogą tu zamawiać'
        ];
    }

    /**
     * Returns actual restaurant name.
     * @return string
     */
    public function getRestaurantName()
    {
        return $this->deleted ? $this->old_name : $this->name;
    }
    
    /**
     * Adds new restaurant and changes all user new restaurant flags.
     * @return boolean
     */
    public function add()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->screen instanceof UploadedFile) {
                $directory = Yii::getAlias('@app/web/uploads') . '/menu';
                if (!FileHelper::createDirectory($directory)) {
                    return 'Błąd tworzenia folderu menu!';
                }
                $file = Yii::$app->security->generateRandomString(10) . '.' . $this->screen->extension;
                if (!$this->screen->saveAs($directory . '/' . $file)) {
                    return 'Błąd zapisu screena!';
                }
                $this->screen = $file;
            } else {
                $this->screen = null;
            }
            if (!$this->save(false)) {
                throw new Exception('Nie udało się zapisać restauracji!');
            }
            $preference = new Preference;
            $preference->restaurant_id = $this->id;
            $preference->like = 1;
            if (!$preference->save()) {
                throw new Exception('Nie udało się zapisać preferencji!');
            }
            $transaction->commit();
            return true;
        } catch (Exception $exc) {
            $transaction->rollBack();
            Yii::error($exc->getMessage());
        }
        return false;
    }
    
    /**
     * Modifies restaurant.
     * @return boolean
     */
    public function modify()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->screen instanceof UploadedFile) {
                $directory = Yii::getAlias('@app/web/uploads') . '/menu';
                if (!FileHelper::createDirectory($directory)) {
                    return 'Błąd tworzenia folderu menu!';
                }
                $file = Yii::$app->security->generateRandomString(10) . '.' . $this->screen->extension;
                if (!$this->screen->saveAs($directory . '/' . $file)) {
                    return 'Błąd zapisu screena!';
                }
                $this->screen = $file;
            }
            if (!$this->save(false)) {
                throw new Exception('Nie udało się zapisać restauracji!');
            }
            $transaction->commit();
            return true;
        } catch (Exception $exc) {
            $transaction->rollBack();
            Yii::error($exc->getMessage());
        }
        return false;
    }
    
    /**
     * Returns list of restaurants.
     * @return array
     */
    public static function getList()
    {
        $all = Restaurant::find()->where(['deleted' => 0])->asArray()->orderBy(['name' => SORT_ASC])->all();
        $list = [];
        foreach ($all as $restaurant) {
            $list[$restaurant['id']] = [
                'name' => $restaurant['name'],
                'url' => $restaurant['url'],
                'screen' => $restaurant['screen'],
            ];
        }
        return $list;
    }
    
    public function getPreference()
    {
        return $this->hasOne(Preference::className(), ['restaurant_id' => 'id']);
    }
    
    /**
     * Returns list of restaurants with preferences.
     * @return array
     */
    public static function getDetailedList()
    {
        $all = Restaurant::find()->where(['deleted' => 0])->orderBy(['name' => SORT_ASC])->all();
        $list = [];
        foreach ($all as $restaurant) {
            $list[$restaurant->id] = [
                'name' => $restaurant->name,
                'url' => $restaurant->url,
                'screen' => $restaurant->screen,
                'like' => $restaurant->preference->like,
            ];
        }
        return $list;
    }
    
    /**
     * Return short name.
     * @return string
     */
    public function getShort()
    {
        $split = explode(' ', $this->name);
        $short = '';
        foreach ($split as $part) {
            if (strlen($part)) {
                $short .= $part{0};
            }
        }
        
        return mb_strtoupper($short, 'UTF-8');
    }
}
