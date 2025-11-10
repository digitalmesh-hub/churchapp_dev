<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "profileupdatenotification".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property string $createddatetime
 *
 * @property Member $member
 * @property Usercredentials $user
 */
class Profileupdatenotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profileupdatenotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'memberid', 'createddatetime'], 'required'],
            [['userid', 'memberid'], 'integer'],
            [['createddatetime'], 'safe'],
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
            'createddatetime' => 'Createddatetime',
        ];
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
