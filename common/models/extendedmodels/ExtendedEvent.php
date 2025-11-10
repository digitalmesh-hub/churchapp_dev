<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Events;
use common\models\basemodels\Usercredentials;
use common\models\basemodels\Familyunit;
use common\models\basemodels\Institution;
use Exception;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $notehead
 * @property string $notebody
 * @property string $activitydate
 * @property string $createddate
 * @property string $activatedon
 * @property string $noteurl
 * @property string $eventtype
 * @property int $createduser
 * @property string $venue
 * @property string $time
 * @property string $expirydate
 * @property int $rsvpavailable
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property int $iseventpublishable
 * @property int $familyunitid
 * @property  $batch
 *
 * @property Album $album
 * @property Usercredentials $createduser0
 * @property Familyunit $familyunit
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Eventseendetails[] $eventseendetails
 * @property Notificationlog[] $notificationlogs
 * @property Rsvpdetails[] $rsvpdetails
 */
class ExtendedEvent extends Events
{

	const SCENARIO_EVENTS = 'events';
    const SCENARIO_NEWS = 'news';

    public $members;

  	public function rules()
    {
        return [
            [['institutionid', 'createduser', 'modifiedby', 'familyunitid'], 'integer'],
            [['createddate', 'modifieddatetime', 'publishedon'], 'safe'],
            [['notebody'],'trim'],
            [['members'],'string'],
            [['notebody', 'notehead'], 'required', 'enableClientValidation' => false],
            [['notehead'], 'string', 'max' => 500],
        	[['noteurl'], 'url', 'defaultScheme' => 'http', 'enableClientValidation' => false],
            [['notebody'], 'string', 'max' => 34359738368],
            [['eventtype'], 'string', 'max' => 1],
            [['venue'], 'string', 'max' => 150],
            [['time'], 'string', 'max' => 45],
            [['rsvpavailable', 'iseventpublishable'], 'integer', 'max' => 4],
            [['createduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createduser' => 'id']],
            [['familyunitid'], 'exist', 'skipOnError' => true, 'targetClass' => Familyunit::className(), 'targetAttribute' => ['familyunitid' => 'familyunitid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']], 
            ['activitydate','required'],    
            [['activatedon'],'required', 'on' => self::SCENARIO_EVENTS],
			[['activatedon', 'expirydate'], 'date', 'format' => 'php:d F Y', 'on' => self::SCENARIO_EVENTS],
			[['activitydate'], 'date', 'format' => 'php:d F Y H:i:s', 'on' => self::SCENARIO_EVENTS],
			[['activitydate'], 'date', 'format' => 'php:d F Y', 'on' => self::SCENARIO_NEWS],
			[['activitydate'], 'validateActivatedDate'],
        	['activatedon', 'validateActivatedOn','on' => self::SCENARIO_EVENTS],
			['expirydate', 'validateExpiryDate'],
			['batch','required','on' => [self::SCENARIO_EVENTS,self::SCENARIO_NEWS] ]

      ];
    }
    public function attributeLabels()
    {   

    	$return = [];
    	switch ($this->scenario) {
    		case self::SCENARIO_EVENTS:
    			$return = [
		            'id' => 'ID',
		            'institutionid' => 'Institutionid',
		            'notehead' => 'Event Heading',
		            'notebody' => 'Event body',
		            'activitydate' => 'Event Date and Time',
		            'createddate' => 'Created On',
		            'activatedon' => 'Activated On',
		            'noteurl' => 'Note url',
		            'eventtype' => 'Eventtype',
		            'createduser' => 'Createduser',
		            'venue' => 'Venue',
		            'time' => 'Time',
		            'expirydate' => 'Expiry Date',
		            'rsvpavailable' => 'Rsvpavailable',
		            'modifiedby' => 'Modifiedby',
		            'modifieddatetime' => 'Modifieddatetime',
		            'iseventpublishable' => 'iseventpublishable',
		            'familyunitid' => 'Familyunitid',
					'publishedon' => 'Published On',
					'batch' => 'Batch'
        		];
    			break;
    		case self::SCENARIO_NEWS:
    			$return = [
		            'id' => 'ID',
		            'institutionid' => 'Institutionid',
		            'notehead' => 'Notice Board Title',
		            'notebody' => 'Notice Board Body',
		            'activitydate' => 'Notice Board Date',
		            'createddate' => 'Created On',
		            'activatedon' => 'Activated On',
		            'noteurl' => 'Note url',
		            'eventtype' => 'Eventtype',
		            'createduser' => 'Createduser',
		            'venue' => 'Venue',
		            'time' => 'Time',
		            'expirydate' => 'Expiry Date',
		            'rsvpavailable' => 'Rsvpavailable',
		            'modifiedby' => 'Modifiedby',
		            'modifieddatetime' => 'Modifieddatetime',
		            'iseventpublishable' => 'iseventpublishable',
		            'familyunitid' => 'Familyunitid',
					'publishedon' => 'Published On',
					'batch' => 'Batch'
        		];
    			break;
    	}
    	return $return;
    }

