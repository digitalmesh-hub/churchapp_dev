<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "roleprivilege".
 *
 * @property int $RolePrivilegeID
 * @property string $RoleID
 * @property string $PrivilegeID
 * @property int $InstitutionID
 *
 * @property Institution $institution
 * @property Privilege $privilege
 * @property Role $role
 */
class Roleprivilege extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roleprivilege';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RoleID', 'PrivilegeID', 'InstitutionID'], 'required'],
            [['InstitutionID'], 'integer'],
            [['RoleID', 'PrivilegeID'], 'string', 'max' => 38],
            [['InstitutionID'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['InstitutionID' => 'id']],
            [['PrivilegeID'], 'exist', 'skipOnError' => true, 'targetClass' => Privilege::className(), 'targetAttribute' => ['PrivilegeID' => 'PrivilegeID']],
            [['RoleID'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['RoleID' => 'roleid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RolePrivilegeID' => 'Role Privilege ID',
            'RoleID' => 'Role ID',
            'PrivilegeID' => 'Privilege ID',
            'InstitutionID' => 'Institution ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['roleid' => 'RoleID']);
    }
}
