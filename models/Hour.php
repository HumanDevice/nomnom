<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * "{{%hour}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $monday_odd
 * @property string $monday_even
 * @property string $tuesday_odd
 * @property string $tuesday_even
 * @property string $wednesday_odd
 * @property string $wednesday_even
 * @property string $thursday_odd
 * @property string $thursday_even
 * @property string $friday_odd
 * @property string $friday_even
 * @property int $updated_at
 * @property int $created_at
 * @property string $vacation
 *
 * @property User $user
 */
class Hour extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hour}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['monday_odd', 'monday_even', 'tuesday_odd', 'tuesday_even',
                'wednesday_odd', 'wednesday_even', 'thursday_odd',
                'thursday_even', 'friday_odd', 'friday_even', 'vacation'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'monday_odd' => 'Pn (N)',
            'monday_even' => 'Pn (P)',
            'tuesday_odd' => 'Wt (N)',
            'tuesday_even' => 'Wt (p)',
            'wednesday_odd' => 'Śr (N)',
            'wednesday_even' => 'Śr (P)',
            'thursday_odd' => 'Cz (N)',
            'thursday_even' => 'Cz (P)',
            'friday_odd' => 'Pt (N)',
            'friday_even' => 'Pt (P)',
            'vacation' => 'Urlop',
            'username' => 'Pracownik',
            'division' => 'Grupa',
        ];
    }

    /**
     * User relation
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
