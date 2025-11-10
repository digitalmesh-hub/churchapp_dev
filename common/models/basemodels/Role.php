<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property string $roleid
 * @property string $roledescription
 * @property int $rolecategoryid
 * @property int $institutionid
 *
 * @property Memberrole[] $memberroles
 * @property Institution $institution
 * @property Rolecategory $rolecategory
 * @property Roleprivilege[] $roleprivileges
 * @property Userrole[] $userroles
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roleid', 'roledescription', 'rolecategoryid', 'institutionid'], 'required'],
            [['rolecategoryid', 'institutionid'], 'integer'],
            [['roleid'], 'string', 'max' => 38],
            [['roledescription'], 'string', 'max' => 75],
            [['roleid'], 'unique'],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['rolecategoryid'], 'exist', 'skipOnError' => true, 'targetClass' => Rolecategory::className(), 'targetAttribute' => ['rolecategoryid' => 'RoleCategoryID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'roleid' => 'Roleid',
            'roledescription' => 'Roledescription',
            'rolecategoryid' => 'Rolecategoryid',
            'institutionid' => 'Institutionid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberroles()
    {
        return $this->hasMany(Memberrole::className(), ['RoleID' => 'roleid']);
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
    public function getRolecategory()
    {
        return $this->hasOne(Rolecategory::className(), ['RoleCategoryID' => 'rolecategoryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleprivileges()
    {
        return $this->hasMany(Roleprivilege::className(), ['RoleID' => 'roleid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserroles()
    {
        return $this->hasMany(Userrole::className(), ['roleid' => 'roleid']);
    }
}
