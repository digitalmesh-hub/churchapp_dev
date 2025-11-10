<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\UserProfile;

/**
 * This is the extended model class for table "userprofile".
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
class ExtendedUserProfile extends UserProfile
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'firstname','lastname'], 'required'],
            [['userid', 'usertype', 'institutionid'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'mobilenumber'], 'string', 'max' => 50],
            [['firstname', 'middlename', 'lastname', 'mobilenumber'],'trim'],
            [['emailid'], 'string', 'max' => 100],
            [['emailid'],'email'],
            [['photo'], 'string', 'max' => 1000],
            ['isactive', 'integer'],
            ['isactive', 'default' , 'value' => 1],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUserCredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(ExtendedUserCredentials::className(), ['id' => 'userid']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(ExtendedInstitution::className(), ['id' => 'institutionid']);
    }
       /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'First Name',
            'middlename' => 'Middle Name',
            'lastname' => 'Last Name',
            'emailid' => 'Email Id',
            'mobilenumber' => 'Mobile number',
            'photo' => 'Photo',
            'userid' => 'Userid',
            'usertype' => 'Usertype',
            'isactive' => 'Is Active',
            'institutionid' => 'Institutionid',
        ];
    }
}
