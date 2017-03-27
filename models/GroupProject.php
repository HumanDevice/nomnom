<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * "{{%group_project}}".
 *
 * @property int $group_id
 * @property int $project_id
 */
class GroupProject extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group_project}}';
    }

    /**
     * Returns project relation.
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * Returns group relation.
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }
}
