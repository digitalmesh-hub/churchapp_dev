<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Familyunit;
use common\models\extendedmodels\ExtendedInstitution;

/**
 * This is the model class for table "familyunit".
 *
 * @property int $familyunitid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 *
 * @property Events[] $events
 * @property Institution $institution
 * @property Member[] $members
 */
class ExtendedFamilyunit extends Familyunit
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'familyunit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'institutionid'], 'required'],
            [['institutionid'], 'integer'],
            [['description'], 'string', 'max' => 45],
            [['active'], 'integer', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'familyunitid' => 'Familyunitid',
            'description' => 'Family Unit',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::className(), ['familyunitid' => 'familyunitid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['familyunitid' => 'familyunitid']);
    }
    
    /**
     * To get the details of all active 
     * family units
     * @param unknown $institutionId
     */
    public function getActiveFamilyUnits( $institutionId){
    	
    	$sql = "SELECT * FROM familyunit WHERE institutionid=:institutionId AND active=1 order by description asc;";
    	
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institutionId )
    	
    	->queryAll();
    	return $result;
    			
    		
    }
}