    public function validateActivatedOn($attribute, $params, $validator)
    {     
        if (strtotimeNew($this->activatedon.'00:00:00') > strtotimeNew($this->activitydate)) {
        	$this->addError('activatedon', 'Activated On date should be lesser or equal to Event Date and Time ');
    	}	
    }
    public function validateActivatedDate($attribute, $params, $validator)
    {     

    	switch ($this->scenario) {
    	 	case self::SCENARIO_EVENTS:
    	 		if (strtotimeNew($this->activitydate) < strtotimeNew('now -2 minutes')) {
        			$this->addError('activitydate', "Event Date and Time should be greater than current date");
    			}
    	 		break;
    	 	case self::SCENARIO_NEWS:
    	 		if (strtotimeNew($this->activitydate) < strtotimeNew(date('Y-m-d'))) {
        			$this->addError('activitydate', "Notice Board Date should be greater than current date");
    			}
    	 		break;
    	} 	
    }
   
    public function validateExpiryDate($attribute, $params, $validator)
    {   
    	$_label = "";
    	switch ($this->scenario) {
    	 	case self::SCENARIO_EVENTS:
    	 		$_label = "Event Date and Time";
    	 		break;
    	 	case self::SCENARIO_NEWS:
    	 		$_label = "Notice Board Date";
    	 		break;
    	} 
        if (strtotimeNew($this->expirydate.'23:59:00') < strtotimeNew($this->activitydate)) {
        	$this->addError('expirydate', 'Expiry Date should be greater or equal to '.$_label);
    	}	
    }
	/**
	 * To get the details of events
	 * @param unknown $userId
	 * @param unknown $activatedOn
	 * @return mixed
	 */
	public static function getEventData($userId,$activatedOn)
	{
		try {
			$eventData = Yii::$app->db->createCommand(
							"CALL getalleventsbydate(:userid,:activatedon)")
							->bindValue(':userid', $userId)
							->bindValue(':activatedon', $activatedOn)
							->queryAll();
			return $eventData;
		
		} catch (\Exception $e) {
			return false;
		
		}
		
	}
	/**
	 * To get the rsvp events
	 * @param unknown $userId
	 * @param unknown $currentDate
	 * @return boolean
	 */
	public static function getRsvpEvents($userId,$currentDate)
	{  
		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$eventData = Yii::$app->db->createCommand(
							"CALL geteventswithrsvp(:userid, :currentdate, :institutionid)")
							->bindValue(':userid', $userId)
							->bindValue(':currentdate', $currentDate)
							->bindValue(':institutionid',$institutionId)
							->queryAll();
			return $eventData;
		} catch (Exception $e) {
            yii::error($e->getMessage());
			return false;
		}
	}    
	/**
	 * to get the event details
	 * @param $userId
	 * @param $eventId
	 */
	public static function getEventDetails($userId,$eventId)
	{
		try {
			$eventDetails = Yii::$app->db->createCommand(
							"CALL geteventdetails(:userid,:eventid)")
							->bindValue(':userid', $userId)
							->bindValue(':eventid', $eventId)
							->queryAll();
			return $eventDetails;
			
		} catch (Exception $e) {
			return false;
		}	
	}
	/**
	 * To get the event related
	 * to an institution
	 * @param $announcementId int
	 */
	public static function getEventInstitution($announcementId)
	{
		try {
			$eventInstitutionData = Yii::$app->db->createCommand('select institutionid from events 
									where id=:announcementid ')
										->bindValue(':announcementid',$announcementId)
										->queryOne();
			return $eventInstitutionData;
	
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get event details
	 * of an institution
	 * @param $eventId int
	 * @param $institutionId int
	 */
	public static function getInstitutionEventDetails($eventId,$institutionId)
	{
		try {
			$eventInstitutionData = Yii::$app->db->createCommand('select * from events where id=:eventid and institutionid=:institutionid ')
									->bindValue(':eventid',$eventId)
									->bindValue(':institutionid',$institutionId)
									->queryOne();
			return $eventInstitutionData;
		} catch (Exception $e) {
			return false;
		}
	} 
	/**
	 * To get events count
	 * @param $userId int
	 * @param $eventDate DateTime
	 */
	public static function getEventCount($userId,$eventDate)
	{

		try {
			$eventCount = Yii::$app->db->createCommand(
							"CALL geteventscount(:userid,:eventdate)")
							->bindValue(':userid', $userId)
							->bindValue(':eventdate', $eventDate)
							->queryOne();
			return $eventCount;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get all notifications
	 * of a user
	 * @param $userId int
	 * @param $activityDate dateTime 
	 */
	public static function getAllAnnouncements($userId,$activityDate)
	{	

		try {
			$allAnnouncements = Yii::$app->db->createCommand("
								CALL getallannouncements(:userid,:activitydate)")
								->bindValue(':userid', $userId)
								->bindValue(':activitydate', $activityDate)
								->queryAll();
			return $allAnnouncements;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get event details
	 * @param $eventid int
	 */
	public static function getEvent($eventid)
	{
		try {
			$eventData = Yii::$app->db->createCommand('select * from events where id=:eventid')
							->bindValue(':eventid', $eventid)
							->queryOne();
			return $eventData;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get event details
	 * @param $eventId int
	 */
	public static function getRsvpEventDetails($eventId)
	{
		try {
			$eventDetails = Yii::$app->db->createCommand("
				CALL getevent_byeventid(:eventid)")
				->bindValue(':eventid', $eventId)
				->queryOne();
			if(!empty($eventDetails)){		
				return $eventDetails;
			}
			else{
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get event details
	 * which is has no album images related to it
	 * @param unknown $eventDate
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function getAlbumEvents($eventDate,$institutionId)
	{
		try {
			$eventAlbums = Yii::$app->db->createCommand('SELECT e.notehead,e.id FROM events e WHERE NOT EXISTS (SELECT  a.eventid FROM  album a 
				WHERE  e.id=a.eventid) and e.institutionid=:institutionid and
 				e.eventtype="E" and date(e.activitydate) <= date(:expirydate) order by e.notehead')
				->bindValue(':institutionid', $institutionId)
				->bindValue(':expirydate', $eventDate)
				->queryAll();
			return $eventAlbums;
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To get notifications on events
	 * @param unknown $institutionId
	 * @param unknown $date
	 * @return mixed
	 */
	public static function getNotificationEventdetails($institutionId,$date){
		
		try {
			$eventDetails = Yii::$app->db->createCommand("
								CALL getallevents(:institutionId,:date)")
										->bindValue(':institutionId', $institutionId)
										->bindValue(':date', $date)
										->queryAll();
		
			return $eventDetails;
				
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * To get notifications on events
	 * @param unknown $institutionId
	 * @param unknown $date
	 * @return mixed
	 */
	public static function getNotificationNewsdetails($institutionId,$date){

		try {
			$news = Yii::$app->db->createCommand('SELECT * FROM events WHERE eventtype = "A" AND DATE(activitydate) = :datefield 
              AND iseventpublishable = 1 AND institutionid = :institutionid
              AND DATE(expirydate) >= DATE(:datefield)')
				->bindValue(':institutionid', $institutionId)
				->bindValue(':datefield', $date)
				->queryAll();

			return $news;
		} catch (Exception $e) {
			return false;
		}
	}

	
	/**
	 * 
	 */
	public static function getEventDetailsByEventdId($itemId)
	{
		try {
			$eventDetails = Yii::$app->db->createCommand("CALL getevent_byeventid(:eventid)")
							->bindValue(':eventid', $itemId)
							->queryOne();
			return $eventDetails;
			
		} catch (Exception $e) {
			return false;
		}
	}
}
