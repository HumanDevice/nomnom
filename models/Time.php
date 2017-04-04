<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * "{{%time}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $issue_id
 * @property int $project_id
 * @property int $seconds
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Project $project
 * @property Issue $issue
 */
class Time extends ActiveRecord
{
    /**
     * @var string group names
     */
    public $group_names;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%time}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ['time' => TimestampBehavior::class];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['user_id', 'project_id', 'seconds'], 'required'],
            [['user_id', 'project_id', 'seconds'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seconds' => 'Czas',
            'project_id' => 'Projekt',
            'issue_id' => 'Ticket',
            'user_id' => 'Pracownik',
            'created_at' => 'Data',
            'description' => 'Opis',
            'group_id' => 'Grupa projektów',
        ];
    }

    /**
     * Adds spent time for project.
     * @param int $project
     * @param int $issue
     * @param string $user
     * @param int $time
     * @return bool
     */
    public static function addTime($project, $issue, $user, $time)
    {
        $projectModel = Project::findOne(['project_id' => $project]);
        if (!$projectModel) {
            Yii::error('Brak projektu o project_id = ' . $project);
            return false;
        }
        $userModel = User::findOne(['gitlab' => $user]);
        if (!$userModel) {
            Yii::error('Brak usera o gitlab = ' . $user);
            return false;
        }
        $previousEntry = static::find()->where([
            'project_id' => $projectModel->project_id,
            'issue_id' => $issue,
            'user_id' => $userModel->id,
            'seconds' => $time,
        ])->limit(1)->one();
        if ($previousEntry && $previousEntry->created_at > time() - 5) {
            // 5 secs duplicate prevention
            Yii::error('Duplikat wpisu czasowego');
            return false;
        }
        $timeModel = new static;
        $timeModel->project_id = $projectModel->project_id;
        $timeModel->issue_id = $issue;
        $timeModel->user_id = $userModel->id;
        $timeModel->seconds = $time;
        if ($timeModel->save()) {
            return true;
        }
        Yii::error($timeModel->errors);
        return false;
    }

    /**
     * Returns project relation
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['project_id' => 'project_id']);
    }

    /**
     * Returns issue relation
     * @return \yii\db\ActiveQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::class, ['inner_id' => 'issue_id']);
    }
}
