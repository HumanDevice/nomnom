<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * "{{%group}}".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Project[] $projects
 */
class Group extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'min' => 1, 'max' => 255],
            ['name', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nazwa',
        ];
    }

    /**
     * Returns group_project relation.
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjects()
    {
        return $this->hasMany(GroupProject::class, ['group_id' => 'id']);
    }

    /**
     * Returns project relation via group_project.
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'project_id'])->via('groupProjects');
    }
}
