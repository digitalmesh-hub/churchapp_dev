<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Zone;
use common\models\extendedmodels\ExtendedInstitution;

/**
 * This is the model class for table "zone".
 *
 * @property int $zoneid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 *
 * @property Events[] $events
 * @property Institution $institution
 * @property Member[] $members
 */
class ExtendedZone extends Zone
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zone';
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
            'zoneid' => 'Zoneid',
            'description' => 'Zone',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::className(), ['zoneid' => 'zoneid']);
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
        return $this->hasMany(Member::className(), ['zoneid' => 'zoneid']);
    }
    
    /**
     * To get the details of all active 
     * zones
     * @param unknown $institutionId
     */
    public function getActiveZones( $institutionId){
    	
    	$sql = "SELECT * FROM zone WHERE institutionid=:institutionId AND active=1 order by description asc;";
    	
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institutionId )
    	
    	->queryAll();
    	return $result;
    			
    		
    }
}
