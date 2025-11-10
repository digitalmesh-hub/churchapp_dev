<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Devicedetails;

/**
 * This is the model class for table "devicedetails".
 *
 * @property int $id
 * @property string $deviceid
 * @property string $deviceidentifier
 * @property int $userid
 * @property string $registeredon
 * @property int $active
 * @property string $usertype
 * @property int $institutionid
 * @property string $devicetype
 * @property string $appversion
 *
 * @property Usercredentials $user
 * @property Institution $institution
 * @property Eventseendetails[] $eventseendetails
 * @property Notificationlog[] $notificationlogs
 */

class ExtendedDevicedetails extends Devicedetails
{
	/**
	 * To get device details based on privilege
	 * @param unknown $institutionId
	 * @param unknown $prayerrequestPrivilegeId
	 * @return mixed
	 */
	public static function getDeviceDetails($institutionId,$prayerrequestPrivilegeId)
	{
		try {
			 $deviceList =  Yii::$app->db->createCommand(
					"CALL get_managers_deviceid(:instittionid,:privilegeid)")
					->bindValue(':instittionid' , $institutionId )
					->bindValue(':privilegeid' , $prayerrequestPrivilegeId )
					->queryAll();
			return $deviceList;
			
		} catch (\Exception $e) {
			return false;
		}
	}
	/**
	 * To get device details 
	 * of members
	 */
	public static function getMemberDeviceDetails($orderid)
	{
	    try {
	        $deviceList =  Yii::$app->db->createCommand(
	            "CALL get_member_devicedetails(:orderid)")
	            ->bindValue(':orderid' , $orderid )
	        	->queryAll();
	        return $deviceList;
	        	
	    } catch (\Exception $e) {
	        return false;
	    }
	}

	/**
     * Get member details
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationLog()
    {
        return $this->hasMany(ExtendedNotificationlog::className(), ['deviceid' => 'id']);
    }
	/**
	 * To get device details based on userId
	 * @param unknown $userId
	 * @return boolean
	 */
	public static function getUserDevices($userId)
	{
		try {
			$deviceList =  Yii::$app->db->createCommand(
					"SELECT userid,deviceid,devicetype,appversion FROM devicedetails WHERE userid= :userid and active = 1 GROUP BY userid")
					->bindValue(':userid', $userId)
					->queryAll();
			return $deviceList;
			
		} catch (\Exception $e) {
			return false;
		}
	}
	/**
	 * To get device details count
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return \yii\db\false|boolean
	 */
	public static function getUserDeviceDetailsCount($memberUserId, $institutionId)
	{
		
		try {
			$deviceCount = Yii::$app->db->createCommand("select count(id) as devicedetailscount from devicedetails where userid =:memberuserid and institutionid=:institutionid")
			->bindValue(':memberuserid', $memberUserId)
			->bindValue(':institutionid', $institutionId)
			->queryOne();
			return $deviceCount;
		} catch (\Exception $e) {
			return false;
		}
	}
	/**
	 * To delete device details
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function deleteDeviceDetailsUsingUserid($memberUserId, $institutionId)
	{
		try{
			$command = Yii::$app->db->createCommand("delete from devicedetails where userid=:userid and institutionid=:institutionid")
			->bindValue(':userid', $memberUserId)
			->bindValue(':institutionid', $institutionId);
			$command->execute();
		
		}
		catch(\ErrorException $e){
			yii::error($e->getMessage());
			return false;
		}
	}
	/**
	 * To get device details
	 */
	public static function getDeviceList($institutionId,$privilegeId)
	{
		try {
			$devices = Yii::$app->db->createCommand("CALL get_managers_deviceid(:institutionid,:privilegeid)")
						->bindValue(':institutionid', $institutionId)
						->bindValue(':privilegeid', $privilegeId)
						->queryAll();
			return $devices;
			
		} catch (\Exception $e) {
			return false;
		}
	}
	

	/**
	 * Get the details of the user devices
	 */
	public static function getUserDevicesForExpiredNotificationRemoval() {

		$devices = [];

		try {
			$devices = Yii::$app->db->createCommand("CALL GetUsersForExpiredNotificationRemoval()")->queryAll();
		} 
		catch (\Exception $e) {}

		return $devices;

	}
}
