<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Tempmember;
use common\models\extendedmodels\ExtendedTitle;

class ExtendedTempmember extends Tempmember
{
  
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				
				[['temp_active', 'temp_approved'], 'integer', 'max' => 4],
				
		];
	}
	
	public function getPendingMembers($institutionId){
		
		$sql = "select count(*)as pendingmemmbercount from tempmember where temp_approved=0 and temp_institutionid=:institutionId ; ";
		
		$result = Yii::$app->db->createCommand($sql)
		->bindValue(':institutionId' , $institutionId )
		
		->queryAll();
		return $result;
	}
	
	public function getDetails ($oldData){
		
		return 
			'<div class="approvalbox nodisplay">
		<div class="prevdatahead">Previous Data</div>
		<div class="prevdata" id="FirstName">'.$oldData.'</div>
		<div class="inlinerow text-center">
		<input class="approvebtn" value="Approve" type="button">
		<input class="rejectbtn" value="Reject" type="button">
		</div>
		</div>';
	}
	/**
	 * To get the details of location details
	 * @param string $lat
	 * @param string $long
	 */
	public function getLocationDetails($lat, $long)
	{
		return
		'<div class="approvalbox nodisplay">
		<div class="prevdatahead">Previous Data</div>
		<div class="prevdata" id="FirstName"> Latitude : ' . $lat . '</div>
		<div class="prevdata" id="FirstName"> Longitude : ' . $long . '</div>
		<div class="inlinerow text-center">
		<input class="approvebtn" value="Approve" type="button">
		<input class="rejectbtn" value="Reject" type="button">
		</div>
		</div>';
	}
	
	public function getPendingInfo($newData,$oldData)
	{
		$pendinginfo = '';
		if($newData != $oldData ){
			$pendinginfo = "pendinginfo";
		}
		return $pendinginfo;
	}

	/**
	* To get the details of pending location details
	* @param string $newData1
	* @param string $oldData1
	* @param string $newData2
	* @param string $oldData2
	*/
	public function getPendingLocationInfo($newData1,$oldData1,$newData2,$oldData2)
	{
		$pendinginfo = '';
		if(($newData1 != $oldData1 ) || ($newData2 != $oldData2)){
			$pendinginfo = "pendinginfo";
		}
		return $pendinginfo;
	}
	
	public function getTempDependantDetails($memberId){
		
		try {
		
			$memberTempDepents = Yii::$app->db->createCommand(
					"CALL get_temp_dependants(:memberId)")
					->bindValue(':memberId' , $memberId )
					->queryAll();
		
			return $memberTempDepents;
		
				
		} catch (Exception $e) {
			return false;
		}
		
	}
	public function getTempDependantMailDetails($memberId){
	
		try {
	
			$memberTempDepentsMail = Yii::$app->db->createCommand(
					"CALL get_temp_dependants_mail(:memberId)")
					->bindValue(':memberId' , $memberId )
					->queryAll();
	
					return $memberTempDepentsMail;
	
	
		} catch (Exception $e) {
			return false;
		}
	
	}
	/**
	 * To get the details of
	 * pending members
	 */
	public static function getPendingMembersData($userId)
	{
		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$pendingMembers = Yii::$app->db->createCommand("
					CALL getpendingmembers(:userid, :institutionid)")
					->bindValue(':userid', $userId)
					->bindValue(':institutionid', $institutionId)
					->queryAll(); 
			/* $pendingMembers = Yii::$app->db->createCommand("
								select * from tempmember where temp_approved=0 and temp_institutionid=:institutionid")
								->bindValue(':institutionid', $institutionid)
								->queryAll(); */
			return $pendingMembers;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Temp member approved
	 */
	public static function isTempMemberApproved($memberId)
	{
		try {
			 $isMemberApproved = Yii::$app->db->createCommand("
								select result.temp_approved from (select temp_approved,temp_memberid from tempmember where 
								temp_memberid=:memberid order by temp_approved asc) result group by result.temp_memberid ")
								->bindValue(':memberid', $memberId)
								->queryOne();
			return $isMemberApproved;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * get pending members
	 */
	public static function getPendingMembersForResponse($memberId)
	{
		try {
			
			$pendingMembers = Yii::$app->db->createCommand("
					CALL get_tempmember_serviceadmin(:memberid)")
					->bindValue(':memberid', $memberId)
					->queryOne();
			return $pendingMembers;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * 
	 */
	public static function saveTempMemberSpouseImageNew($memberId,$institutionId,$pendingImageURL,$pendingImageThumbnail,$type)
	{
		try {
			if($type == 'member')
			{
				
				$sql = 'INSERT INTO tempmember (temp_memberid,
                temp_institutionid,temp_member_pic,temp_memberImageThumbnail)
                VALUES(:memberid, :institutionid,
                :pendingImageURL,:thumbnail)';
			}elseif ($type == 'spouse')
			{
				$sql = 'INSERT INTO tempmember (temp_memberid,
                temp_institutionid,temp_spouse_pic,temp_spouseImageThumbnail)
                VALUES(:memberid, :institutionid,
                :pendingImageURL,:thumbnail)';
			}
			
		
			Yii::$app->db->createCommand($sql)
			->bindValue(':memberid', $memberId)
			->bindValue(':institutionid', $institutionId)
			->bindValue(':pendingImageURL', $pendingImageURL)
			->bindValue(':thumbnail', $pendingImageThumbnail)
			->execute();
			return true;
		} catch (ErrorException $e) {
			return false;
		}
	}
	/**
	 * To update member and
	 * spouse images
	 */
	public static function saveTempMemberSpouseImage($memberId,$institutionId,$pendingImageURL,$pendingImageThumbnail,$type)
	{
		
		try {
			if($type == 'member')
			{
				$sql = 'update tempmember set temp_member_pic=:pendingImageURL,temp_memberImageThumbnail=:thumbnail, temp_approved = 0 where temp_memberid=:memberid';
			
			}elseif ($type == 'spouse')
			{
				$sql = 'update tempmember set temp_spouse_pic=:pendingImageURL,temp_spouseImageThumbnail=:thumbnail, temp_approved = 0 where temp_memberid=:memberid';
			}
				
			Yii::$app->db->createCommand($sql)
			->bindValue(':memberid', $memberId)
			->bindValue(':pendingImageURL', $pendingImageURL)
			->bindValue(':thumbnail', $pendingImageThumbnail)
			->execute();
				
			return true;
		} catch (ErrorException $e) {
			Yii::error("Failed to save temporary image: " . $e->getMessage(), __METHOD__);
        	return false;
		} 
	}
	/**
	 * To approve member details
	 */
	public static function approveMemberDetails($categoryId,$subcategoryId,$data,$memberId)
	{
		try {
			$approveMemberDetails = Yii::$app->db->createCommand("
					CALL approvedetails(:categoryid,:subcategoryid,:data,:memberid)")
					->bindValue(':categoryid', $categoryId)
					->bindValue(':subcategoryid', $subcategoryId)
					->bindValue(':data', $data)
					->bindValue(':memberid', $memberId)
					->execute();
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * update temp member image
	 */
	public static function updateTempMemberImage($memberId,$image)
	{
		try {
			$sql = "update tempmember set temp_memberImageThumbnail=:memberImageThumbnail where temp_memberid=:memberid and temp_approved=0";
			$updateImage = Yii::$app->db->createCommand($sql)
							->bindValue(':memberImageThumbnail', $image)
							->bindValue(':memberid', $memberId)
							->execute();
			if($updateImage)
			{
				return true;
			}
			else{
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}	
	}
	/**
	 * update temp spouse image
	 */
	public static function updateTempSpouseImage($memberId,$spouseImage)
	{
		try {
			$sql = "update tempmember set temp_spouseImageThumbnail=:spouseImageThumbnail where temp_memberid=:memberid and temp_approved=0";
			$updateTempSpouseImage = Yii::$app->db->createCommand($sql)
							->bindValue(':spouseImageThumbnail', $spouseImage)
							->bindValue(':memberid', $memberId)
							->execute();
			if($updateTempSpouseImage)
			{
				return true;
			}
			else{
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To set the approved bit
	 */
	public static function setApprovedBit($memberId)
	{
		try {
			$updateBit = Yii::$app->db->createCommand("update tempmember set temp_approved=1 where temp_memberid=:memberid")
				->bindValue(':memberid', $memberId)
				->execute();
			if($updateBit)
			{
				return true;
			}
			else{
				return false;
				 
			}
	
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTempspousetitle0()
	{
	    return $this->hasOne(ExtendedTitle::className(), ['TitleId' => 'temp_spousetitle']);
	}
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTempmembertitle0()
	{
	    return $this->hasOne(ExtendedTitle::className(), ['TitleId' => 'temp_membertitle']);
	}
}
