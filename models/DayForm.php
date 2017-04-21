<?php

namespace app\models;

/**
 * Day Form
 *
 */
class DayForm extends Calendar
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['offday', 'required'],
            ['offday', 'unique'],
            ['offday', 'date', 'format' => 'y-MM-dd'],
        ];
    }
}
