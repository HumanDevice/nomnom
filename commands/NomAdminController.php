<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;

/**
 * NomAdminController
 */
class NomAdminController extends Controller
{
    /**
     * Adds new admin. Params: name
     * @param string $name admin name
     */
    public function actionIndex($name)
    {
        $old = User::findOne(['username' => mb_strtolower($name, 'UTF-8')]);
        if ($old) {
            $this->stdout('Uzytkownik o tym imieniu jest juz dodany.' . "\n");
            return Controller::EXIT_CODE_ERROR;
        }
        $user = new User;
        $user->username = $name;
        $user->role = User::ROLE_ADMIN;
        $user->generateAuthKey();
        if ($user->save()) {
            $this->stdout('Admin dodany.' . "\n");
            return Controller::EXIT_CODE_NORMAL;
        }
        $this->stdout('Admin nie zostal dodany!!!' . "\n");
        return Controller::EXIT_CODE_ERROR;
    }
}
