<?php

/**
 * @license MIT License
 */

namespace daxslab\yii2casclient\cas\controllers;

use Yii;
use yii\helpers\Url;

/**
 * A controller inside the Module that will handle the HTTP query of the CAS server.
 *
 * Provides 2 actions, usually /cas/login and /cas/logout,
 * where "cas" is the key in the configuration file of the Yii2 application
 * `"modules" => ['cas' => ...]`.
 *
 * @author François Gannaz <francois.gannaz@poofe.info>
 */
class AuthController extends \yii\web\Controller
{
    public function actionLogin()
    {
        try {
            $user = $this->module->casService->forceAuthentication();
            if ($user) {
                Yii::$app->user->login($user);
            } else {
                throw new \yii\web\HttpException(403, "This user has no access to the application.");
            }
        } catch (\Exception $exception) {
            \Yii::debug($exception->getMessage());
            $this->redirect(Url::to(['auth/login']));
        }
        return $this->goBack();
    }

    public function actionLogout()
    {
        //CAS注销通知
        if (strpos($_SERVER['QUERY_STRING'], 'ticket=') !== false) {
            $ticket = substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], 'ticket=') + 7);
            $session_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $ticket);
            $session_file = \Yii::$app->session->getSavePath() . '/sess_' . $session_id;
            @unlink($session_file);
            return true;
        }
        try {
            //当前站点注销
            $this->module->casService->logout(Url::home(true));
            if (!Yii::$app->getUser()->isGuest) {
                Yii::$app->getUser()->logout(true);
            }
            // In case the logout fails (not authenticated)
        } catch (\Exception $exception) {

        }
        \phpCAS::handleLogoutRequests(false);
        return $this->redirect(Url::home(true));
    }
}
