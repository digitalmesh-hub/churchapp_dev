<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "local_authentication_registered_users".
 *
 * @property int $id
 * @property int $userid
 * @property string $deviceidentifier
 * @property string $createdon
 * 
 */
class LocalAuthenticationRegisteredUser extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'local_authentication_registered_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['userid'], 'integer', 'required'],
            [['deviceidentifier'], 'string', 'required', 'max' => 75],
            [['createdon'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'userid' => 'userId',
            'deviceidentifier' => 'deviceIdentifier',
            'createdon' => 'createdOn',
        ];
    }
}
