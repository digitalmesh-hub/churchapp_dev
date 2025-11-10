<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\UserMember;
use Exception;

/**
 * This is the extended model class for table "usermember".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property int $institutionid
 * @property string $usertype
 *
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $user
 */
class ExtendedUserMember extends UserMember
{
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'],'string','max' => 100],
            [['userid', 'memberid', 'institutionid'], 'required'],
            [['userid', 'memberid', 'institutionid'], 'integer'],
            [['usertype'], 'string', 'max' => 1],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedMember::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUserCredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }
    
    public function beforeSave($insert)
    {
      if (parent::beforeSave($insert)) {
         if ($this->isNewRecord)
            $this->id = uniqid();
          return true;
      } else {
          return false;
      }
    }
    /**
     * to get the user type
     * @param $memberId int
     */
    public static function getUserType($memberId)
    {
    	try {
    		$userType = Yii::$app->db->createCommand('SELECT usertype FROM  usermember
				    				WHERE memberid=:memberid ')
    						    				->bindValue(':memberid',$memberId)
    						    				->queryOne();
    		return $userType;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * to get the user id
     * @param $memberId int
     */
    public static function getUserId($memberId,$userType)
    {
    	try {
    		$userId = Yii::$app->db->createCommand('SELECT userid,usertype FROM  usermember
				    				WHERE memberid=:memberid and usertype=:usertype')
    				->bindValue(':memberid',$memberId)
    				->bindValue(':usertype', $userType)
    		->queryOne();
    		return $userId;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * to get the institution id
     * @param $memberId int
     */
    public static function getInstitutionId($memberId)
    {
    	try {
    		$institutionId = Yii::$app->db->createCommand('SELECT institutionid FROM  usermember
				    				WHERE memberid=:memberid ')
    					    				->bindValue(':memberid',$memberId)
    					    				->queryOne();
    		return $institutionId;
    	
    	} catch (Exception $e) {
    		return false;
    	}
    }
  /**
     * to ckeck user member existing
     * @param unknown $userId
     * @param unknown $memberId
     * @param unknown $institutionId
     * @param unknown $userType
     * @return mixed
     */
    public function userMemberExist($userId,$memberId,$institutionId,$userType)
    {
    	
    	$sql = "select * from usermember where userid=:userId and memberid=:memberId and institutionid=institutionId and usertype=:userType ;";
    	 
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':userId' , $userId )
    	->bindValue(':memberId' , $memberId )
    	->bindValue(':userType' , $userType )
    	->queryOne();
    	return $result;
    	 
    }
    /**
     * to get the count of
     * pending members
     * @param $userId int
     */
    public static function getPendingMembersCount($userId)
    {

      $institutionId = Yii::$app->user->identity->institutionid;
    	try {
    		$pendingMembersCount= Yii::$app->db->createCommand(
    				"CALL getpendingmemberscount(:userid,:institutionid)")
    				->bindValue(':userid', $userId)
            ->bindValue(':institutionid',$institutionId)
    				->queryScalar();
    		return $pendingMembersCount;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get all the notifications
     * of the user
     */
    public static function getAllNotifications($userId,$institutionId,$currentDate)
    {
    	try {
    		$getAllData= Yii::$app->db->createCommand(
    				"CALL getallnotifications(:userid,:institutionid,:currentdate)")
    				->bindValue(':userid', $userId)
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':currentdate', $currentDate)
    				->queryAll();
    		return $getAllData;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the email id
     * of the member
     * @param $userId int
     * @param $userType string
     * @param $institutionId int
     */
    public static function getMemberEmail($institutionId,$userId,$userType)
    {
    	try {
    		$memberEmail = Yii::$app->db->createCommand(
    				"CALL getmemberemailforrsvp(:userid,:institutionid,:usertype)")
    				->bindValue(':userid', $userId)
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':usertype', $userType)
    				->queryOne();
    		return $memberEmail;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the member id 
     * of the user
     * @param $institutionId int
     * @param $userType string
     * @param $userId int
     */
    public static function getMemberId($userId,$institutionId,$userType)
    {
    	try {
    		$memberId = Yii::$app->db->createCommand("select memberid from usermember where 
    						userid=:userid and institutionid=:institutionid and usertype=:usertype")
    					->bindValue(':userid', $userId)
    					->bindValue(':institutionid', $institutionId)
    					->bindValue(':usertype', $userType)
    					->queryOne();
    		return $memberId;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     */
    public function getUserMember($userId)
    {
    	try {
    		
    		$userMember = Yii::$app->db->createCommand("select usermember.* from usermember where usermember.userid=:userid")
    				->bindValue(':userid', $userId)
    				->queryAll();
        if(!empty($userMember)){
    		  return $userMember;
        }
        else{
          return false;
        }
    	} 
      catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * Get userid
     * using memberid
     */
    public static function getUserIdMemberId($memberId, $institutionId, $userType)
    {
      
    	try {
    		$userId = Yii::$app->db->createCommand("select userid from usermember where memberid = :memberid and usertype = :usertype")
    					->bindValue(':memberid', $memberId)
    					->bindValue(':usertype', $userType)
    					->queryScalar();
    		if ($userId) {
    			   $institutionMemberId = Yii::$app->db->createCommand("select memberid from usermember where userid = :userid and institutionid = :institutionid and usertype=:usertype")
    										->bindValue(':userid', $userId)
    										->bindValue(':institutionid', $institutionId)
    										->bindValue(':usertype', $userType)
    										->queryScalar();
    			$institutionMemberId = ($institutionMemberId) ? $institutionMemberId : $memberId;
    		} else {
    			   $institutionMemberId = $memberId;
    		}
    		    return $institutionMemberId;
    	} catch (Exception $e) {
    		    return false;
    	}
    }
    /**
     * Get memberid
     */
    public static function getMemberIdByUserIdAndInstitutionId($userId,$institutionId)
    {
    	try {
    		$sql = 'select memberid,usertype from usermember where userid=:userid and institutionid=:institutionid';
    		
    		$memberId = Yii::$app->db->createCommand($sql)
    					->bindValue(':userid', $userId)
    					->bindValue(':institutionid', $institutionId)
    					->queryOne();
    		return $memberId;
    	} catch (Exception $e) {
    		return false;
    	}
    }

    /**
     * get member count
     */
    public static function getUserMemberCount($memberUserId)
    {
    	try {
    		$memberCount = Yii::$app->db->createCommand("select count(*) as count from usermember where userid = :userid")
    						->bindValue(':userid', $memberUserId)
    						->queryOne();
    		return $memberCount;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To delete user member
     */
    public static function deleteUserMember($memberId,$institutionId,$userType)
    {
    	try {
    		$deleteMember = Yii::$app->db->createCommand("delete from usermember where memberid = :memberid and institutionid = :institutionid and usertype = :usertype")
    						->bindValue(':memberid', $memberId)
    						->bindValue(':institutionid', $institutionId)
    						->bindValue(':usertype', $userType)
    						->execute();
    		return true;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To add user member
     */
    public static function addUserMember($userMember)
    {
    	try {
    		$addUser = Yii::$app->db->createCommand("INSERT INTO usermember(userid, memberid, institutionid,usertype) VALUES(:userid,:memberid,:institutionid,:usertype)")
    					->bindValue(':userid', $userMember->userid)
    					->bindValue(':memberid', $userMember->memberid)
    					->bindValue(':institutionid', $userMember->institutionid)
    					->bindValue(':usertype', $userMember->usertype)
    					->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     */
    public static function getUserMembersCount($memberId,$institutionId,$userType)
    {
    	try {
    		$memberCount = Yii::$app->db->createCommand("SELECT count(id) as usermembercount from usermember where memberid = :memberid
    						and institutionid = :institutionid and usertype = :usertype")
    						->bindValue(':memberid', $memberId)
    						->bindValue(':institutionid', $institutionId)
    						->bindValue(':usertype', $userType)
    						->queryOne();
    		return $memberCount;
						    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     */
    public static function getAnyInstitutionId($memberUserId,$institutionId)
    {
    	try {
    		$sql = Yii::$app->db->createCommand("select um.institutionid from usercredentials us inner join usermember
    											um on um.userid=us.id where us.id=:userid and um.institutionid!=:institutionid LIMIT 1")
    											
    				->bindValue(':userid', $memberUserId)
    				->bindValue(':institutionid', $institutionId)
    				->queryOne();
    		return $sql;
    	} catch (Exception $e) {
    		return false;
    	}
    }

    /**
     * To get the user id of the user
     * @param $userType string
     * @param $userId int
     * @return $memberId aaray
     */
    public static function getUserIdFromUserMember($memberId ,$userType)
    {
      try {
        $memberId = Yii::$app->db->createCommand("SELECT userid FROM usermember WHERE memberid =:memberid AND usertype =:usertype")
              ->bindValue(':memberid', $memberId)
              ->bindValue(':usertype', $userType)
              ->queryOne();
        return $memberId;
        
      } catch (ErrorException $e) {
        return false;
      }
    }
    public static function getUserMemberId($memberid, $institutionid, $usertype)
    {
       $sql = "SELECT id from usermember WHERE memberid =:memberid and usertype =:usertype and institutionid =:institutionid";
       try {
         return yii::$app->db->createCommand($sql)->bindValue(':memberid', $memberid)
                ->bindValue(':usertype', $usertype)->bindValue(':institutionid', $institutionid)->queryScalar();
       } catch (Exception $e) {
         yii::error($e->getMessage());
       }
       return false;
    }
    /**
     * To update user member
     */
    public static function updateUserMember($userId,$memberUserId,$institutionId)
    {
    	try {
    		
    		$sql = Yii::$app->db->createCommand("update usermember set userid = :userid where userid = :memberuserid and institutionid = :institutionid")
    		    						->bindValue(':userid', $userId)
    		    						->bindValue(':memberuserid', $memberUserId)
    		    						->bindValue(':institutionid', $institutionId)
    		    						->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }

     /**
     * Get userid
     * using memberid
     */
    public static function getUserIdForRsvp($memberId, $institutionId, $userType)
    {
      try {
        $userId = Yii::$app->db->createCommand("select userid from usermember where memberid = :memberid and usertype = :usertype and institutionid = :institutionid ")
              ->bindValue(':memberid', $memberId)
              ->bindValue(':usertype', $userType)
              ->bindValue(':institutionid', $institutionId)
              ->queryScalar();
        return $userId;
      } catch (Exception $e) {
        return false;
      }
    }
}

