<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Time Form
 *
 */
class TimeForm extends Time
{
    /**
     * @var string date for custom time spent
     */
    public $date;

    /**
     * @var string time spent
     */
    public $time;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['project_id', 'time', 'date', 'description'], 'required'],
            ['project_id', 'integer'],
            ['description', 'string', 'min' => 1, 'max' => 255],
            ['date', 'date', 'format' => 'd.M.y'],
            ['time', 'match', 'pattern' => '/[0-9]?[0-9]:[0-9]{2}/', 'message' => 'Format czasu jest nieprawidłowy'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'time' => 'Czas',
            'project_id' => 'Projekt',
            'date' => 'Data',
            'description' => 'Opis',
        ];
    }

    /**
     * Converts time in format HH:MM to seconds.
     */
    public function prepareSeconds()
    {
        $time = explode(':', $this->time);
        $this->seconds = (int)$time[0] * 60 * 60 + (int)$time[1] * 60;
    }

    /**
     * Converts date to timestamp.
     */
    public function prepareDate()
    {
        $this->created_at = Yii::$app->formatter->asTimestamp(date_create($this->date, timezone_open('Europe/Warsaw')));
        $this->getBehavior('time')->attributes[ActiveRecord::EVENT_BEFORE_INSERT] = 'updated_at';
    }

    /**
     * Converts DB values for the form to update.
     */
    public function prepareUpdate()
    {
        $minutes = $this->seconds / 60;
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;
        $this->time = ($hours < 10 ? '0' : '') . $hours . ':' . ($minutes < 10 ? '0' : '') . $minutes;
        $this->date = Yii::$app->formatter->asDate($this->created_at);
    }

    /**
     * Adds time spent entry.
     * @return bool
     */
    public function add()
    {
        if ($this->validate()) {
            $this->user_id = Yii::$app->user->id;
            $this->prepareDate();
            $this->prepareSeconds();
            return $this->save();
        }
    }
}
