<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property int $feedbackid
 * @property int $feedbacktypeid
 * @property int $userid
 * @property string $description
 * @property string $createddatetime
 * @property int $isresponded
 * @property int $feedbackrating
 * @property int $institutionid
 *
 * @property Feedbacktype $feedbacktype
 * @property Institution $institution
 * @property Usercredentials $user
 * @property Feedbackimagedetails[] $feedbackimagedetails
 * @property Feedbacknotification[] $feedbacknotifications
 * @property Feedbacknotificationsent[] $feedbacknotificationsents
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbacktypeid', 'userid', 'institutionid'], 'required'],
            [['feedbacktypeid', 'userid', 'feedbackrating', 'institutionid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['description'], 'string', 'max' => 250],
            [['isresponded'], 'string', 'max' => 4],
            [['feedbacktypeid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedbacktype::className(), 'targetAttribute' => ['feedbacktypeid' => 'feedbacktypeid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feedbackid' => 'Feedbackid',
            'feedbacktypeid' => 'Feedbacktypeid',
            'userid' => 'Userid',
            'description' => 'Description',
            'createddatetime' => 'Createddatetime',
            'isresponded' => 'Isresponded',
            'feedbackrating' => 'Feedbackrating',
            'institutionid' => 'Institutionid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacktype()
    {
        return $this->hasOne(Feedbacktype::className(), ['feedbacktypeid' => 'feedbacktypeid']);
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
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbackimagedetails()
    {
        return $this->hasMany(Feedbackimagedetails::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotifications()
    {
        return $this->hasMany(Feedbacknotification::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotificationsents()
    {
        return $this->hasMany(Feedbacknotificationsent::className(), ['feedbackid' => 'feedbackid']);
    }
}
