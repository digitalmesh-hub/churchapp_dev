<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\LocalAuthenticationRegisteredUser;


/**
 * This is the model class for table "local_authentication_registered_users".
 *
 * @property int $id
 * @property int $userid
 * @property string $deviceidentifier
 * @property string $createdon
 * 
 */


class ExtendedLocalAuthenticationRegisteredUser extends LocalAuthenticationRegisteredUser {
    
    public function getLocalAuthRegisteredUserBy($userid, $deviceidentifier) {

        $registeredUser = [];

        try {
            $registeredUser = Yii::$app->db->createCommand(
                'CALL GetLocalAuthenticationRegisteredUsersBy(
                    :userid, 
                    :deviceidentifier)')
            ->bindValue(':userid', $userid)
            ->bindValue(':deviceidentifier', $deviceidentifier)
            ->queryOne();

        } catch(Exception $e) {}
        
        return $registeredUser;
    }


    public function registerUserForLocalAuthentication($userid, $deviceidentifier) {

        if (self::hasDeviceOfUserRegisteredForLocalAuthentication($userid, $deviceidentifier)) {
            return true;
        }

        $registeredUser = false;

        try {
            $registeredUser = Yii::$app->db->createCommand(
                'CALL RegisterUserForLocalAuthentication(
                    :userid, 
                    :deviceidentifier)')
            ->bindValue(':userid' , $userid)
            ->bindValue(':deviceidentifier', $deviceidentifier)
            ->execute();

        } catch(Exception $e) {}

        if ($registeredUser) {
            return true;
        }

        return false;
    }


    public function deregisterUserFromLocalAuthentication($userid, $deviceidentifier) {
        
        $registeredUser = [];

        try {

            Yii::$app->db->createCommand(
                'CALL DeregisterUserForLocalAuthentication(
                    :userid, 
                    :deviceidentifier)')
            ->bindValue(':userid' , $userid)
            ->bindValue(':deviceidentifier', $deviceidentifier)
            ->execute();

        }  catch(Exception $e) {}

        return true;
    }


    public function hasDeviceOfUserRegisteredForLocalAuthentication($userId, $deviceidentifier) {

        $registeredUser = self::getLocalAuthRegisteredUserBy($userId, $deviceidentifier);
        if ($registeredUser) {
            return true;
        }

        return false;
    }


    public function logLastLoginWithLocalAuthentication($userId, $deviceIdentifier) {
        try {
            Yii::$app->db->createCommand(
                'CALL UpdateLastLoginWithLocalAuthentication(
                    :userid, 
                    :deviceidentifier)')
            ->bindValue(':userid' , $userId)
            ->bindValue(':deviceidentifier', $deviceIdentifier)
            ->execute();

        }  catch(Exception $e) {}
    }
}