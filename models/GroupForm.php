<?php

namespace app\models;

use Throwable;
use Yii;
use yii\db\Exception;

/**
 * Group Form
 *
 */
class GroupForm extends Group
{
    /**
     * @var array list of projects
     */
    public $projectsToAdd;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['projectsToAdd', 'name'], 'required'],
            ['name', 'string', 'min' => 1, 'max' => 255],
            ['projectsToAdd', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'projectsToAdd' => 'Projekty',
            'name' => 'Nazwa grupy',
        ];
    }

    /**
     * Adds group.
     * @return bool
     */
    public function add()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $group = new Group;
                $group->name = $this->name;
                if (!$group->save()) {
                    $this->addError('name', $group->getFirstError('name'));
                    throw new Exception('Blad zapisu grupy');
                }
                foreach ($this->projectsToAdd as $project) {
                    $projectModel = Project::findOne(['project_id' => $project]);
                    if (!$projectModel) {
                        $this->addError('projectsToAdd', 'Nie znaleziono projektu o ID ' . $project);
                        throw new Exception('Nie znaleziono projektu');
                    }
                    $group->link('projects', $projectModel);
                }
                $transaction->commit();
                return true;
            } catch (Throwable $exc) {
                $transaction->rollBack();
                Yii::error($exc->getMessage());
            }
        }
        return false;
    }

    /**
     * Edits group.
     * @return bool
     */
    public function edit()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new Exception('Blad zapisu grupy');
                }
                $previousProjects = GroupProject::find()->joinWith('project')->where(['group_id' => $this->id])->all();
                foreach ($previousProjects as $previousProject) {
                    if (!in_array($previousProject->project->project_id, $this->projectsToAdd, false)) {
                        $this->unlink('projects', $previousProject);
                    }
                }
                foreach ($this->projectsToAdd as $project) {
                    $projectModel = Project::findOne(['project_id' => $project]);
                    if (!$projectModel) {
                        $this->addError('projectsToAdd', 'Nie znaleziono projektu o ID ' . $project);
                        throw new Exception('Nie znaleziono projektu');
                    }
                    foreach ($previousProjects as $previousProject) {
                        if ($previousProject->project_id == $projectModel->id) {
                            continue 2;
                        }
                    }
                    $this->link('projects', $projectModel);
                }
                $transaction->commit();
                return true;
            } catch (Throwable $exc) {
                $transaction->rollBack();
                Yii::error($exc->getMessage());
            }
        }
        return false;
    }
}
