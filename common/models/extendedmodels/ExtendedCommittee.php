<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Committee;
use Exception;

/**
 * This is the model class for table "committee".
 *
 * @property int $committeeid
 * @property int $userid
 * @property int $designationid
 * @property int $datefrom
 * @property int $dateto
 * @property int $memberid
 * @property int $institutionid
 * @property int $isspouse
 * @property int $active
 * @property string $createddatetime
 * @property int $createdby
 * @property int $committeegroupid
 * @property int $committeeperiodid
 *
 * @property Institution $institution
 * @property Committeegroup $committeegroup
 * @property CommitteePeriod $committeeperiod
 * @property Usercredentials $createdby0
 * @property Designation $designation
 * @property Member $member
 * @property Usercredentials $user
 */
class ExtendedCommittee extends Committee
{
	public static function tableName()
	{
		return 'committee';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['userid', 'designationid', 'memberid', 'institutionid', 'isspouse', 'createddatetime', 'createdby'], 'required'],
				[['userid', 'designationid', 'datefrom', 'dateto', 'memberid', 'institutionid', 'createdby','committeeperiodid'], 'integer'],
				[['createddatetime'], 'safe'],
				[['isspouse', 'active'], 'string', 'max' => 4],
				[['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
				[['committeegroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Committeegroup::className(), 'targetAttribute' => ['committeegroupid' => 'committeegroupid'],'message' => 'Committee type can not be blank'],
				[['committeeperiodid'], 'exist', 'skipOnError' => true, 'targetClass' => CommitteePeriod::className(), 'targetAttribute' => ['committeeperiodid' => 'committee_period_id']],
				[['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
				[['designationid'], 'exist', 'skipOnError' => true, 'targetClass' => Designation::className(), 'targetAttribute' => ['designationid' => 'designationid']],
				[['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
				[['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
		];
	}

	/**
	 * To get the user available 
	 * in a committee
	 * @param $userId int
	 * @return $checkUserAvailability array
	 */
	public static function checkUserAvailableInCommittee($userId)
	{
		try {
			$checkUserAvailability = Yii::$app->db->createCommand('SELECT committeeid FROM committee 
				    				WHERE userid=:userid and active = 1 ')
    		->bindValue(':userid',$userId)
    		->queryAll();
    		return $checkUserAvailability;
		} catch (Exception $e) {
			return false;
		}
		
	}

	/**
	 * To get the committee
	 * institutions of user
	 * @param $userId int
	 * @return $commmitteeInstitution array
	 */
	public static function getAllCommitteeInstitutionOfUser($userId)
	{
		try {
			$committeeInstitution = Yii::$app->db->createCommand('select institutionid from committee where userid = :userid
									and active = 1 group by institutionid')
									->bindValue(':userid', $userId)
									->queryColumn();
			return $committeeInstitution;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * To get all 
	 * committee members for synchronize
	 */
	public static function getCommitteeMembers($institutionId,$currentDate)
	{
		try {
			$committeMembers = Yii::$app->db->createCommand(
    				"CALL getcommitteememberforsync(:institutionid,:currentdate)")
    				->bindValue(':institutionid' , $institutionId )
    				->bindValue(':currentdate' , $currentDate )
    				->queryAll();
			return $committeMembers;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Check member available in committee
	 * @param $committeePeriodId int
	 * @param $designationId int
	 * @param $memberId int
	 * @param $committeeGroupid int
	 */
	public static function checkMemberAvailbaleInCommittee($committeePeriodId, $designationId, $memberId, $committeeGroupId)
	{	

		$sql = "select count(committeeid) as count from committee where committeeperiodid=:committeeperiodid and designationid = :designationid and memberid = :memberid  and committeegroupid=:committeegroupid and active = 1";
		try {
			$committeeCount = Yii::$app->db->createCommand($sql)
				->bindValue(':committeeperiodid', $committeePeriodId)
				->bindValue(':designationid', $designationId)
				->bindValue(':memberid', $memberId)
				->bindValue(':committeegroupid', $committeeGroupId)
				->queryScalar();

			return $committeeCount;
		} catch (ErrorException $e) {
			return 0;
		}
	}

	/**
	 * Check member available in committee
	 * @param $userId int
	 * @param $designationId int
	 * @param $memberId int
	 * @param $institutionId int
	 * @param $isSpouse int
	 * @param $createdDatetime dateTime
	 * @param $createdBy int
	 * @param $committeeGroupid int
	 * @param $committeePeriodId int
	
	 */
	public static function saveCommitteeMember($userId, 
        $designationId, $memberId, $institutionId,
        $isSpouse, $createdDatetime, 
        $createdBy, $committeGroupId, 
        $committePeriodId)
	{
		try {
			$sql = 'INSERT INTO committee(userid,designationid,'.
				'memberid,institutionid,isspouse,'. 
				'active,createddatetime,createdby,'.
				'committeegroupid,committeeperiodid)'. 
				'VALUES(:userid,:designationId,'. 
				':memberid,:institutionid,:isspouse,:active,'.
				':createddatetime,:createdby,'.
				':committegroupid,'.
				':committeperiodid)';
				
			Yii::$app->db->createCommand($sql)
				->bindValue(':userid', $userId)
				->bindValue(':designationId', $designationId)
				->bindValue(':memberid', $memberId)
				->bindValue(':institutionid', $institutionId)
				->bindValue(':isspouse', $isSpouse)
				->bindValue(':active', 1)
				->bindValue(':createddatetime', $createdDatetime)
				->bindValue(':createdby', $createdBy)
				->bindValue(':committegroupid', $committeGroupId)
				->bindValue(':committeperiodid', $committePeriodId)
				->execute();

			return true;;
		} catch (ErrorException $e) {
			return false;
		}
	}

	/**
	 * To get all committee member under institution
	 * @param $institutionId int
	 * @return $commmitteeInstitution array
	 */
	public static function getCommitteeMemberUnderInstitution($institutionId)
	{
		try {
			$committeMembers = Yii::$app->db->createCommand(
    				"CALL get_committee_under_instituion(:institutionid,:currentdate)")
    				->bindValue(':institutionid', $institutionId )
    				->bindValue(':currentdate', gmdate("Y-m-d H:i:s"))
    				->queryAll();
			return $committeMembers;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Check user available in current committee
	 * @param $userId int
	 * @return $commmitteeInstitution array
	 */
	public static function checkUserAvailableInCurrentCommittee($userId, $institutionId)
	{
		try {
			$committeeInstitution = Yii::$app->db->createCommand('SELECT committeeid FROM committee WHERE userid = :userid AND institutionid = :institutionid
				AND active = 1')
				->bindValue(':userid', $userId)
				->bindValue(':institutionid', $institutionId)
				->queryOne();
			return $committeeInstitution;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get committee designations
	 * @param unknown $memberId
	 * @param unknown $userType
	 * @param string $isService
	 * @return boolean
	 */
	public static function getCommitteeDesignations($memberId,$userType,$isService=false)
	{
		try {
			$isspouse = ($userType == 'S') ? true : false;
			$committeeDesignation = Yii::$app->db->createCommand("
					CALL get_committee_designations(:memberid,:usertype)")
					->bindValue(':memberid', $memberId)
					->bindValue(':usertype', $isspouse)
					->queryAll();
			if($isService){
				if(!empty($committeeDesignation)){
					return $committeeDesignation;
				} else {
					return true;
				}
			} else {
				return $committeeDesignation;
			}	
		} catch (Exception $e) {
			yii::error($e->getMessage());
			return false;
		}
	}
	/**
	 * To update the committee userid
	 * @param unknown $userId
	 * @param unknown $memberUserId
	 * @param unknown $institutionID
	 * @return boolean
	 */
	
	public static function updateCommitteeUserid($userId, $memberUserId, $institutionID)
	{
		
		$sql = "update committee set userid = :userid where userid = :memberuserid and institutionid = :institutionid";
		try{
		$committeeUpdate = Yii::$app->db->createCommand($sql)
							->bindValue(':userid', $userid)
							->bindValue(':memberuserid', $memberUserId)
							->bindValue(':institutionid', $institutionID)
							->execute();
		}catch (Exception $e) {
			return false;
		}
	}
}
