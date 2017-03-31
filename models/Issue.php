<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * "{{%issue}}".
 *
 * @property int $id
 * @property int $project_id
 * @property int $issue_id
 * @property int $inner_id
 * @property int $time
 */
class Issue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%issue}}';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['issue_id', 'project_id', 'time'], 'required'],
            [['issue_id', 'project_id', 'time'], 'integer'],
        ];
    }

    /**
     * Returns time spent on issue.
     * @param int $project
     * @param int $issue
     * @return int
     */
    public static function getTime($project, $issue)
    {
        $issueModel = static::findOne(['project_id' => $project, 'issue_id' => $issue]);
        if (!$issueModel) {
            return 0;
        }
        return $issueModel->time;
    }

    /**
     * Updates time spent on issue.
     * @param int $project
     * @param int $issue
     * @param int $innerIssue
     * @param int $time
     * @return int
     */
    public static function updateTime($project, $issue, $innerIssue, $time)
    {
        $projectModel = Project::findOne(['project_id' => $project]);
        if (!$projectModel) {
            return -1;
        }
        $issueModel = static::findOne([
            'project_id' => $projectModel->project_id,
            'issue_id' => $issue,
            'inner_id' => $innerIssue
        ]);
        if (!$issueModel) {
            $issueModel = new static;
            $issueModel->project_id = $project;
            $issueModel->issue_id = $issue;
            $issueModel->inner_id = $innerIssue;
        }
        $issueModel->time = $time;
        if ($issueModel->save()) {
            return 1;
        }
        Yii::error($issueModel->errors);
        return 0;
    }
}
