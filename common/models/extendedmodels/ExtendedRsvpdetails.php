<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Rsvpdetails;

/**
 * This is the model class for table "rsvpdetails".
 *
 * @property int $id
 * @property int $rsvpid
 * @property int $userid
 * @property string $membername
 * @property int $rsvpvalue
 * @property string $mobile
 * @property int $membercount
 * @property int $childrencount
 * @property int $guestcount
 * @property int $memberid
 * @property string $acksentdatetime
 * @property string $createddatetime
 *
 * @property Member $member
 * @property Events $rsvp
 * @property Usercredentials $user
 * @property Rsvpnotification[] $rsvpnotifications
 * @property Rsvpnotificationsent[] $rsvpnotificationsents
 */
class ExtendedRsvpdetails extends Rsvpdetails
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'rsvpdetails';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['rsvpid', 'userid', 'createddatetime'], 'required'],
				[['rsvpid', 'userid', 'rsvpvalue', 'membercount', 'childrencount', 'guestcount', 'memberid'], 'integer'],
				[['acksentdatetime', 'createddatetime'], 'safe'],
				[['membername'], 'string', 'max' => 45],
				[['mobile'], 'string', 'max' => 13],
				[['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
				[['rsvpid'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['rsvpid' => 'id']],
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
				'rsvpid' => 'Rsvpid',
				'userid' => 'Userid',
				'membername' => 'Membername',
				'rsvpvalue' => 'Rsvpvalue',
				'mobile' => 'Mobile',
				'membercount' => 'Membercount',
				'childrencount' => 'Childrencount',
				'guestcount' => 'Guestcount',
				'memberid' => 'Memberid',
				'acksentdatetime' => 'Acksentdatetime',
				'createddatetime' => 'Createddatetime',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMember()
	{
		return $this->hasOne(ExtendedMember::className(), ['memberid' => 'memberid','userid' => 'userid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRsvp()
	{
		return $this->hasOne(ExtendedEvents::className(), ['id' => 'rsvpid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'userid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRsvpnotifications()
	{
		return $this->hasMany(ExtendedRsvpnotification::className(), ['rsvpid' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRsvpnotificationsents()
	{
		return $this->hasMany(ExtendedRsvpnotificationsent::className(), ['rsvpid' => 'id']);
	}
	/**
	 * To save Rsvp details
	 * @param unknown $itemId
	 * @param unknown $type
	 * @param unknown $memberCount
	 * @param unknown $childrenCount
	 * @param unknown $guestCount
	 * @param unknown $userId
	 * @param unknown $userType
	 * @param unknown $institutionId
	 * @param unknown $createddatetime
	 * @return boolean
	 */
	public static function saveRsvp($itemId,$type,$memberCount,
								$childrenCount,$guestCount,$userId,$userType,$institutionId,$createddatetime)
	{
		try {
			$saveRsvpData = Yii::$app->db->createCommand(
					"CALL setrsvpdetailsnew(:itemid,:type,:membercount,
								:childrencount,:guestcount,:userid,:usertype,:institutionid,:createddatetime)")
					->bindValue(':itemid' , $itemId)
					->bindValue(':type', $type)
					->bindValue(':membercount', $memberCount)
					->bindValue(':childrencount', $childrenCount)
					->bindValue(':guestcount', $guestCount)
					->bindValue(':userid', $userId)
					->bindValue(':usertype', $userType)
					->bindValue(':institutionid', $institutionId)
					->bindValue(':createddatetime', $createddatetime)
					->queryOne();
			return $saveRsvpData;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get rsvp event details
	 * @param unknown $eventId
	 * @return boolean
	 */
    public static function getRsvpEventDetails($eventId)
    {
        try {
            $eventRsvpdetails = Yii::$app->db->createCommand('select * from rsvpdetails where rsvpid=:eventid ')
                ->bindValue(':eventid',$eventId) 
                ->queryAll();
            return $eventRsvpdetails;

        } catch (Exception $e) {
            return false;
        }
    } 
    /**
     * To get Rsvp count details
     * @param unknown $eventId
     * @return boolean
     */
    public static function getRsvpCountDetails($eventId)
    {
        try {
            $eventRsvpdetails = Yii::$app->db->createCommand('select sum(membercount) as membercount,
            sum(childrencount) as childrencount,sum(guestcount) as guestcount from rsvpdetails where rsvpid=:eventid ')
                ->bindValue(':eventid',$eventId) 
                ->queryAll();
               
            return $eventRsvpdetails;

        } catch (Exception $e) {
            return false;
        }
    }   

	/**
	 * to get rsvp count
	 * @param unknown $userId
	 * @param unknown $currentDate
	 * @return string|NULL|\yii\db\false|boolean
	 */
	public static function getRsvpCount($userId,$currentDate)
	{	
		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$rsvpCount = Yii::$app->db->createCommand(
					"CALL geteventswithrsvpcount(:userid,:currentdate,:institutionid)")
					->bindValue(':userid' , $userId)
					->bindValue(':currentdate', $currentDate)
					->bindValue(':institutionid',$institutionId)
					->queryScalar();
			return $rsvpCount;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * to get all rsvp service
	 * @param unknown $eventId
	 * @param unknown $rsvpValue
	 * @return boolean
	 */
	public static function getRsvpService($eventId,$rsvpValue)
	{
		try {
			$rsvpService = Yii::$app->db->createCommand(
					"CALL getallrsvpforevent(:eventid,:rsvpvalue)")
					->bindValue(':eventid' , $eventId)
					->bindValue(':rsvpvalue', $rsvpValue)
					->queryAll();
			if(!empty($rsvpService)){
				return $rsvpService;
			}
			else{
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get acknowledged date
	 * @param $memberId int
	 * @param $eventId int
	 */
	public static function getAcknowledgedDate($memberId,$eventId)
	{
		try {
			$rsvpData = Yii::$app->db->createCommand('SELECT acksentdatetime,id FROM rsvpdetails 
							WHERE memberid=:memberid AND rsvpid=:eventid')
						->bindValue(':memberid', $memberId)
						->bindValue(':eventid', $eventId)
						->queryOne();
			return $rsvpData;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get Rsvp service details
	 * @param $eventId int
	 */
	public static function getRsvpServiceDetails($eventId,$rsvpValue)
	{
		try {
			$rsvpServiceData = Yii::$app->db->createCommand(
					"CALL getallrsvpservice(:eventid,:rsvpvalue)")
					->bindValue(':eventid' , $eventId)
					->bindValue(':rsvpvalue', $rsvpValue)
					->queryAll();
			if(!empty($rsvpServiceData)){
				return $rsvpServiceData;
			}
			else{
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To Delete rsvp details
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function deleteRsvpDetailsByUserId($memberUserId,$institutionId)
	{
		try{
			$deleteRsvpDetails = Yii::$app->db->createCommand(
					"CALL delete_rsvp_by_userid(:userid,:institutionid)")
					->bindValue(':userid',$memberUserId)
					->bindValue(':institutionid',$institutionId)
					->execute();
			return true;
		}
		catch(ErrorException $e){
			yii::error($e->getMessage());
			return false;
		}
	}
	public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }
        return $total;
    }
}

