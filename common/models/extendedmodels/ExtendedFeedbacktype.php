<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Feedbacktype;

/**
 * This is the model class for table "feedbacktype".
 *
 * @property int $feedbacktypeid
 * @property string $description
 *
 * @property Feedback[] $feedbacks
 * @property Institutionfeedbacktype[] $institutionfeedbacktypes
 */
class ExtendedFeedbacktype extends Feedbacktype
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
    /**
     * To get feedback description
     * @param unknown $feedbacktypeid
     * @return \yii\db\false|boolean
     */
    public static function getFeedbackDescription($feedbacktypeid)
    {
        try {
            $getFeedbackDescription = Yii::$app->db->createCommand('select description from feedbacktype where feedbacktypeid=:feedbacktypeid ')
                                        ->bindValue(':feedbacktypeid',$feedbacktypeid)
                                        ->queryOne();
            return $getFeedbackDescription;
    
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * To get the feedback order
     * @param unknown $institutionid
     * @return \yii\db\false|boolean
     */
     public static function getFeedbackOrder($institutionid)
    {
        try {
            $getFeedbackOrder = Yii::$app->db->createCommand('select max(`order`) as ordervalue from institutionfeedbacktype where institutionid=:institutionid ')
                                        ->bindValue(':institutionid',$institutionid)
                                        ->queryOne();
            return $getFeedbackOrder;
    
        } catch (Exception $e) {
            return false;
        }
    }
}