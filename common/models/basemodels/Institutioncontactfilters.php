<?php

namespace common\models\basemodels;

use Yii;

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
class Institutioncontactfilters extends \yii\db\ActiveRecord
{
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
}
