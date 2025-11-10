<?php
namespace common\components\rbacGroup;

use Yii;
use yii\base\Component;
use common\models\extendedmodels\ExtendedUserCredentials;


class UserGroupRule extends Component
{
    public function checkAdminGroupAccess($userId)
    {   
        $auth = Yii::$app->authManager;
        $loggedUserRole = $auth->getRolesByUser($userId);
        $child = null;
        if (!empty($loggedUserRole)) {
            foreach ($loggedUserRole as $key => $value) {
            $child = $value;
            }
        }
        $adminRole = $auth->getRole(ExtendedUserCredentials::ROLE_ADMIN);
        if ($adminRole && $child) { 
            return $auth->hasChild($adminRole, $child);  
        }
        return false;
    }
    public function checkAppAccessGroup($userId)
    {   
        $auth = Yii::$app->authManager;
        $loggedUserRole = $auth->getRolesByUser($userId);
        $child = null;
        if (!empty($loggedUserRole)) {
            foreach ($loggedUserRole as $key => $value) {
                $child = $value;
            }
        }
        $memberRole = $auth->getRole(ExtendedUserCredentials::ROLE_MEMBER);
        $staffRole = $auth->getRole(ExtendedUserCredentials::ROLE_STAFF);
        if ($memberRole && $staffRole && $child) { 
            return ($auth->hasChild($memberRole, $child) || $auth->hasChild($staffRole, $child)) ? true : false;  
        }
        return false;
    }
}
