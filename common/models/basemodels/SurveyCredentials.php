<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "surveycredentials".
 *
 * @property int $surveycredentialsid
 * @property string $username
 * @property string $password
 * @property string $token
 * @property int $institutionid
 * @property string $createddatetime
 * @property int $createdby
 * @property string $modifieddatetime
 * @property int $modifiedby
 *
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Usercredentials $createdby0
 */
class SurveyCredentials extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'surveycredentials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'institutionid', 'createddatetime', 'createdby'], 'required'],
            [['institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['username', 'password'], 'string', 'max' => 100],
            [['token'], 'string', 'max' => 38],
            [['institutionid'], 'unique'],
            [['username'], 'unique'],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'surveycredentialsid' => 'Surveycredentialsid',
            'username' => 'Username',
            'password' => 'Password',
            'token' => 'Token',
            'institutionid' => 'Institutionid',
            'createddatetime' => 'Createddatetime',
            'createdby' => 'Createdby',
            'modifieddatetime' => 'Modifieddatetime',
            'modifiedby' => 'Modifiedby',
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
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
    }
}
