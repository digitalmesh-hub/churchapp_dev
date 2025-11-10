<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "delete_usermember".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property int $institutionid
 * @property string $usertype
 *
 * @property Member $member
 * @property Usercredentials $user
 * @property Institution $institution
 */
class DeleteUsermember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delete_usermember';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'memberid', 'institutionid'], 'required'],
            [['userid', 'memberid', 'institutionid'], 'integer'],
            [['usertype'], 'string', 'max' => 1],
            
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

   
}
