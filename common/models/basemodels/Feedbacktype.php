<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "feedbacktype".
 *
 * @property int $feedbacktypeid
 * @property string $description
 *
 * @property Feedback[] $feedbacks
 * @property Institutionfeedbacktype[] $institutionfeedbacktypes
 */
class Feedbacktype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbacktype';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feedbacktypeid' => 'Feedbacktypeid',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedback::className(), ['feedbacktypeid' => 'feedbacktypeid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionfeedbacktypes()
    {
        return $this->hasMany(Institutionfeedbacktype::className(), ['feedbacktypeid' => 'feedbacktypeid']);
    }
}
