<?php
namespace common\models\basemodels;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedConversation;
use common\models\extendedmodels\ExtendedUserConversation;
use common\models\extendedmodels\ExtendedUserConversationTopic;
use common\models\extendedmodels\ExtendedSuccessfulleventsent;
use common\models\extendedmodels\ExtendedEventsentdetails;
use common\models\extendedmodels\ExtendedEventseendetails;
use common\models\extendedmodels\ExtendedDevicedetails;
use common\models\extendedmodels\ExtendedEditmember;
use common\models\extendedmodels\ExtendedTempmember;
use common\models\extendedmodels\ExtendedTempmembermail;
use common\models\extendedmodels\ExtendedTempdependantmail;
use common\models\extendedmodels\ExtendedTempdependant;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfo;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfomail;
use common\models\basemodels\RememberAppConstModel;
use common\models\extendedmodels\ExtendedUserToken;
use common\models\extendedmodels\ExtendedUserLocation;
use common\models\extendedmodels\ExtendedRsvpdetails;
use common\models\extendedmodels\ExtendedOrderitems;
use common\models\extendedmodels\ExtendedOrdernotificationsent;
use common\models\extendedmodels\ExtendedOrdernotifications;
use common\models\extendedmodels\ExtendedOrders;
use common\models\extendedmodels\ExtendedMember;
use Exception;

class BaseModel extends Model
{

    public static function getBatches($reg=false)
    {
        $allbatches = [];
        $year = 1959;
        if(!$reg) {
            $allbatches['All'] = 'All Batches';
        }
      
        do{
            ++$year;
            $allbatches[$year] = $year;
        } while($year < date('Y'));
        
        return $allbatches;
    }

