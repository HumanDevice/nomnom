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
        $pref = Preference::find()->all();
        /* @var $p Preference */
        foreach ($pref as $p) {
            if (isset($this->restaurants[$p->restaurant_id])) {
                $this->restaurants[$p->restaurant_id] = $p->like;
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
                $allRestaurants = Restaurant::getList();
                foreach ($this->restaurants as $id => $pref) {
                    if (!in_array($id, array_keys($allRestaurants))) {
                        throw new Exception('ID spoza dozwolonego zakresu!');
                    }
                    Preference::updateAll([
                            'like' => $pref,
                            'updated_at' => time()
                        ], ['and',
                        [
                            'restaurant_id' => $id
                        ],
                        ['!=', 'like', $pref]
                    ]);
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
