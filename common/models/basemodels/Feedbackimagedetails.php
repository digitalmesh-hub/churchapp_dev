<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "feedbackimagedetails".
 *
 * @property int $id
 * @property int $feedbackid
 * @property string $feedbackimage
 * @property string $createddate
 *
 * @property Feedback $feedback
 */
class Feedbackimagedetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbackimagedetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbackid', 'feedbackimage', 'createddate'], 'required'],
            [['feedbackid'], 'integer'],
            [['createddate'], 'safe'],
            [['feedbackimage'], 'string', 'max' => 150],
            [['feedbackid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['feedbackid' => 'feedbackid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'feedbackid' => 'Feedbackid',
            'feedbackimage' => 'Feedbackimage',
            'createddate' => 'Createddate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['feedbackid' => 'feedbackid']);
    }
}
