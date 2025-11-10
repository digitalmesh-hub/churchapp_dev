<?php

namespace common\models\extendedmodels;
use common\models\basemodels\Prayerrequestnotificationsent;
use common\models\basemodels\Usercredentials;
use common\models\basemodels\Prayerrequest;

use Yii;

/**
 * This is the model class for table "prayerrequestnotificationsent".
 *
 * @property int $id
 * @property int $prayerrequestid
 * @property int $userid
 * @property string $createddatetime
 *
 * @property Prayerrequest $prayerrequest
 * @property Usercredentials $user
 */
class ExtendedPrayerrequestnotificationsent extends Prayerrequestnotificationsent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prayerrequestnotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prayerrequestid', 'userid', 'createddatetime'], 'required'],
            [['prayerrequestid', 'userid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['prayerrequestid'], 'exist', 'skipOnError' => true, 'targetClass' => Prayerrequest::className(), 'targetAttribute' => ['prayerrequestid' => 'prayerrequestid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }
    /**
     * To add prayer request
     * notification sent
     * @param unknown $institutionId
     * @param unknown $prayerrequestPrivilegeId
     * @param unknown $requestId
     * @return boolean
     */
    public static function setPrayerRequestNotificationSent($institutionId,$prayerrequestPrivilegeId,$requestId)
    {
    	try {
    		$response = ExtendedDevicedetails::getDeviceDetails($institutionId, $prayerrequestPrivilegeId);
    		if($response) {
    			foreach ($response as $key => $value) {
    				$userId = $value['id'];
    				$prayerRequestId = $requestId;
    				$createdDate = date('Y-m-d H:i:s');
    				$addNotification = ExtendedPrayerrequestnotificationsent::saveNotification($userId, $prayerRequestId, $createdDate);
    				if($addNotification) {
    					return true;
    				} else {
    					return false;
    				}
    			}
    		} else {
    			return false;
    		}
    	} catch (Exception $e) {
            yii::error($e->getMessage());
    		return false;
    	}
    }
    /**
     * ad prayer request
     * notification sent
     */
    public static function saveNotification($userId,$prayerRequestId,$createdDate)
    {
    	try {
    		$saveData = Yii::$app->db->createCommand('
    						INSERT INTO prayerrequestnotificationsent(prayerrequestid,userid,createddatetime)
    						VALUES(:prayerrequestid,:userid,:date)')
                           ->bindValue(':prayerrequestid',$prayerRequestId)
                           ->bindValue(':userid', $userId)
                           ->bindValue(':date', $createdDate)
                           ->execute();
    		return true;	
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
