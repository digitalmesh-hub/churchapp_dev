<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institutionfeedbacktype".
 *
 * @property int $institutionfeedbacktypeid
 * @property int $feedbacktypeid
 * @property int $institutionid
 * @property int $active
 * @property int $order
 *
 * @property Feedbacktype $feedbacktype
 * @property Institution $institution
 */
class Institutionfeedbacktype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institutionfeedbacktype';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedbacktypeid', 'institutionid'], 'required'],
            [['feedbacktypeid', 'institutionid', 'order'], 'integer'],
            [['active'], 'integer'],
            [['feedbacktypeid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedbacktype::className(), 'targetAttribute' => ['feedbacktypeid' => 'feedbacktypeid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institutionfeedbacktypeid' => 'Institutionfeedbacktypeid',
            'feedbacktypeid' => 'Feedbacktypeid',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
            'order' => 'Order',
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
}