    public static function createMultiple($modelClass,$multipleModels=null)
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];
        $flag     = false;

        if ($multipleModels !== null && is_array($multipleModels) && !empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
            $flag = true;
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if ($flag) {
                    if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                        $models[] = $multipleModels[$item['id']];
                    } else {
                        $models[] = new $modelClass;
                    }
                } else {
                    $models[] = new $modelClass;
                }
            }
        }
        unset($model, $formName, $post);
        return $models;
    }

    public static function getUserInstitution($userId)
    {
        $response = new \stdClass();
        $dataArr = [];
        try {
            $DashboardList = Yii::$app->db->createCommand("CALL getuserinstitutions(:userId)")
                ->bindValue(':userId', $userId)
                ->queryAll();
            
            if (count($DashboardList) > 0) {
                foreach ($DashboardList as $key => $value) {
                    $dataArr[$key] = $value;
                    $dataArr[$key]['DashboardList'] = self::getallFeatures($value['id']);
                }
            }
            $response->value = $dataArr;
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Institution Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        
        return $response;
    }

    public static function getallFeatures($institutionid)
    {
        try {
            return Yii::$app->db->createCommand("CALL getinstitutionfeatures(:institutionid)")
                ->bindValue(':institutionid', $institutionid)
                ->queryAll();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    public static function updateUserInstitutionandType($institutionId, $userId, $usertype)
    {
        $response = new \stdClass();
        $params = [
            ':institutionid' => $institutionId,
            ':userid' => $userId,
            ':usertype' => $usertype
        ];
        try {
            Yii::$app->db->createCommand("CALL updateuserinstitutionandtype(:userid,:institutionid, :usertype)")
                ->bindValues($params)
                ->execute();
            $response->Status = true;
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            $response->ErrorMessage = "UpdateUserInstitutionandType failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
        }
        return $response;
    }

    public static function getFamilyUnits($institutionid)
    {
        $response = new \stdClass();
        $sql = 'SELECT * FROM familyunit WHERE institutionid = :institutionid AND active = 1 order by description asc';
        try {
            $data = Yii::$app->db->createCommand($sql)
                ->bindValue(':institutionid', $institutionid)
                ->queryAll();
            $response->value = $data;
            $response->Status = true;
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            $response->ErrorMessage = "Get titles Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
        }
        
        return $response;
    }

    public static function getAllCommitteePeriodsAndTypes($institutionid)
    {
        $response = new \stdClass();
        
        try {
            $response->value = new \stdClass();
            $response->value->committeeGroupList = self::getAllCommitteeGroupWithPeriods($institutionid);
            $response->value->committeePeriodList = self::getAllCommitteePeriodsAndGroups($institutionid);
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Get committee period Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        
        return $response;
    }

    public static function getAllCommitteeGroupWithPeriods($institutionid)
    {
        try {
            return Yii::$app->db->createCommand("CALL get_all_committeegroup_with_periods(:institutionid)")
                ->bindValue(':institutionid', $institutionid)
                ->queryAll();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    public static function getAllCommitteePeriodsAndGroups($institutionid)
    {
        try {
            return Yii::$app->db->createCommand("CALL getallactive_committee_period_group(:institutionid)")
                ->bindValue(':institutionid', $institutionid)
                ->queryAll();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    public static function getAddressType()
    {
        $response = new \stdClass();
        $sql = "SELECT addresstype.* FROM addresstype";
        try {
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            $response->value = $data;
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "AddressType Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        
        return $response;
    }

    public static function getAllAppUserPrivileges($memberId, $institutionId, $userType)
    {   

        $response = new \stdClass();
        try {
            $userMemberId = ExtendedUserMember::getUserMemberId($memberId, $institutionId, $userType);
            $auth = yii::$app->authManager;
            $response->value = $auth->getPermissionsByUser($userMemberId);
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Error while selecting app privilages";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function getFeedbackTypesByUnderInstitution($institutionid, $isActive)
    {
        $response = new \stdClass();
        try {
            
            $institutionFeedbackType = Yii::$app->db->createCommand("CALL institution_feedbacktype_get(:institutionid,:isActive)")
                ->bindValue(':institutionid', $institutionid)
                ->bindValue(':isActive', $isActive)
                ->queryAll();
            
            $response->value = $institutionFeedbackType;
            $sql = "SELECT feedbackemail from institution where institution.id = :id";
            $institutionFeedbackEmail = Yii::$app->db->createCommand($sql)
                ->bindValue(':id', $institutionid)
                ->queryOne();
            
            $response->institution = new \stdClass();
            $response->institution->feedbackEmail = $institutionFeedbackEmail;
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Feedback type under Institution Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function getInstitutionFeatureEnabledValue($institutionid)
    {
        $response = new \stdClass();
        $sql = "SELECT feedbackenabled,paymentoptionenabled,prayerrequestenabled,moreenabled,advancedsearchenabled,tagcloud FROM  institution WHERE institution.id = :id";
        try {
            
            $response->value = Yii::$app->db->createCommand($sql)
                ->bindValue(':id', $institutionid)
                ->queryOne();
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Error while selecting institution enabled feature";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function checkUserAvailableInCommittee($userId, $institutionId)
    {
        $response = false;
        $sql = "select count(committeeid) from committee where userid = :userid and active = 1";
        try {
            $response = Yii::$app->db->createCommand($sql)
                ->bindValue(':userid', $userId)
                ->queryScalar();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function getNotificationsLoginCount($userId, $timezoneName = null )
    {
        $response = new \stdClass();
        $params = [
            ':userId' => $userId,
            ':timezoneName' => ($timezoneName) ? self::convertToUserTimezone(gmdate('Y-m-d H:i:s'),$timezoneName) : self::convertToUserTimezone(gmdate('Y-m-d H:i:s')),
        ];
        try {
            $response->value = Yii::$app->db->createCommand("CALL geteventscount(:userId,:timezoneName)")
                ->bindValues($params)
                ->queryOne();
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Notification count Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function getUnreadConversationCount($userId)
    {
        $response = new \stdClass();
        try {
            $institutionId = [];
            $institutionId = self::getCommitteInstitutionForUser($userId);
            $institutions = "";
            if (! empty($institutionId) && count($institutionId) > 0) {
                $institutions = implode(",", $institutionId);
            }
            if ($institutions != "") {
                $response->value = self::getUnreadCount($userId, $institutions);
            }
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "GetUnreadConversationCount Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
        }
        return $response;
    }

    public static function getBillSeenCount($memberId, $userType, $institutionid)
    {
        $response = new \stdClass();
        $params = [
            ':institutionid' => $institutionid,
            ':memberId' => $memberId,
            ':userType' => $userType
        ];
        try {
            $response->value = Yii::$app->db->createCommand("CALL  getbillsseencount(:institutionid, :memberId, :userType)")
                ->bindValues($params)
                ->queryOne();
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Bills seen count Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
        return $response;
    }

    public static function getCommitteInstitutionForUser($userId)
    {
        $sql = "SELECT institutionid from committee where userid = :userid and active = 1 group by institutionid";
        try {
            return yii::$app->db->createCommand($sql)
                ->bindValue(':userid', $userId)
                ->queryColumn();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getUnreadCount($userId, $institutions)
    {
        $params = [
            ':userId' => $userId,
            'institutions' => $institutions
        ];
        try {
            return Yii::$app->db->createCommand("CALL get_total_conversation_count(:userId, :institutions)")
                ->bindValues($params)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getManagementCounts($userId, $institutionId)
    {
        $response = new \stdClass();
        $response->value = new \stdClass();
        try {
            $response->value->PrayerRequestCount = self::getAllPrayerRequestCount($userId, $institutionId);
            $response->value->RSVPCount = self::getRsvpEventsCount($userId, $institutionId);
            $response->value->ProfileApprovalCount = self::getMembersListForApprovalCount($userId, $institutionId);
            $response->value->FeedbackCount = self::getallFeedbackCount($userId, $institutionId);
            $response->value->FoodOrdrCount = self::getOrdersCount($userId, ExtendedPrivilege::MANAGE_FOOD_ORDERS, RememberAppConstModel::RESTAURANT,$institutionId);
            $response->value->PendingAlbumCount = self::getAllPendingAlbumsCount($userId, $institutionId);
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Bills seen count Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
        return $response;
    }

    public static function getAllPrayerRequestCount($userId,$institutionId)
    {   
        $params = [
            ':currentDate' => gmdate("Y-m-d H:i:s"),
            ':userId' => $userId,
            ':institutionid' => $institutionId
        ];
        try {
            return Yii::$app->db->createCommand("CALL getallprayerrequestcount(:currentDate,:userId,:institutionid)")
                ->bindValues($params)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getRsvpEventsCount($userId, $institutionId)
    {
        $params = [
            ':userId' => $userId,
            ':currentDate' => gmdate("Y-m-d H:i:s"),
            ':institutionid' => $institutionId
        ];
        try {
            return Yii::$app->db->createCommand("CALL geteventswithrsvpcount(:userId, :currentDate, :institutionid)")
                ->bindValues($params)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getMembersListForApprovalCount($userId, $institutionId)
    {
        try {
            return Yii::$app->db->createCommand("CALL getpendingmemberscount(:userId, :institutionid)")
                ->bindValue(':userId', $userId)
                ->bindValue(':institutionid',$institutionId)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getallFeedbackCount($userId, $institutionId)
    {
        $params = [
            ':userId' => $userId,
            ':currentDate' => gmdate("Y-m-d H:i:s"),
            ':institutionid' => $institutionId
        ];
        try {
            return Yii::$app->db->createCommand("CALL getallfeedbackscount(:userId, :currentDate, :institutionid)")
                ->bindValues($params)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getAllPendingAlbumsCount($userId, $institutionId)
    {
        try {
            return Yii::$app->db->createCommand("CALL getpendingalbumscount(:userId,:institutionid)")
                ->bindValue(':userId', $userId)
                ->bindValue(':institutionid', $institutionId)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }

    public static function getOrdersCount($userId, $privilage, $propertyGroupId, $institutionId)
    {
        $params = [
            ':userId' => $userId,
            ':propertyGroupId' => $propertyGroupId,
            ':privilageId' => $privilage,
            ':currentDate' => gmdate("Y-m-d H:i:s"),
            ':institutionid' => $institutionId
        ];
        try {
            return Yii::$app->db->createCommand("CALL get_order_count(:userId, :propertyGroupId, :privilageId, :currentDate,:institutionid)")
                ->bindValues($params)
                ->queryOne();
        } catch (\Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        } 
    }

    public static function getMemberCommitteeForUser($userId)
    {
        $response = new \stdClass();
        try {
            $temp = [];
            $institutionId = [];
            $institutionId = self::getCommitteForInstitutionUser($userId);
            if ($institutionId != null && count($institutionId) > 0) {
                $response->value = new \stdClass();
                foreach ($institutionId as $item) {
                    $conversation = array(
                        'InstitutionId' => $item
                    );
                    array_push($temp, $conversation);
                }
                $response->value = $temp;
            }
            $response->Status = true;
        } catch (\Exception $e) {
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
        return $response;
    }

    public static function getCommitteForInstitutionUser($userId)
    {
        $sql = "select institutionid from committee where userid = :userId and active = 1 group by institutionid";
        try {
            return yii::$app->db->createCommand($sql)
                ->bindValue(":userId", $userId)
                ->queryColumn();
        } catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
    }
    
    public static function getNotificationDevices($institutionId,$date,$type)
    {
    	
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL getnotificationdevices(:institutionId,:date,:type) ")
    				->bindValue(':institutionId' , $institutionId)
    				->bindParam(':date', $date)
    				->bindValue(':type', $type)
    				->queryAll();
    				
    		return $data; 
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    	
    }
    
    public static function getNotifications($institutionId,$date)
    {
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL getallnotificationsforscheduler(:institutionId,:date) ")
    				->bindValue(':institutionId' , $institutionId)
    				->bindValue(':date', $date)
    				->queryAll();
    	
    		return $data;	 
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    }
    
    public static function getAllEventDevices($institutionId,$notificationId,$familyUnitId)
    { 
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL geteventdevices(:notificationId,:institutionId,:familyUnitId) ")
    				->bindValue(':institutionId' , $institutionId)
    				->bindValue(':notificationId' , $notificationId)
    				->bindValue(':familyUnitId', $familyUnitId)
    				->queryAll();
    		 
    		return $data;
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    }

    /**
     * To get the announcement devices
     */
    public static function getAnnouncementDevices($announcementId,$institutionId,$familyUnitId)
    {
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL getannouncementdevices(:announcementid,:institutionid,:familyunitid)")
    				->bindValue(':announcementid', $announcementId)
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':familyunitid', $familyUnitId)
    				->queryAll();
    		return $data;
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    }

    /**
     * To get the members devices
     */
    public static function getInstitutionMemberDevices($institutionId)
    {
    	try {
            $sql = "
            select
                um.usertype,
                dd.deviceid,
                dd.userid,
                dd.devicetype,
                s.membernotification,
                s.spousenotification,
                s.memberemail,
                s.membersms,
                m.batch,
                m.firstname,
                dd.deviceidentifier AS deviceIdentifier,
                dd.id as devicedetailid
            from devicedetails dd
            inner join usermember on dd.userid=usermember.userid
            inner join member m on m.memberid=usermember.memberid
            inner join usermember um on um.userid=dd.userid and
            um.institutionid=dd.institutionid
            inner join settings s on s.memberid=usermember.memberid
            where dd.active = true and  
            usermember.institutionid=:institutionid
            and um.usertype = 'M'
            group by dd.deviceid
            ";
    		$data = Yii::$app->db->createCommand($sql)
    				->bindValue(':institutionid', $institutionId)
    				->queryAll();
    		return $data;
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    }

    /**
     * Update rsvp details of a memeber
     * @param unknown $member
     */
    public static function  updateRspv($member)
    {
    	
    		$data = Yii::$app->db->createCommand(
    				"CALL updatersvpdetails ( :p_memberid ,
											  :p_institutionid ,
											  :p_membertitle ,
											  :p_firstName,
											  :p_middleName,
											  :p_lastName ,
											  :p_member_mobile1 ,
											  :p_spousetitle ,
											  :p_spouse_firstName ,
											  :p_spouse_middleName ,
											  :p_spouse_lastName ,
        											  :p_spouse_mobile1 ) ")
            		->bindValue(":p_memberid", $member->MemberID)
            		->bindValue(":p_institutionid", $member->InstitutionID)
            		->bindValue(":p_membertitle", $member->MemberTitle)
            		->bindValue(":p_firstName", $member->FirstName)
            		->bindValue(":p_middleName", $member->MiddleName)
            		->bindValue(":p_lastName", $member->LastName)
            		->bindValue(":p_member_mobile1", $member->MemberMobile1)
            		->bindValue(":p_spousetitle", $member->SpouseTitle)
            		->bindValue(":p_spouse_firstName", $member->SpouseFirstName)
            		->bindValue(":p_spouse_middleName", $member->SpouseMiddleName)
            		->bindValue(":p_spouse_lastName", $member->SpouseLastName)
            		->bindValue(":p_spouse_mobile1", $member->spousemobile1)
            		->execute();

			}
	/**
	 * Delete Temp memeber tables
	 */
	public static function deleteTempMemberDeatils($memeberId)
    {
		
		ExtendedTempmemberadditionalinfomail::deleteAll(['memberid' => $memeberId]);
		ExtendedTempmemberadditionalinfo::deleteAll(['memberid' => $memeberId]);
		ExtendedTempdependantmail::deleteAll(['tempmemberid' => $memeberId]);
		ExtendedTempdependant::deleteAll(['tempmemberid' => $memeberId]);
		ExtendedTempmember::deleteAll(['temp_memberid' => $memeberId]);
		ExtendedTempmembermail::deleteAll(['temp_memberid' => $memeberId]);
		ExtendedEditmember::deleteAll(['memberid' => $memeberId]);
	}		

    
    public static function updateUserCredentials( $memberDetails )
    {
    	
    	try{
    		
    		$memberUserId = 0;
    		$memberSpouseId = 0;
    		$userId = 0;
    		$spouseUserId = 0;
    		$memberCount = 0;
    		$spouseCount = 0;
    		 
    		$credentials = ExtendedUserCredentials::getCredential($memberDetails);
				  		 
    		if (!empty($credentials)){
    		 	$memberUserId   = $credentials['memberuserid'];
    		 	$memberSpouseId = $credentials['spouseuserid'];
    		 	$userId         = $credentials['userid'];
    		 	$spouseUserId   = $credentials['spouseid'];
    		 	$memberCount    = $credentials['memberusercount'];
    		 	$spouseCount    = $credentials['spouseusercount'];
    		 	
    		 }
    		if (($memberUserId == $spouseUserId && $spouseUserId != 0) && ($memberSpouseId == $userId && $userId != 0)) {
    		 	$memberModal = ExtendedUserMember::find()
    		 	->where(['userid'=>$memberUserId ,'institutionid'=>$memberDetails->institutionid
    		 			])
    		 	->one();
    		 	$memberModal->userid = $memberSpouseId;
    		 	$memberModal->save();

    		 	$memberModal = ExtendedUserMember::find()
    		 	->where(['userid'=>$memberUserId ,'institutionid'=>$memberDetails->institutionid
    		 	])
    		 	->one();
    		 	$memberModal->userid = $memberUserId;
    		 	$memberModal->save();
    		 	
    		 }
    		 else if ($memberDetails->member_mobile1 != null && !empty($memberDetails->member_mobile1))
    		 {
    		    $userType = ExtendedMember::USER_TYPE_MEMBER;
    		 	//If user with same number already exists.
    		 	if ($userId > 0 && $memberUserId > 0 && $userId != $memberUserId)
    		 	{
    		 
    		 		ExtendedUserMember::updateUserMember($userId,$memberUserId,$memberDetails->institutionid);
    		 		if (ExtendedCommittee::checkUserAvailableInCurrentCommittee($memberUserId, $memberDetails->institutionid))
    		 		{
    		 			ExtendedCommittee::updateCommitteeUserid($userId, $memberUserId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$conversationcount = 0 ;
    		 		$conversation = ExtendedConversation::getConversationCreatedCount($memberUserId);
    		 		
    		 		if (!empty($conversation['conversationcount'])){
    		 			$conversationcount = $conversation['conversationcount'];
    		 		}
    		 	
    		 		if ($conversationcount > 0)
    		 		{
    		 			ExtendedConversation::updateConversationCreated($userId, $memberUserId);
    		 		}
    		 		
    		 		$countNo = 0; 
    		 		$count = ExtendedUserConversation::getUserConversationCreatedCount($memberUserId);
    		 		if (!empty($count['userconversationcount'])){
    		 			$countNo = $count['userconversationcount'];
    		 		}
    		 	
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversation::updateUserConversationUser($userId, $memberUserId);
    		 		}
    		 
    		 		$countNo = 0;
    		 		$count = ExtendedUserConversationTopic::getUserConversationCreatedCount($memberUserId, $memberDetails->institutionid);
    		 		if (!empty($count['conversationtopiccount'])){
    		 			$countNo = $count['conversationtopiccount'];
    		 		}
    		 		
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversationTopic::updateConversationTopicUser($userId, $memberUserId,$memberDetails->institutionid);
    		 		}
    		 	
    		 		$countNo = 0;
    		 		$count = ExtendedUserConversationTopic::getUserConversationTopicCreatedCount($memberUserId);
    		 		if (!empty($count['userconversationtopiccount'])){
    		 			$countNo = $count['userconversationtopiccount'];
    		 		}
    		 			
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversationTopic::updateUserConversationTopicUser($userId, $memberUserId);
    		 		}
    		 		
    		 		ExtendedSuccessfulleventsent::deleteSuccessfullEventSentDetailsUsingUserid($memberUserId, $memberDetails->institutionid);
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedEventsentdetails::getUserEventSentDetailsCount($memberUserId, $memberDetails->institutionid);
    		 		if (!empty($count['eventsentdetailscount'])){
    		 			$countNo = $count['eventsentdetailscount'];
    		 		}
    		 		
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedEventsentdetails::deleteEventSentDetailsUsingUserid($memberUserId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedEventsentdetails::getUserEventSeenDetailsCount($memberUserId, $memberDetails->institutionid);
    		 		if (!empty($count['eventseendetailscount'])){
    		 			$countNo = $count['eventseendetailscount'];
    		 		}
    		 			
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedEventsentdetails::deleteEventSeenDetailsUsingUserid($memberUserId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedDevicedetails::getUserDeviceDetailsCount($memberUserId, $memberDetails->institutionid);
    		 		if(!empty($count['devicedetailscount'])){
    		 			$countNo = $count['devicedetailscount'];
    		 		}
    		 		
    		 		if($countNo > 0)
    		 		{
    		 			ExtendedDevicedetails::deleteDeviceDetailsUsingUserid($memberUserId, $memberDetails->institutionid);
    		 		}
    				
    		 		$countNo = 0;
    		 		$count = ExtendedUserToken::getUserTokenCount($memberUserId);
    		 		if(!empty($count['usertokencount'])){
    		 			$countNo = $count['usertokencount'];
    		 		}
    		 		
    		 		if($countNo > 0)
    		 		{
    		 			ExtendedUserToken::deleteUserToken($memberUserId);
    		 		}
    		 
    		 		ExtendedUserLocation::deleteUserLocation($memberUserId);
    		 		ExtendedRsvpdetails::deleteRsvpDetailsByUserId($memberUserId, $memberDetails->institutionid);
    		 		ExtendedOrderitems::deleteOrderItems($memberUserId);
    		 		
    		 		$orderNotificationSent = ExtendedOrdernotificationsent::getOrderIdByCreatedBy($memberUserId);
    		 		if((!empty($orderNotificationSent)) && (count($orderNotificationSent) > 0))
    		 		{
    		 			
    		 			foreach ($orderNotificationSent as $key => $value)
    		 			{
    		 				ExtendedOrdernotificationsent::deleteOrderSentByOrderId($value['orderid']);
    		 				ExtendedOrdernotifications::deleteOrderNotificationsByOrderId($value['orderid']);
    		 			}
    		 		}
    		 		
    		 		ExtendedOrders::deleteOrder($memberUserId);
    		 		
    		 		$memberCount = ExtendedUserMember::getUserMemberCount($memberUserId);
    		 
    		 		if($userId != $memberUserId && $spouseUserId != $memberUserId && count($memberCount) <= 1)
    		 		{
    		 			ExtendedUserCredentials::deleteUserCredentials($memberUserId);
    		 		}
    		 
    		 	}
    		 	elseif ($memberCount <= 1 && $memberUserId != 0 && $spouseUserId != $memberUserId )
    		 	{
    		 		//update user credentials
    		 		ExtendedUserCredentials::updateUserCredentials($memberDetails->member_email,$memberDetails->member_mobile1,$memberUserId);
    		 	}
    		 	elseif ($memberCount == 1 && $memberUserId != 0)
    		 	{
	    		 		$userType = ExtendedMember::USER_TYPE_MEMBER;
	    		 		ExtendedUserMember::deleteUserMember($memberDetails->memberid,$memberDetails->institutionid,$userType);
	    		 		$userCredentials = new ExtendedUserCredentials();
	    		 		$userCredentials->institutionid = $memberDetails->institutionid;
	    		 		$userCredentials->emailid = $memberDetails->member_email;
	    		 		$userCredentials->usertype = "M";
	    		 		$userCredentials->password = 'remember';
	    		 		$userCredentials->mobileno = $memberDetails->member_mobile1;
	    		 		$userCredentials->initiallogin = false;
	    		 		$newUserId = ExtendedUserCredentials::addUserDetails($userCredentials);
	    		 		if($newUserId != 0)
	    		 		{
	    		 			$userMember = new ExtendedUserMember();
	    		 			$userMember->userid = $newUserId;
	    		 			$userMember->memberid = $memberDetails->memberid;
	    		 			$userMember->institutionid = $memberDetails->institutionid;
	    		 			$userMember->usertype = "M";
	    		 			ExtendedUserMember::addUserMember($userMember);
	    		 		}
    		 	}
    		 	elseif ($userId == 0)
    		 	{
    		 		$count = ExtendedUserMember::getUserMembersCount($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 		if(!empty($count['usermembercount'])){
    		 				$countNo = $count['usermembercount'];
    		 			}
    		 				
    		 			if($countNo > 0)
    		 			{
    		 				ExtendedUserMember::deleteUserMember($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 			}
    		 			
    		 			$currentUserInstitution = ExtendedUserCredentials::getUserCurrentInstitution($memberUserId);
    		 			$currentUserInstitution = $currentUserInstitution['institutionid'];
    		 			$institutionId = ExtendedUserMember::getAnyInstitutionId($memberUserId,$memberDetails->institutionid);
    		 			$institutionId = $institutionId['institutionid'];
    		 			if($memberDetails->institutionid == $currentUserInstitution)
    		 			{
    		 				ExtendedUserCredentials::updateInstitutionId($institutionId,$memberUserId);
    		 			}
    		 			
    		 			$userCredentials = new ExtendedUserCredentials();
    		 			$userCredentials->institutionid = $memberDetails->institutionid;
    		 			$userCredentials->emailid = $memberDetails->member_email;
    		 			$userCredentials->usertype = "M";
    		 			$userCredentials->password = 'remember';
    		 			$userCredentials->mobileno = $memberDetails->member_mobile1;
    		 			$userCredentials->initiallogin = false;
    		 			$newUserId = ExtendedUserCredentials::addUserDetails($userCredentials);
    		 			if($newUserId != 0)
    		 			{
    		 				$userMember = new ExtendedUserMember();
    		 				$userMember->userid = $newUserId;
    		 				$userMember->memberid = $memberDetails->memberid;
    		 				$userMember->institutionid = $memberDetails->institutionid;
    		 				$userMember->usertype = "M";
    		 				ExtendedUserMember::addUserMember($userMember);
    		 			}
    		 		}
    		 		elseif ($userId > 0 && $memberUserId < 1)
    		 		{
    		 		    $countNo = 0;
    		 			$count = ExtendedUserMember::getUserMembersCount($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 			if(!empty($count['usermembercount'])){
    		 				$countNo = $count['usermembercount'];
    		 			}
    		 				
    		 			if($countNo < 1)
    		 			{
    		 				$userMember = new ExtendedUserMember();
    		 				$userMember->userid = $userId;
    		 				$userMember->memberid = $memberDetails->memberid;
    		 				$userMember->institutionid = $memberDetails->institutionid;
    		 				$userMember->usertype = "M";
    		 				ExtendedUserMember::addUserMember($userMember);
    		 			}
    		 		}
    		 }	
    		 
    		 elseif ($memberDetails->spouse_mobile1 != null && $memberDetails->spouse_mobile1 != '')
    		 {
    		     $userType = ExtendedMember::USER_TYPE_SPOUSE;
    		     
    		 	//if user with same mobile number exists
    		 	if($spouseUserId > 0 && $memberSpouseId > 0 && $spouseUserId != $memberSpouseId)
    		 	{
    		 		//rm.UserMemberRepository.UpdateUserMember(spouseUserId, memberSpouseId, member.InstitutionID);
    		 		if (ExtendedCommittee::checkUserAvailableInCurrentCommittee($memberSpouseId, $memberDetails->institutionid))
    		 		{
    		 			ExtendedCommittee::updateCommitteeUserid($spouseUserId, $memberSpouseId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$conversationcount = 0 ;
    		 		$conversation = ExtendedConversation::getConversationCreatedCount($memberSpouseId);
    		 			
    		 		if (!empty($conversation['conversationcount'])){
    		 			$conversationcount = $conversation['conversationcount'];
    		 		}
    		 		
    		 		if ($conversationcount > 0)
    		 		{
    		 			ExtendedConversation::updateConversationCreated($spouseUserId, $memberSpouseId);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedUserConversation::getUserConversationCreatedCount($memberSpouseId);
    		 		if (!empty($count['userconversationcount'])){
    		 			$countNo = $count['userconversationcount'];
    		 		}
    		 		
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversation::updateUserConversationUser($spouseUserId, $memberSpouseId);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedUserConversationTopic::getUserConversationCreatedCount($memberUserId, $memberDetails->institutionid);
    		 		if (!empty($count['conversationtopiccount'])){
    		 			$countNo = $count['conversationtopiccount'];
    		 		}
    		 			
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversationTopic::updateConversationTopicUser($spouseUserId, $memberSpouseId,$memberDetails->institutionid);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedUserConversationTopic::getUserConversationTopicCreatedCount($memberSpouseId);
    		 		if (!empty($count['userconversationtopiccount'])){
    		 			$countNo = $count['userconversationtopiccount'];
    		 		}
    		 		
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedUserConversationTopic::updateUserConversationTopicUser($spouseUserId, $memberSpouseId);
    		 		}
    		 		
    		 		ExtendedSuccessfulleventsent::deleteSuccessfullEventSentDetailsUsingUserid($memberSpouseId, $memberDetails->institutionid);
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedEventsentdetails::getUserEventSentDetailsCount($memberSpouseId, $memberDetails->institutionid);
    		 		if (!empty($count['eventsentdetailscount'])){
    		 			$countNo = $count['eventsentdetailscount'];
    		 		}
    		 			
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedEventsentdetails::deleteEventSentDetailsUsingUserid($memberSpouseId, $memberDetails->institutionid);
    		 		}
    		 		$countNo = 0;
    		 		$count = ExtendedEventsentdetails::getUserEventSeenDetailsCount($memberSpouseId, $memberDetails->institutionid);
    		 		if (!empty($count['eventseendetailscount'])){
    		 			$countNo = $count['eventseendetailscount'];
    		 		}
    		 		
    		 		if ( $countNo > 0)
    		 		{
    		 			ExtendedEventsentdetails::deleteEventSeenDetailsUsingUserid($memberSpouseId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedDevicedetails::getUserDeviceDetailsCount($memberSpouseId, $memberDetails->institutionid);
    		 		if(!empty($count['devicedetailscount'])){
    		 			$countNo = $count['devicedetailscount'];
    		 		}
    		 			
    		 		if($countNo > 0)
    		 		{
    		 			ExtendedDevicedetails::deleteDeviceDetailsUsingUserid($memberSpouseId, $memberDetails->institutionid);
    		 		}
    		 		
    		 		$countNo = 0;
    		 		$count = ExtendedUserToken::getUserTokenCount($memberSpouseId);
    		 		if(!empty($count['usertokencount'])){
    		 			$countNo = $count['usertokencount'];
    		 		}
    		 			
    		 		if($countNo > 0)
    		 		{
    		 			ExtendedUserToken::deleteUserToken($memberSpouseId);
    		 		}
    		 		ExtendedUserLocation::deleteUserLocation($memberSpouseId);
    		 		ExtendedRsvpdetails::deleteRsvpDetailsByUserId($memberSpouseId, $memberDetails->institutionid);
    		 		ExtendedOrderitems::deleteOrderItems($memberSpouseId);
    		 		
    		 		$orderNotificationSent = ExtendedOrdernotificationsent::getOrderIdByCreatedBy($memberUserId);
    		 		if((!empty($orderNotificationSent)) && (count($orderNotificationSent) > 0))
    		 		{
    		 		
    		 			foreach ($orderNotificationSent as $key => $value)
    		 			{
    		 				ExtendedOrdernotificationsent::deleteOrderSentByOrderId($value['orderid']);
    		 				ExtendedOrdernotifications::deleteOrderNotificationsByOrderId($value['orderid']);
    		 			}
    		 		}
    		 		
    		 		ExtendedOrders::deleteOrder($memberSpouseId);
    		 		$memberCount = ExtendedUserMember::getUserMemberCount($memberSpouseId);
    		 		 
    		 		if($spouseUserId != $memberSpouseId && $userId != $memberSpouseId && count($memberCount) <= 1)
    		 		{
    		 			ExtendedUserCredentials::deleteUserCredentials($memberSpouseId);
    		 		}
    		 	}
    		 	elseif ($spouseCount <= 1 && $memberSpouseId != 0 && $userId != $memberSpouseId )
    		 	{
    		 		//update user credentials
    		 		ExtendedUserCredentials::updateUserCredentials($memberDetails->member_email,$memberDetails->spouse_mobile1,$memberSpouseId);
    		 	}
    		 	elseif ($spouseCount == 1 && $memberSpouseId != 0)
    		 	{
    		 		$userType = ExtendedMember::USER_TYPE_SPOUSE;
    		 		ExtendedUserMember::deleteUserMember($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 		$userCredentials = new ExtendedUserCredentials();
    		 		$userCredentials->institutionid = $memberDetails->institutionid;
    		 		$userCredentials->emailid = $memberDetails->spouse_email;
    		 		$userCredentials->usertype = "S";
    		 		$userCredentials->password = 'remember';
    		 		$userCredentials->mobileno = $memberDetails->spouse_mobile1;
    		 		$userCredentials->initiallogin = false;
    		 		$newUserId = ExtendedUserCredentials::addUserDetails($userCredentials);
    		 		if($newUserId != 0)
    		 		{
    		 			$userMember = new ExtendedUserMember();
    		 			$userMember->userid = $newUserId;
    		 			$userMember->memberid = $memberDetails->memberid;
    		 			$userMember->institutionid = $memberDetails->institutionid;
    		 			$userMember->usertype = "S";
    		 			ExtendedUserMember::addUserMember($userMember);
    		 		}
    		 	}
    		 	elseif ($spouseUserId == 0)
    		 	{
    		 		$count = ExtendedUserMember::getUserMembersCount($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 		if(!empty($count['usermembercount'])){
    		 			$countNo = $count['usermembercount'];
    		 		}
    		 			
    		 		if($countNo > 0)
    		 		{
    		 			ExtendedUserMember::deleteUserMember($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 		}
    		 	
    		 		$userCredentials = new ExtendedUserCredentials();
    		 		$userCredentials->institutionid = $memberDetails->institutionid;
    		 		$userCredentials->emailid = $memberDetails->spouse_email;
    		 		$userCredentials->usertype = "S";
    		 		$userCredentials->password = 'remember';
    		 		$userCredentials->mobileno = $memberDetails->spouse_mobile1;
    		 		$userCredentials->initiallogin = false;
    		 		$newUserId = ExtendedUserCredentials::addUserDetails($userCredentials);
    		 		if($newUserId != 0)
    		 		{
    		 			$userMember = new ExtendedUserMember();
    		 			$userMember->userid = $newUserId;
    		 			$userMember->memberid = $memberDetails->memberid;
    		 			$userMember->institutionid = $memberDetails->institutionid;
    		 			$userMember->usertype = "S";
    		 			ExtendedUserMember::addUserMember($userMember);
    		 		}
    		 	}
    		 	elseif ($spouseUserId > 0 && $memberSpouseId < 1)
    		 	{
    		 		$count = ExtendedUserMember::getUserMembersCount($memberDetails->memberid,$memberDetails->institutionid,$userType);
    		 		if(!empty($count['usermembercount'])){
    		 			$countNo = $count['usermembercount'];
    		 		}
    		 			
    		 		if($countNo < 1)
    		 		{
    		 			$userMember = new ExtendedUserMember();
    		 			$userMember->userid = $spouseUserId;
    		 			$userMember->memberid = $memberDetails->memberid;
    		 			$userMember->institutionid = $memberDetails->institutionid;
    		 			$userMember->usertype = "S";
    		 			ExtendedUserMember::addUserMember($userMember);
    		 		}
    		 	}
    		 }
    	} catch (Exception $e) {
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
    		return false;
    	}
    }

    //Convert to user timezone
    public static function convertToUserTimezone($dateTime, $timeZone = 'Asia/Kolkata', $dateObj = false)
    {
        try{
            $datetime = new \DateTime($dateTime);
            $dateTimeZone = new \DateTimeZone($timeZone);
            $datetime->setTimezone($dateTimeZone);
            return ($dateObj) ? $datetime : $datetime->format('Y-m-d H:i:s');
        } catch(Exception $e){
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
            return false;
        }  
    }
    /**
     * To get the member details for sync
     * @param unknown $userId
     * @param unknown $lastUpdatedOn
     * @return boolean
     */
    public function getMemberDetailsFileForSync($userId, $lastUpdatedOn)
    {
        $response = new \stdClass();
        try {
            $response->value = Yii::$app->db->createCommand("CALL getmember_details_forsync(:userId, :lastUpdatedOn)")
                    ->bindValue(':userId', $userId)
                    ->bindValue(':lastUpdatedOn', $lastUpdatedOn)
                    ->queryAll();
            $response->status = true;
        } catch(Exception $e) {
            $response->errorMessage = "Members Selection Failed";
            $response->status = false;
            $response->errorCode = 1;
            yii::error($e->getMessage().'\n'.$e->getFile().'\n'.$e->getLine());
        }
        return $response;
    }
    public function getDependantsForSync($userId)
    {

        $response = new \stdClass();
        try {
            $response->value = Yii::$app->db->createCommand("CALL get_dependants_for_sync(:userId)")
                    ->bindValue(':userId', $userId)
                    ->queryAll();
            $response->status = true;
        } catch(Exception $e) {
            $response->errorMessage = "Members Dependant Selection Failed";
            $response->status = false;
            $response->errorCode = 1;
            yii::error('Member Dependant selection failed'.$e->getMessage());
        }      
        return $response;

    }
    public function getDeletedContactsForSync($userId, $dtupdated)
    { 
        $response = new \stdClass();
        try {
            $response->value = Yii::$app->db->createCommand("CALL get_deleted_members_for_sync(:userId,:dtupdated)")
                    ->bindValue(':userId', $userId)
                    ->bindValue(':dtupdated', $dtupdated)
                    ->queryColumn();
            $response->status = true;
        } catch(Exception $e) {
            $response->errorMessage = "Removed contact selection failed";
            $response->status = false;
            $response->errorCode = 1;
            yii::error('Removed contact selection failed'.$e->getMessage());
        }      
        return $response;

    }
    public function getAssosiatedInstitutions($userId, $userType,$institutionId)
    {   
        $response = new \stdClass();
        try {
            $response->value = Yii::$app->db->createCommand("CALL get_associatedinstitutions_for_sync(:userid,:userType,:institutionId)")
                        ->bindValue(':userType', $userType)
                        ->bindValue(':userid', $userId)
                        ->bindValue(':institutionId', $institutionId)
                        ->queryAll();
            if($userType == 'S' && empty($response->value)) {
                $response->value = Yii::$app->db->createCommand("CALL get_associatedinstitutions_for_sync(:userid,:userType,:institutionId)")
                        ->bindValue(':userType', 'M')
                        ->bindValue(':userid', $userId)
                        ->bindValue(':institutionId', $institutionId)
                        ->queryAll();
            }
            $response->status = true;
        } catch(Exception $e) {
            $response->errorMessage = "Associated institution selection failed";
            $response->status = false;
            $response->errorCode = 1;
            yii::error('Associated institution selection failed'.$e->getMessage());
        }      
        return $response;
    }
    public function getCommitteeDesignationsForSync($userId,$userType)
    {
        $typeBit = ($userType == "M") ? false : true;
        $response = new \stdClass();
        try {
            $response->value = Yii::$app->db->createCommand("CALL get_committee_designations_for_sync(:userId,:typeBit)")
                    ->bindValue(':userId', $userId)
                    ->bindValue(':typeBit', $typeBit)
                    ->queryAll();
            $response->status = true;
        } catch(Exception $e) {
            $response->errorMessage = "Committee desg selection failed";
            $response->status = false;
            $response->errorCode = 1;
            yii::error('Committee desg'.$e->getMessage());
        }      
        return $response;

    }

    public static function getMemberBatches($userId=0,$institutionId=0)
    {
        if($userId>0) {
            $data = Yii::$app->db->createCommand(
                "SELECT 
                GROUP_CONCAT(member.batch) as batch
            FROM
                member
                    INNER JOIN
                usermember ON member.memberid = usermember.memberid
            WHERE
                usermember.userid =:userId")
                ->bindValue(':userId', $userId)
                ->queryScalar();
            return $data;
        } else {
            return '';
        }
    }

    public static function getInstitutionMinBatch()
    {
        // if($institutionId>0) {
        //     $data = Yii::$app->db->createCommand("SELECT MIN(batch) FROM member where institutionid=:institutionId")
        //     ->bindValue(':institutionId', $institutionId)
        //     ->queryScalar();
            
        // }
        return 1960;
    }
    
    public function getMemberInstitutionBatch($userid=0,$institutionId=0) 
    {
        if($userid>0) {
            $data = Yii::$app->db->createCommand(
                "SELECT 
                member.batch as batch
            FROM
                member
                    INNER JOIN
                usermember ON member.memberid = usermember.memberid
            WHERE
                usermember.userid =:userid and usermember.institutionid= :institutionid")
                ->bindValue(':userid', $userid)
                ->bindValue(':institutionid', $institutionId)
                ->queryScalar();
            return $data;
        } else {
            return '';
        }
    }

    public function getMemberBatchArray($institutionId=0)
    {
        if($institutionId>0) {
            $data = Yii::$app->db->createCommand("SELECT 
                    member.batch, usermember.userid
                    FROM
                        member
                            INNER JOIN
                        usermember ON member.memberid = usermember.memberid
                    WHERE
                        usermember.institutionid = :institutionId
                            AND member.institutionid = :institutionId")
                    ->bindValue(':institutionId', $institutionId)    
                    ->queryAll();
            return $data;
        } else {
            return [];
        }
    }
}
