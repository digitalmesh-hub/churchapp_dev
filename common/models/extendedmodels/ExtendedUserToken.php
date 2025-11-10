<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\UserToken;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the extended model class for table "usertoken".
 *
 * @property string $tokenid
 * @property int $userid
 * @property string $createddatetime
 * @property string $useridentity
 * @property string $lastactivedatetime
 *
 * @property Usercredentials $user
 */
class ExtendedUserToken extends UserToken
{ 

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createddatetime',
                'updatedAtAttribute' => 'lastactivedatetime',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    /**
     * To get user token count
     */
    public static function getUserTokenCount($memberUserId)
    {
    	try {
    		$deviceTokenCount = Yii::$app->db->createCommand("select count(tokenid) as usertokencount from usertoken where userid =:memberuserid")
					    		->bindValue(':memberuserid', $memberUserId)
					    		->queryOne();
    		return $deviceTokenCount;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To delete user token
     */
    public static function deleteUserToken($memberUserId)
    {
    	try{
    		$command = Yii::$app->db->createCommand("delete from usertoken where userid = :memberuserid")
    					->bindValue(':memberuserid', $memberUserId);
    		$command->execute();
    	}
    	catch(ErrorException $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    }
}
