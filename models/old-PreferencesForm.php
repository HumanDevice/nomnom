<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;

/**
 * PreferencesForm
 *
 */
class PreferencesForm extends Model
{
    public $restaurants;
    public $alreadySet = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['restaurants', 'required'],
            ['restaurants', 'each', 'rule' => ['boolean']],
        ];
    }

    /**
     * Sets all preferences.
     */
    public function init()
    {
        parent::init();
        $allRestaurants = Restaurant::getList();
        foreach ($allRestaurants as $id => $restaurant) {
            $this->restaurants[$id] = 1;
        }
        $my = Yii::$app->user->identity->preferences;
        /* @var $pref Preference */
        foreach ($my as $pref) {
            $this->alreadySet[] = $pref->restaurant_id;
            if (isset($this->restaurants[$pref->restaurant_id])) {
                $this->restaurants[$pref->restaurant_id] = $pref->like;
            }
        }
    }
    
    /**
     * Saves preferences.
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $inserts = [];
                $allRestaurants = Restaurant::getList();
                foreach ($this->restaurants as $id => $pref) {
                    if (!in_array($id, array_keys($allRestaurants))) {
                        throw new Exception('ID spoza dozwolonego zakresu!');
                    }
                    if (!in_array($id, $this->alreadySet)) {
                        $inserts[] = [Yii::$app->user->id, $id, $pref, time(), time()];
                    } else {
                        Preference::updateAll([
                                'like' => $pref,
                                'updated_at' => time()
                            ], ['and',
                            [
                                'user_id' => Yii::$app->user->id,
                                'restaurant_id' => $id
                            ],
                            ['!=', 'like', $pref]
                        ]);
                    }
                }
                if (!empty($inserts)) {
                    Yii::$app->db->createCommand()->batchInsert(
                        Preference::tableName(), ['user_id', 'restaurant_id', 'like', 'created_at', 'updated_at'], $inserts
                    )->execute();
                }
                Yii::$app->user->identity->new_restaurants = 0;
                if (!Yii::$app->user->identity->save()) {
                    throw new Exception('Nie można zmienić flagi nowych restauracji!');
                }
                $transaction->commit();
                return true;
            } catch (Exception $exc) {
                $transaction->rollBack();
                Yii::error($exc->getMessage());
            }
        }
        return false;
    }
}
