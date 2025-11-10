<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "usertoken".
 *
 * @property string $tokenid
 * @property int $userid
 * @property string $createddatetime
 * @property string $useridentity
 * @property string $lastactivedatetime
 *
 * @property Usercredentials $user
 */
class UserToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usertoken';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tokenid', 'userid'], 'required'],
            [['userid'], 'integer'],
            [['createddatetime', 'lastactivedatetime'], 'safe'],
            [['tokenid'], 'string', 'max' => 255],
            [['device_id'], 'string', 'max' => 1000],
            [['useridentity'], 'string', 'max' => 45],
            [['tokenid'], 'unique'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tokenid' => 'Tokenid',
            'userid' => 'Userid',
            'createddatetime' => 'Createddatetime',
            'useridentity' => 'Useridentity',
            'lastactivedatetime' => 'Lastactivedatetime',
            'device_id' => 'Device Id'
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
