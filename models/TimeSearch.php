<?php

namespace app\models;

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
     * @var array off days
     */
    public $offdays = [];

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
        $summary = $sumQuery->sum('seconds');
        return $this->formatSummary(is_numeric($summary) ? $summary : 0);
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
        $efficiency = [];
        $projects = [];
        foreach ($report as $data) {
            $seconds += $data['seconds'];
            if (!isset($employees[$data['user_id']])) {
                $employees[$data['user_id']] = 0;
            }
            if (!isset($efficiency[$data['user_id']])) {
                $efficiency[$data['user_id']] = [
                    'curr' => $this->currentMonthWorkingTimeOf($data['user_id']),
                    'prev' => $this->previousMonthWorkingTimeOf($data['user_id']),
                ];
            }
            $employees[$data['user_id']] += $data['seconds'];
            if (!isset($projects[$data['project_id']])) {
                $projects[$data['project_id']] = 0;
            }
            $projects[$data['project_id']] += $data['seconds'];
        }
        arsort($employees, SORT_NATURAL);
        arsort($projects, SORT_NATURAL);

        return [
            'seconds' => $seconds,
            'employees' => $employees,
            'projects' => $projects,
            'efficiency' => $efficiency,
        ];
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Time::find()->select(['time.*', 'GROUP_CONCAT(DISTINCT group.name SEPARATOR \', \') as group_names'])
            ->joinWith(['project', 'project.groups', 'issue'])
            ->groupBy(['group_project.project_id', 'time.id']);
        $sumQuery = Time::find();
        $offQuery = Calendar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if ($this->from) {
            $query->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
            $sumQuery->andFilterWhere(['>=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->from, timezone_open('Europe/Warsaw')))]);
            $offQuery->andFilterWhere(['>=', 'offday', Yii::$app->formatter->asDate(date_create($this->from, timezone_open('Europe/Warsaw')), 'y-MM-dd')]);
        }
        if ($this->to) {
            $query->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
            $sumQuery->andFilterWhere(['<=', 'time.created_at', Yii::$app->formatter->asTimestamp(date_create($this->to, timezone_open('Europe/Warsaw')))]);
            $offQuery->andFilterWhere(['<=', 'offday', Yii::$app->formatter->asDate(date_create($this->to, timezone_open('Europe/Warsaw')), 'y-MM-dd')]);
        }

        $this->offdays = $offQuery->asArray()->all();

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

        $this->summary = $sumQuery->sum('seconds');

        return $dataProvider;
    }

    /**
     * Formats number of seconds.
     * Normally asDuration() could be used but here we need units smaller than days.
     * @param int $seconds
     * @param bool $decimal
     * @return string
     */
    public function formatSummary($seconds, $decimal = false)
    {
        $minus = false;
        if ($seconds < 0) {
            $minus = true;
            $seconds *= -1;
        }
        $hours = floor($seconds / 60 / 60);
        $left = $seconds - $hours * 60 * 60;
        $minutes = floor($left / 60);
        $left -= $minutes * 60;

        if ($decimal) {
            return $hours . '.' . ($minutes ? round($minutes * 10 / 6) : '00');
        }
        $parts = [];
        if ($hours > 0 || $seconds === 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 hour} other{# hours}}', ['delta' => $hours]);
        }
        if ($minutes > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 minute} other{# minutes}}', ['delta' => $minutes]);
        }
        if ($left > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 second} other{# seconds}}', ['delta' => $left]);
        }

        return ($minus ? '-' : '') . implode(', ', $parts);
    }

    /**
     * Returns number of working days in current month
     * @return int
     */
    public function currentMonthWorkingDays()
    {
        $allDays = (int)date('t');
        $offDays = Calendar::find()->where(['like', 'offday', date('Y-m-') . '%', false])->count();
        return $allDays - $offDays;
    }

    /**
     * Returns number of working seconds spent in current month so far.
     * @return int
     */
    public function currentMonthWorkingTime()
    {
        $allDays = (int)date('j');
        $offDays = Calendar::find()->where(['and',
            ['>=', 'offday', date('Y-m-01')],
            ['<=', 'offday', date('Y-m-d')]
        ])->count();
        return ($allDays - $offDays) * 8 * 3600;
    }

    /**
     * Returns number of working seconds spent in current month so far for given user.
     * @param int $user
     * @return int
     */
    public function currentMonthWorkingTimeOf($user)
    {
        $sum = Time::find()->where(['and',
            ['user_id' => $user],
            ['>=', 'created_at', Yii::$app->formatter->asTimestamp(date_create(date('Y-m-01 00:00:00'), timezone_open('Europe/Warsaw')))],
            ['<=', 'created_at', Yii::$app->formatter->asTimestamp(date_create(date('Y-m-d 23:59:59'), timezone_open('Europe/Warsaw')))],
        ])->sum('seconds');
        return $sum ?: 0;
    }

    /**
     * Returns number of working seconds spent in previous month so far.
     * @return int
     */
    public function previousMonthWorkingTime()
    {
        $allDays = (int)date('t', strtotime('last month'));
        $offDays = Calendar::find()->where(['and',
            ['>=', 'offday', date('Y-m-01', strtotime('last month'))],
            ['<=', 'offday', date('Y-m-t', strtotime('last month'))]
        ])->count();
        return ($allDays - $offDays) * 8 * 3600;
    }

    /**
     * Returns number of working seconds spent in previous month for given user.
     * @param int $user
     * @return int
     */
    public function previousMonthWorkingTimeOf($user)
    {
        $sum = Time::find()->where(['and',
            ['user_id' => $user],
            ['>=', 'created_at', Yii::$app->formatter->asTimestamp(date_create(date('Y-m-01 00:00:00', strtotime('last month')), timezone_open('Europe/Warsaw')))],
            ['<=', 'created_at', Yii::$app->formatter->asTimestamp(date_create(date('Y-m-t 23:59:59', strtotime('last month')), timezone_open('Europe/Warsaw')))],
        ])->sum('seconds');
        return $sum ?: 0;
    }
}
