<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "rolecategory".
 *
 * @property int $RoleCategoryID
 * @property string $Description
 * @property int $InstitutionID
 * @property int $RoleGroupID
 *
 * @property Role[] $roles
 * @property Institution $institution
 * @property Rolegroup $roleGroup
 */
class RoleCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rolecategory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description', 'InstitutionID', 'RoleGroupID'], 'required'],
            [['InstitutionID', 'RoleGroupID'], 'integer'],
            [['Description'], 'string', 'max' => 100],
            [['InstitutionID'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['InstitutionID' => 'id']],
            [['RoleGroupID'], 'exist', 'skipOnError' => true, 'targetClass' => Rolegroup::className(), 'targetAttribute' => ['RoleGroupID' => 'RoleGroupID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RoleCategoryID' => 'Role Category ID',
            'Description' => 'Description',
            'InstitutionID' => 'Institution ID',
            'RoleGroupID' => 'Role Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['rolecategoryid' => 'RoleCategoryID']);
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
    public function getRoleGroup()
    {
        return $this->hasOne(Rolegroup::className(), ['RoleGroupID' => 'RoleGroupID']);
    }
}
