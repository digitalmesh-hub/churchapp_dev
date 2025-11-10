<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\InstitutionPaymentGateways;

/**
 * This is the model class for table "institution_payment_gateways".
 *
 * @property int $id
 * @property string $guid
 * @property int $institutionId
 * @property int $status
 * @property string $type
 * @property string $source
 * @property string $credentials
 * @property string $PaymentUrl
 * @property string $ReturnUrl
 * @property string $created
 * @property string $updated
 *
 * @property Institution $institution
 */
class ExtendedInstitutionPaymentGateways extends InstitutionPaymentGateways
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institution_payment_gateways';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['guid', 'institutionId', 'credentials', 'PaymentUrl', 'ReturnUrl'], 'required'],
            [['institutionId'], 'integer'],
            [['credentials'], 'string'],
            [['created', 'updated'], 'safe'],
            [['guid', 'source'], 'string', 'max' => 45],
            [['status'], 'string', 'max' => 1],
            [['type'], 'string', 'max' => 5],
            [['PaymentUrl', 'ReturnUrl'], 'string', 'max' => 255],
            [['institutionId'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'guid' => 'Guid',
            'institutionId' => 'Institution ID',
            'status' => 'Status',
            'type' => 'Type',
            'source' => 'Source',
            'credentials' => 'Credentials',
            'PaymentUrl' => 'Payment Url',
            'ReturnUrl' => 'Return Url',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionId']);
    }
    /**
     * To get the merchant details
     * @param $institutionId string
     * @param $type string
     */
    public static function getMerchantData($institutionId,$type)
    {
    	
    	try {
    		$merchantDetails = Yii::$app->db->createCommand('SELECT guid,PaymentUrl from institution_payment_gateways 
    				where institutionId = :institutionId and type = :type ')
    		->bindValue(':institutionId', $institutionId)
    		->bindValue(':type', $type)
    		->queryOne();
    		return $merchantDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }

    /**
     * To get the payment gateway details
     * @param $institutionId string
     * @param $type string
     */
    public static function getPaymentGatewayData($institutionId,$type)
    {
    	
    	try {
    		$merchantDetails = Yii::$app->db->createCommand('SELECT guid,paymentEnquiryUrl, credentials from institution_payment_gateways 
    				where institutionId = :institutionId and type = :type ')
    		->bindValue(':institutionId', $institutionId)
    		->bindValue(':type', $type)
    		->queryOne();
    		return $merchantDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }
}
