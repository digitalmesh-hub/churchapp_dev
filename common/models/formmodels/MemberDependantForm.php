<?php
namespace common\models\formmodels;

use Yii;
use yii\base\Model;
use common\models\basemodels\CustomRoleModel;
use common\models\extendedmodels\ExtendedTitle;
use yii\helpers\ArrayHelper;

class MemberDependantForm extends Model
{
    public $dependant_title;
    public $dependant_name;
    public $dependant_relation;
    public $dependant_dob;
    public $dependant_martial_status;
    public $dependant_foto;
    public $dependant_spouse_title;
    public $dependant_spouse_name;
    public $dependant_spouse_dob;
    public $dependant_wedding_anniversary;
    public $dependant_spouse_foto;
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [   
                    'dependant_title', 
                    'dependant_name'
                ],
            'required'
            ],
            [
                [   
                    'dependant_spouse_title', 
                    'dependant_spouse_name',
                ],
            'required',
            'when' => function($model) {
                return ($model->dependant_martial_status == 2) ? true :false;
            },
            'whenClient' => "function (attribute, value) {
                    return $('#memberdependantform-dependant_martial_status').val() == 2;
            }"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dependant_title'=>'test',
            'dependant_name'=>'test',
            'dependant_relation'=>'test',
            'dependant_dob'=>'test',
            'dependant_martial_status'=>'test',
            'dependant_foto'=>'test',
            'dependant_spouse_title'=>'test',
            'dependant_spouse_name'=>'test',
            'dependant_spouse_dob'=>'test',
            'dependant_wedding_anniversary'=>'test',
            'dependant_spouse_foto'=>'test'  
        ];
    }
    public function getTitles() 
    {
        return ArrayHelper::map(
            ExtendedTitle::find()
            ->select(['TitleId', 'Description'])
            ->where(['institutionid' => yii::$app->user->identity->institutionid])
            ->andWhere(['active' => 1])
            ->orderBy('Description')->all(),
            'TitleId',
            'Description'
        );
    }
}
