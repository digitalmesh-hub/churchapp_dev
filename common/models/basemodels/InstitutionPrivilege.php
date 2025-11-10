<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institutionprivilege".
 *
 * @property int $InstitutionPrivilegeID
 * @property int $InstitutionID
 * @property string $PrivilegeID
 *
 * @property Institution $institution
 * @property Privilege $privilege
 */
class InstitutionPrivilege extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institutionprivilege';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['InstitutionID', 'PrivilegeID'], 'required'],
            [['InstitutionID'], 'integer'],
            [['PrivilegeID'], 'string', 'max' => 38],
            [['InstitutionID'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['InstitutionID' => 'id']],
            [['PrivilegeID'], 'exist', 'skipOnError' => true, 'targetClass' => Privilege::className(), 'targetAttribute' => ['PrivilegeID' => 'PrivilegeID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'InstitutionPrivilegeID' => 'Institution Privilege ID',
            'InstitutionID' => 'Institution ID',
            'PrivilegeID' => 'Privilege ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'InstitutionID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivilege()
    {
        return $this->hasOne(Privilege::className(), ['PrivilegeID' => 'PrivilegeID']);
    }
}
