<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Institutioncontactfilters;

/**
 * This is the model class for table "institutioncontactfilters".
 *
 * @property int $institutioncontactfilterid
 * @property int $institutionid
 * @property int $contactfilterid
 *
 * @property Contactfilters $contactfilter
 * @property Institution $institution
 */
class ExtendedInstitutioncontactfilters extends Institutioncontactfilters
{
	const CONTACT_FILTER_FAMILYUNIT = 0;
	const CONTACT_FILTER_TAGSEARCH = 1;
    const CONTACT_FILTER_BLOODGROUP = 2;
    const CONTACT_FILTER_BATCH = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institutioncontactfilters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'contactfilterid'], 'required'],
            [['institutionid', 'contactfilterid'], 'integer'],
            [['contactfilterid'], 'exist', 'skipOnError' => true, 'targetClass' => Contactfilters::className(), 'targetAttribute' => ['contactfilterid' => 'contactfilterid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institutioncontactfilterid' => 'Institutioncontactfilterid',
            'institutionid' => 'Institutionid',
            'contactfilterid' => 'Contactfilterid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactfilter()
    {
        return $this->hasOne(Contactfilters::className(), ['contactfilterid' => 'contactfilterid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
    /**
     * To get the contact filters
     * of an institution
     * @param unknown $institutionId
     * @return boolean
     */
    public static function getInstitutionContactFilters($institutionId)
    {
    	try {
    		$contactFilters = Yii::$app->db->createCommand(
    				"CALL institution_contact_filters(:institutionid)")
    				->bindValue(':institutionid' , $institutionId )
    				->queryAll();
    		return $contactFilters;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
