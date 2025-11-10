<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\DeleteUsercredentials;

/**
 * This is the model class for table "delete_usercredentials".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $emailid
 * @property string $password
 * @property int $initiallogin
 * @property string $usertype
 * @property string $lastlogin
 * @property string $mobileno
 * @property string $membershipno
 * @property string $userpin
 *
 * @property Institution $institution
 */
class ExtendedDeleteUsercredentials extends DeleteUsercredentials
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delete_usercredentials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid'], 'integer'],
            [['lastlogin'], 'safe'],
            [['emailid'], 'string', 'max' => 150],
            [['password'], 'string', 'max' => 15],
            [['initiallogin'], 'string', 'max' => 4],
            [['usertype'], 'string', 'max' => 1],
            [['mobileno'], 'string', 'max' => 45],
            [['membershipno'], 'string', 'max' => 100],
            [['userpin'], 'string', 'max' => 10],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institutionid' => 'Institutionid',
            'emailid' => 'Emailid',
            'password' => 'Password',
            'initiallogin' => 'Initiallogin',
            'usertype' => 'Usertype',
            'lastlogin' => 'Lastlogin',
            'mobileno' => 'Mobileno',
            'membershipno' => 'Membershipno',
            'userpin' => 'Userpin',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
