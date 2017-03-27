<?php

namespace app\models;

use const SORT_NATURAL;
use function var_dump;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * TimeSearch
 *
 * @property int $summaryReport
 * @property array $summaryReportTabs
 */
class TimeSearch extends Time
{
    /**
     * @var int sum of seconds spent on issues
     */
    public $summary;

    /**
     * @var int group ID
     */
    public $group_id;

    /**
     * @var string date from
     */
    public $from;
    /**
     * @var string date to
     */
    public $to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'issue_id', 'user_id', 'group_id'], 'integer'],
            ['description', 'string'],
            [['from', 'to'], 'date', 'format' => 'y-MM-dd HH:mm'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'from' => 'Od',
            'to' => 'Do',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Returns sum of seconds.
     * @return int
     */
    public function getSummaryReport()
    {
        $projectsFromGroup = null;
        if ($this->group_id) {
            $projectsGroup = (new Query)->from(GroupProject::tableName())
                ->select(Project::tableName() . '.project_id')
                ->where(['group_id' => $this->group_id])
                ->leftJoin(Project::tableName(), 'id = group_project.project_id')->all();
            foreach ($projectsGroup as $pg) {
                $projectsFromGroup[] = $pg['project_id'];
            }
        }
        $sumQuery = Time::find()
            ->andFilterWhere(['time.user_id' => $this->user_id])
            ->andFilterWhere(['time.project_id' => $this->project_id])
            ->andFilterWhere(['time.project_id' => $projectsFromGroup])
            ->andFilterWhere(['time.issue_id' => $this->issue_id])
            ->andFilterWhere(['like', 'time.description', $this->description]);
        if ($this->from) {
            $sumQuery->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
        }
        if ($this->to) {
            $sumQuery->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
        }
        return $sumQuery->sum('seconds');
    }

    /**
     * Returns summary tabs.
     * @return array
     */
    public function getSummaryReportTabs()
    {
        $projectsFromGroup = null;
        if ($this->group_id) {
            $projectsGroup = (new Query)->from(GroupProject::tableName())
                ->select(Project::tableName() . '.project_id')
                ->where(['group_id' => $this->group_id])
                ->leftJoin(Project::tableName(), 'id = group_project.project_id')->all();
            foreach ($projectsGroup as $pg) {
                $projectsFromGroup[] = $pg['project_id'];
            }
        }
        $sumQuery = (new Query)->from(Time::tableName())
            ->andFilterWhere(['time.user_id' => $this->user_id])
            ->andFilterWhere(['time.project_id' => $this->project_id])
            ->andFilterWhere(['time.project_id' => $projectsFromGroup])
            ->andFilterWhere(['time.issue_id' => $this->issue_id])
            ->andFilterWhere(['like', 'time.description', $this->description]);
        if ($this->from) {
            $sumQuery->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
        }
        if ($this->to) {
            $sumQuery->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
        }
        $report =  $sumQuery->all();
        $seconds = 0;
        $employees = [];
        $projects = [];
        foreach ($report as $data) {
            $seconds += $data['seconds'];
            if (!isset($employees[$data['user_id']])) {
                $employees[$data['user_id']] = 0;
            }
            $employees[$data['user_id']] += $data['seconds'];
            if (!isset($projects[$data['project_id']])) {
                $projects[$data['project_id']] = 0;
            }
            $projects[$data['project_id']] += $data['seconds'];
        }
        asort($employees, SORT_NATURAL);
        asort($projects, SORT_NATURAL);
        return [
            'seconds' => $seconds,
            'employees' => $employees,
            'projects' => $projects,
        ];
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Time::find()->select(['time.*', 'GROUP_CONCAT(group.name SEPARATOR \', \') as group_names'])
            ->joinWith(['project', 'project.groups', 'issue'])
            ->groupBy(['group_project.group_id', 'group_project.project_id', 'time.id']);
        $sumQuery = Time::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $this->summary = $sumQuery->sum('seconds');
            return $dataProvider;
        }

        $projectsFromGroup = null;
        if ($this->group_id) {
            $projectsGroup = (new Query)->from(GroupProject::tableName())
                ->select(Project::tableName() . '.project_id')
                ->where(['group_id' => $this->group_id])
                ->leftJoin(Project::tableName(), 'id = group_project.project_id')->all();
            foreach ($projectsGroup as $pg) {
                $projectsFromGroup[] = $pg['project_id'];
            }
        }
        $query
            ->andFilterWhere(['time.user_id' => $this->user_id])
            ->andFilterWhere(['time.project_id' => $this->project_id])
            ->andFilterWhere(['time.project_id' => $projectsFromGroup])
            ->andFilterWhere(['time.issue_id' => $this->issue_id])
            ->andFilterWhere(['like', 'time.description', $this->description]);
        $sumQuery
            ->andFilterWhere(['time.user_id' => $this->user_id])
            ->andFilterWhere(['time.project_id' => $this->project_id])
            ->andFilterWhere(['time.project_id' => $projectsFromGroup])
            ->andFilterWhere(['time.issue_id' => $this->issue_id])
            ->andFilterWhere(['like', 'time.description', $this->description]);
        if ($this->from) {
            $query->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
            $sumQuery->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
        }
        if ($this->to) {
            $query->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
            $sumQuery->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
        }

        $this->summary = $sumQuery->sum('seconds');

        return $dataProvider;
    }
}
