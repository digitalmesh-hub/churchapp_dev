<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "usermember".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property int $institutionid
 * @property string $usertype
 *
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $user
 */
class UserMember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usermember';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'],'string','max' => 100],
            [['userid', 'memberid', 'institutionid'], 'required'],
            [['userid', 'memberid', 'institutionid'], 'integer'],
            [['usertype'], 'string', 'max' => 1],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'memberid' => 'Memberid',
            'institutionid' => 'Institutionid',
            'usertype' => 'Usertype',
        ];
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
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
