<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "userrole".
 *
 * @property int $userroleid
 * @property int $userid
 * @property string $roleid
 *
 * @property Role $role
 * @property Usercredentials $user
 */
class Userrole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userrole';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'roleid'], 'required'],
            [['userid'], 'integer'],
            [['roleid'], 'string', 'max' => 38],
            [['roleid'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['roleid' => 'roleid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userroleid' => 'Userroleid',
            'userid' => 'Userid',
            'roleid' => 'Roleid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['roleid' => 'roleid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
