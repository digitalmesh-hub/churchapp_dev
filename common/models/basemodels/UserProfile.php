<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "userprofile".
 *
 * @property int $id
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $emailid
 * @property string $mobilenumber
 * @property string $photo
 * @property int $userid
 * @property int $usertype
 * @property int $isactive
 * @property int $institutionid
 *
 * @property Usercredentials $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userprofile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid'], 'required'],
            [['userid', 'usertype', 'institutionid'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'mobilenumber'], 'string', 'max' => 50],
            [['emailid'], 'string', 'max' => 100],
            [['photo'], 'string', 'max' => 1000],
            [['isactive'], 'string', 'max' => 4],
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
            'firstname' => 'Firstname',
            'middlename' => 'Middlename',
            'lastname' => 'Lastname',
            'emailid' => 'Emailid',
            'mobilenumber' => 'Mobilenumber',
            'photo' => 'Photo',
            'userid' => 'Userid',
            'usertype' => 'Usertype',
            'isactive' => 'Isactive',
            'institutionid' => 'Institutionid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
