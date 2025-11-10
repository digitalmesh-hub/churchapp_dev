<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Feedbackimagedetails;

/**
 * This is the model class for table "feedbackimagedetails".
 *
 * @property int $id
 * @property int $feedbackid
 * @property string $feedbackimage
 * @property string $createddate
 *
 * @property Feedback $feedback
 */
class ExtendedFeedbackimagedetails extends Feedbackimagedetails
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackimagedetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbackid', 'feedbackimage', 'createddate'], 'required'],
            [['feedbackid'], 'integer'],
            [['createddate'], 'safe'],
            [['feedbackimage'], 'string', 'max' => 150],
            [['feedbackid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['feedbackid' => 'feedbackid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'feedbackid' => 'Feedbackid',
            'feedbackimage' => 'Feedbackimage',
            'createddate' => 'Createddate',
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
     * To get feedback image details
     * @param $feedbackId int
     */
    public static function getFeedbackImages($feedbacKId)
    {
    	try {
    		$feedbackImage = Yii::$app->db->createCommand('select id,feedbackid,feedbackimage from feedbackimagedetails
    										where feedbackid = :feedbackid')
                                    ->bindValue(':feedbackid',$feedbacKId)
                                    ->queryAll();
    		return $feedbackImage;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To save feedback images
     * @param unknown $feedbackId
     * @param unknown $feedbackImages
     * @param unknown $createdDate
     * @return boolean
     */
    public static function saveFeedbackImage($feedbackId,$feedbackImages,$createdDate)
    {
    	try {
    		$saveImage = Yii::$app->db->createCommand('insert into feedbackimagedetails(feedbackid,feedbackimage,createddate)
    						values (:feedbackid,:feedbackimage,:createddate)')
                                  ->bindValue(':feedbackid',$feedbackId)
                                  ->bindValue(':feedbackimage',$feedbackImages)
                                  ->bindValue(':createddate',$createdDate)
                                  ->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
