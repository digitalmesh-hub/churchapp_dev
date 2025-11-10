<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Feedback;
use common\models\basemodels\Feedbacktype;
use common\models\basemodels\Institution;
use common\models\basemodels\Usercredentials;


/**
 * This is the model class for table "feedback".
 *
 * @property int $feedbackid
 * @property int $feedbacktypeid
 * @property int $userid
 * @property string $description
 * @property string $createddatetime
 * @property int $isresponded
 * @property int $feedbackrating
 * @property int $institutionid
 *
 * @property Feedbacktype $feedbacktype
 * @property Institution $institution
 * @property Usercredentials $user
 * @property Feedbackimagedetails[] $feedbackimagedetails
 * @property Feedbacknotification[] $feedbacknotifications
 * @property Feedbacknotificationsent[] $feedbacknotificationsents
 */
class ExtendedFeedback extends Feedback
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbacktypeid', 'userid', 'institutionid'], 'required'],
            [['feedbacktypeid', 'userid', 'feedbackrating', 'institutionid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['description'], 'string', 'max' => 250],
            [['isresponded'], 'string', 'max' => 4],
            [['feedbacktypeid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedbacktype::className(), 'targetAttribute' => ['feedbacktypeid' => 'feedbacktypeid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feedbackid' => 'Feedbackid',
            'feedbacktypeid' => 'Feedbacktypeid',
            'userid' => 'Userid',
            'description' => 'Description',
            'createddatetime' => 'Createddatetime',
            'isresponded' => 'Isresponded',
            'feedbackrating' => 'Feedbackrating',
            'institutionid' => 'Institutionid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacktype()
    {
        return $this->hasOne(ExtendedFeedbacktype::className(), ['feedbacktypeid' => 'feedbacktypeid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(ExtendedInstitution::className(), ['id' => 'institutionid']);
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
    public function getFeedbackimagedetails()
    {
        return $this->hasMany(ExtendedFeedbackimagedetails::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotifications()
    {
        return $this->hasMany(ExtendedFeedbacknotification::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotificationsents()
    {
        return $this->hasMany(ExtendedFeedbacknotificationsent::className(), ['feedbackid' => 'feedbackid']);
    }
    /**
     * to get feedback count
     * @param $userId int
     * @param $currentDate DateTime
     */
    public static function getFeedbackCount($userId,$currentDate)
    {
        $institutionId = Yii::$app->user->identity->institutionid;
    	try {
    		$feedbackCount = Yii::$app->db->createCommand(
    				"CALL getallfeedbackscount(:userid,:currentdate,:institutionid)")
    				->bindValue(':userid', $userId)
    				->bindValue(':currentdate', $currentDate)
                    ->bindValue(':institutionid',$institutionId)
    				->queryScalar();
    		return $feedbackCount;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }
   /**
    * To save feedback data
    * @param unknown $feedbackType
    * @param unknown $userId
    * @param unknown $feedback
    * @param unknown $createdTime
    * @param unknown $isResponded
    * @param unknown $rating
    * @param unknown $institutionId
    * @return \yii\db\false|boolean
    */
    public static function saveFeedbackData($feedbackType,$userId,$feedback,$createdTime,$isResponded,$rating,$institutionId)
    {
    	try {
			$saveFeedback = Yii::$app->db->createCommand(
							"CALL addfeedback(:feedbacktype,:userid,:feedback,:createdtime,:isresponded,:rating,:institutionid)")
							->bindValue(':feedbacktype', $feedbackType)
							->bindValue(':userid', $userId)
							->bindValue(':feedback', $feedback)
							->bindValue(':createdtime', $createdTime)
							->bindValue(':isresponded', $isResponded)
							->bindValue(':rating', $rating)
							->bindValue(':institutionid', $institutionId)
							->queryOne();
			return $saveFeedback;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     * @param unknown $feedbackid
     * @return boolean
     */
    public static function getRequestData($feedbackid, $isService = false)
    {
        try {
            if($isService){
                $requestData = Yii::$app->db->createCommand(
                    "CALL getfeedbackdetails(:feedbackid)")
                    ->bindValue(':feedbackid' , $feedbackid )
                    ->queryAll();
                if(!empty($requestData)){
                    return $requestData;
                } else {
                    return true;
                }
            } else {
                $requestData = Yii::$app->db->createCommand(
                    "CALL getfeedbackdetails(:feedbackid)")
                    ->bindValue(':feedbackid' , $feedbackid )
                    ->queryOne();
                return $requestData;
            }  
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * To update feedback with feedback email
     * @param unknown $institutionId
     * @param unknown $feedbackemail
     * @return number|boolean
     */
    public static function  saveFeedbackEmail($institutionId,$feedbackemail)
    {
        try {
            $saveFeedbackEmail = Yii::$app->db->createCommand('update institution set feedbackemail =:feedbackemail WHERE id=:institutionid')
                                        ->bindValue(':institutionid',$institutionId)
                                        ->bindValue(':feedbackemail',$feedbackemail)
                                        ->execute();
            return $saveFeedbackEmail;
    
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * To get the feedback email
     * @param unknown $institutionId
     * @return \yii\db\false|boolean
     */
    public static function getFeedbackEmail($institutionId)
    {
        try {
            $getFeedbackEmail = Yii::$app->db->createCommand('select feedbackemail from institution where id=:institutionid')
                                    ->bindValue(':institutionid',$institutionId)
                                    ->queryOne();
            return $getFeedbackEmail;
        } catch (Exception $e) {
            return false;
        }
    }
   /**
    * To get all feedbacks
    * @param unknown $userId
    * @param unknown $currentDate
    * @return boolean
    */
    public static function getAllFeedback($userId,$currentDate)
    {
        $institutionId = Yii::$app->user->identity->institutionid;
    	try {
    		$feedBackData = Yii::$app->db->createCommand(
                    "CALL getallfeedbacktolist(:userid,:currentdate,:institutionid)")
                    ->bindValue(':userid' , $userId )
                    ->bindValue(':currentdate' ,$currentDate)
                    ->bindValue(':institutionid',$institutionId)
                    ->queryAll();
            if(!empty($feedBackData)){
    		  return $feedBackData;
            }
            else{
                return true;
            }
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To update isResponded bit
     * @param unknown $feedbackId
     * @return boolean
     */
    public static function updateRespondedBit($feedbackId)
    {
    	try {
    		$updateBit = Yii::$app->db->createCommand('update feedback set isresponded=1 where feedbackid = :feedbackid')
			    		->bindValue(':feedbackid',$feedbackId)
			    		->execute();
    		return true;
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }
   
}
