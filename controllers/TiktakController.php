<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class TiktakController extends Controller
{
    use FlashTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns week type.
     * Cached for 1 hour.
     * @return string
     */
    public function getWeek()
    {
        $week = Yii::$app->cache->get('week');
        if ($week === false) {
            $week = '?';
            $provider = file_get_contents('http://jakitydzien.pl/');
            if (($start = strpos($provider, '<h1>')) !== false) {
                if (($end = strpos($provider, '</h1>')) !== false) {
                    $week = trim(substr($provider, $start + 4, $end - $start - 4));
                }
            }
            Yii::$app->cache->set('week', $week, 3600);
        }
        return $week;
    }

    /**
     * Displays hours.
     * @return string
     */
    public function actionIndex()
    {



        return $this->render('index', [
            'week' => $this->getWeek()
        ]);
    }
}
