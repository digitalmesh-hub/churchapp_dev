<?php

namespace common\models\extendedmodels;
use Yii;
use common\models\basemodels\Institutionfeedbacktype;
use common\models\basemodels\Feedbacktype;
use common\models\basemodels\Institution;



 
class ExtendedInstitutionfeedbacktype extends Institutionfeedbacktype
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

 
  
}
