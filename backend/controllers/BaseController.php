<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\extendedmodels\ExtendedUserCredentials;
use yii\helpers\Url;
use yii\web\Request;
use yii\web\Response;

class BaseController extends Controller
{

    public function beforeAction($action)
    {
        $this->userBasedLayout();
        $this->request = Yii::$app->request;
        if (isset(Yii::$app->user->identity->institution->id)) {
            $institution = (new \yii\db\Query())
                ->select(['active'])
                ->from('institution')
                ->where(['id' => Yii::$app->user->identity->institution->id])
                ->scalar();

            if ($institution === null || $institution == 0) {
                $message = 'Your institution is inactive. Please contact the administrator.'; // Store message before logout

                Yii::$app->user->logout(); // Logout clears session
                Yii::$app->session->open(); // Reopen session after logout
                Yii::$app->session->set('institutionInactive', $message);
                Yii::$app->response->redirect(Yii::$app->urlManager->createAbsoluteUrl(['account/login']))->send();
                Yii::$app->end();
            }
        }
        return parent::beforeAction($action);
    }

    protected function userBasedLayout()
    {
        $foundRole = false;
        if (Yii::$app->user->can('superadmin')) {
            $this->layout = '@backend/views/layouts/superAdminMain';
            $foundRole = true;
        }
        if (!$foundRole) {
            if (Yii::$app->checkAdminGroup->checkAdminGroupAccess($this->currentUserId())) {
                $this->layout = '@backend/views/layouts/AdminMain';
                $foundRole = true;
            }
        }
        if (!$foundRole) {
            $this->layout = '@backend/views/layouts/login';
        }

    }
    public function currentUserId()
    {
        if (isset(Yii::$app->user)) {
            return Yii::$app->user->id;
        }
        return null;
    }

    public function currentUser()
    {
        if (isset(Yii::$app->user)) {
            return Yii::$app->user->identity;
        }
        return null;
    }

    public function currentUserCan($permission)
    {
        if (isset(Yii::$app->user)) {
            return Yii::$app->user->can($permission);
        }
        return false;
    }
    public function sessionAddFlashArray($key, $value, $removeAfterAccess = true)
    {
        if (is_array($value))
        {
            foreach ($value as $item) {
                Yii::$app->session->addFlash($key,$item,$removeAfterAccess);
            }
        }
        else
            Yii::$app->session->addFlash($key,$value,$removeAfterAccess);
    }
}
