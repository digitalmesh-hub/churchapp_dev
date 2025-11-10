<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Feedbacknotificationsent;

/**
 * This is the model class for table "feedbacknotificationsent".
 *
 * @property int $id
 * @property int $userid
 * @property int $feedbackid
 * @property string $createddatetime
 *
 * @property Feedback $feedback
 * @property Usercredentials $user
 */
class ExtendedFeedbacknotificationsent extends Feedbacknotificationsent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbacknotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'feedbackid', 'createddatetime'], 'required'],
            [['userid', 'feedbackid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['feedbackid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['feedbackid' => 'feedbackid']],
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
            'feedbackid' => 'Feedbackid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
   /**
    * To set feedback image notification
    * @param unknown $institutionId
    * @param unknown $manageFeedbacksPrivilegeId
    * @param unknown $feedbackId
    * @return boolean
    */
     public static function setFeedbackNotification($institutionId,$manageFeedbacksPrivilegeId,$feedbackId)
     {
     	try {
     		$response = ExtendedDevicedetails::getDeviceDetails($institutionId, $manageFeedbacksPrivilegeId);
     		if($response)
     		{
     			foreach ($response as $key => $value)
     			{
     				$userId = $value['id'];
     				$feedbackId = $feedbackId;
     				$createdDate = date('Y-m-d H:i:s');
     				$addNotification = ExtendedFeedbacknotificationsent::saveNotification($userId, $feedbackId, $createdDate);
     				if($addNotification)
     				{
     					return true;
     				}else{
     					return false;
     				}
     			}
     		}
     		
     	} catch (Exception $e) {
     	}
     }
     /**
      * To save feedback notification
      * @param unknown $userId
      * @param unknown $feedbackId
      * @param unknown $createdDate
      * @return boolean
      */
     public static function saveNotification($userId,$feedbackId,$createdDate)
     {
     	try {
     		$saveData = Yii::$app->db->createCommand('
    						INSERT INTO feedbacknotificationsent(feedbackid,userid,createddatetime)
    						VALUES(:feedbackid,:userid,:date)')
         						->bindValue(':feedbackid',$feedbackId)
         						->bindValue(':userid', $userId)
         						->bindValue(':date', $createdDate)
         						->execute();
         	return true;
     
     	} catch (Exception $e) {
     		return false;
     	}
     }
}
