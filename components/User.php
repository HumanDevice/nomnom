<?php

namespace app\components;

use Yii;
use yii\web\User as YiiUser;

/**
 * User component
 *
 */
class User extends YiiUser
{
    /**
     * Checks if user needs to be redirected to preferences.
     * @return mixed
     */
//    public function init()
//    {
//        parent::init();
//        if (!$this->isGuest) {
//            if ($this->identity->new_restaurants) {
//                if (!(Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'preferences')) {
//                    return Yii::$app->response->redirect(['site/preferences']);
//                }
//            }
//        }
//    }
    
    /**
     * Checks if user is admin.
     * @return bool
     */
    public function getIsAdmin()
    {
        $identity = $this->getIdentity();
        return $identity !== null ? $identity->getIsAdmin() : null;
    }
}