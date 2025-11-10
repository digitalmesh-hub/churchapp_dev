<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Affiliatedinstitution;

/**
 * This is the model class for table "affiliatedinstitution".
 *
 * @property int $affiliatedinstitutionid
 * @property int $institutionid
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $district
 * @property string $state
 * @property int $CountryID
 * @property string $pin
 * @property string $phone1_countrycode
 * @property string $phone1_areacode
 * @property string $phone1
 * @property string $location
 * @property string $mobilenocountrycode
 * @property string $phone2
 * @property string $email
 * @property int $active
 * @property int $createduser
 * @property int $modifieduser
 * @property string $phone3_countrycode
 * @property string $phone3_areacode
 * @property string $phone3
 * @property string $url
 * @property string $institutionlogo
 * @property string $presidentname
 * @property string $presidentmobile
 * @property string $presidentmobile_countrycode
 * @property string $secretaryname
 * @property string $secretarymobile
 * @property string $secretarymobile_countrycode
 * @property string $meetingvenue
 * @property string $meetingday
 * @property string $meetingtime
 * @property string $remarks
 *
 * @property Country $country
 * @property Usercredentials $createduser0
 * @property Institution $institution
 * @property Usercredentials $modifieduser0
 */
class ExtendedAffiliatedinstitution extends Affiliatedinstitution
{
	
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return \yii\helpers\Arrayhelper::merge(parent::rules(), [
            [['CountryID'], 'required', 'when' => function($model){
                return ($model->institution->isrotary) == 0 ? true :false;
            }],
            [['name'], 'required'],
            [['name'], 'filter', 'filter'=>'trim'],
            ['email','email'],
            [['pin'], 'match', 'pattern' => '/^([0-9]+-)*[0-9]+$/', 
                'message'=>'Pin invalid'],
            [['institutionlogo'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg,  jpeg', 'skipOnEmpty' =>true,
                'maxSize' => \Yii::$app->params['fileUploadSize']['imageFileSize'],
                'tooBig' => \Yii::$app->params['fileUploadSize']['imageSizeMsg']],
        ]);
    }
      /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(ExtendedInstitution::className(), ['id' => 'institutionid']);
    }
    /**
     * To get the details of affiliated institutions
     * @param unknown $institutionId
     * @return boolean
     */
    public static function getAffiliatedInstitutionData($institutionId)
    {
      
    	try {

    		    $affiliatedInstitutionDetails = Yii::$app->db->createCommand(
        				"CALL getaffliatedinstitution(:institutionname,:institutionid)")
        				->bindValue(':institutionname','' )
        				->bindValue(':institutionid',$institutionId )
        				->queryAll();

    				return $affiliatedInstitutionDetails;
    
    	} catch (\Exception $e) {

    		return false;
    	}
    }
    
}
