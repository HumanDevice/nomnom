<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * "{{%project}}".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $project_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Group[] $groups
 */
class Project extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project}}';
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
            [['name', 'url', 'project_id'], 'required'],
            [['name', 'url'], 'string', 'max' => 255],
            ['url', 'url'],
            ['project_id', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nazwa',
            'url' => 'URL',
        ];
    }

    /**
     * Adds new project.
     * @param string $name
     * @param string $url
     * @param int $id
     * @return bool
     */
    public static function addNew($name, $url, $id)
    {
        $old = static::findOne(['project_id' => $id]);
        if ($old) {
            return false;
        }
        $project = new static;
        $project->name = $name;
        $project->url = $url;
        $project->project_id = $id;
        return $project->save();
    }

    /**
     * Returns group_project relation.
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjects()
    {
        return $this->hasMany(GroupProject::class, ['project_id' => 'id']);
    }

    /**
     * Returns group relation.
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::class, ['id' => 'group_id'])->via('groupProjects');
    }
}
