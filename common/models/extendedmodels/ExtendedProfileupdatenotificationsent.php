<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Profileupdatenotificationsent;
use common\models\extendedmodels\ExtendedPrivilege;

/**
 * This is the model class for table "profileupdatenotificationsent".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property string $createddatetime
 *
 * @property Member $member
 * @property Usercredentials $user
 */
class ExtendedProfileupdatenotificationsent extends Profileupdatenotificationsent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profileupdatenotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'memberid', 'createddatetime'], 'required'],
            [['userid', 'memberid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'memberid' => 'Memberid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
    
    /**
     * To delete the profile notification sent details
     * @param unknown $memberId
     */
    public function deleteFromProfileNotificationSent($memberId){
    	 
    	$sql = " SET sql_safe_updates=0; DELETE FROM profileupdatenotificationsent WHERE memberid=:memberId";
    	 
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':memberId' , $memberId )
    	->execute();
    }
    /**
     * To add profile notification sent details
     */
    public static function sentNotification($institutionId,$memberId)
    {
    	try {
    		$deviceList = ExtendedDevicedetails::getDeviceList($institutionId,ExtendedPrivilege::APPROVE_PENDING_MEMBER);
    		if(!empty($deviceList))
    		{
    			foreach ($deviceList as $devices)
    			{
    				$profileNotificationModel = new ExtendedProfileupdatenotificationsent();
    				$profileNotificationModel->userid = $devices['id'];
    				$profileNotificationModel->memberid = $memberId;
    				$profileNotificationModel->createddatetime = gmdate("Y-m-d H:i:s");
    				$profileNotificationModel->save();
    			}
    		}
    		return true;
    	} catch (Exception $e) {
    	}
    }
}
