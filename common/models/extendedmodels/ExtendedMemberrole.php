<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Memberrole;

/**
 * This is the model class for table "memberrole".
 *
 * @property int $MemberRoleID
 * @property string $RoleID
 * @property int $MemberID
 * @property string $MemberType
 *
 * @property Member $member
 * @property Role $role
 */
class ExtendedMemberrole extends Memberrole
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'memberrole';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RoleID', 'MemberID', 'MemberType'], 'required'],
            [['MemberID'], 'integer'],
            [['RoleID'], 'string', 'max' => 38],
            [['MemberType'], 'string', 'max' => 1],
            [['MemberID'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['MemberID' => 'memberid']],
            [['RoleID'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['RoleID' => 'roleid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'MemberRoleID' => 'Member Role ID',
            'RoleID' => 'Role ID',
            'MemberID' => 'Member ID',
            'MemberType' => 'Member Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'MemberID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['roleid' => 'RoleID']);
    }
}
